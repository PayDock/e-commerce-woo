<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

declare( strict_types=1 );

namespace PowerBoard\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use PowerBoard\Controllers\Admin\WidgetController;
use PowerBoard\Controllers\Webhooks\PaymentController;
use PowerBoard\Enums\SettingsSectionEnum;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Util\MasterWidgetBlock;

class ActionsService {
	protected static ?ActionsService $instance = null;
	protected string $last_shipping_id         = '';

	protected const PROCESS_OPTIONS_FUNCTION    = 'process_admin_options';
	protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
	protected const SECTION_HOOK                = 'woocommerce_get_sections';

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function __construct() {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			'before_woocommerce_init',
			function () {
				$this->add_compatibility_with_woocommerce();
				$this->add_payment_method_to_checkout();
				$this->add_settings_actions();
				$this->add_order_actions();
			}
		);
	}

	public static function get_instance(): ActionsService {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function add_compatibility_with_woocommerce(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', POWER_BOARD_PLUGIN_FILE );
		}
	}

	/**
	 * Add new payment method on checkout page
	 * Uses a function (add_action) from WordPress
	 */
	protected function add_payment_method_to_checkout(): void {
		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			return;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			'before_woocommerce_init',
			function () {
				FeaturesUtil::declare_compatibility(
					'cart_checkout_blocks',
					POWER_BOARD_PLUGIN_FILE
				);
			}
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new MasterWidgetBlock() );
			}
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_after_cart_item_quantity_update', [ $this, 'cart_item_quantity_changed' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_add_to_cart', [ $this, 'add_to_cart' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_cart_item_removed', [ $this, 'cart_item_removed' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_update_shipping', [ $this, 'order_update_shipping' ] );
	}

	public function cart_item_quantity_changed() {
		$this->calculate_totals_and_save_cookie();
	}

	public function add_to_cart() {
		$this->calculate_totals_and_save_cookie();
	}

	public function cart_item_removed() {
		$this->calculate_totals_and_save_cookie();
	}

	public function order_update_shipping() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$current_shipping = WC()->session->get( 'chosen_shipping_methods' )[0];
		if ( $current_shipping !== $this->last_shipping_id ) {
			$this->last_shipping_id = $current_shipping;
			$expiry_time            = time() + 3600;
			setcookie( 'selected_shipping', $current_shipping, $expiry_time, '/' );
		}
	}

	private function calculate_totals_and_save_cookie() {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_checkout() ) {
			return;
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;
		$cart->calculate_totals();
		$cart_total  = (string) $cart->get_total( false );
		$expiry_time = time() + 3600;
		setcookie( 'cart_total', $cart_total, $expiry_time, '/' );
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function add_settings_actions(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			self::PROCESS_OPTIONS_HOOK_PREFIX . SettingsSectionEnum::WIDGET_CONFIGURATION,
			[
				new WidgetConfigurationSettingService(),
				self::PROCESS_OPTIONS_FUNCTION,
			]
		);
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			self::SECTION_HOOK,
			function ( $system_tabs ) {
				return array_merge( $system_tabs, [ SettingsSectionEnum::WIDGET_CONFIGURATION => '' ] );
			}
		);
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function add_order_actions(): void {
		$order_service      = new OrderService();
		$payment_controller = new PaymentController();
		$widget_controller  = new WidgetController();

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_item_add_action_buttons', [ $order_service, 'init_power_board_order_buttons' ], 10, 2 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_status_changed', [ $order_service, 'status_change_verification' ], 20, 4 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_create_refund', [ $payment_controller, 'refund_process' ], 10, 2 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_order_refunded', [ $payment_controller, 'after_refund_process' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_power-board-create-charge-intent', [ $widget_controller, 'create_checkout_intent' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_nopriv_power-board-create-charge-intent', [ $widget_controller, 'create_checkout_intent' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'admin_init', [ $order_service, 'remove_bulk_action_message' ] );
	}
}
