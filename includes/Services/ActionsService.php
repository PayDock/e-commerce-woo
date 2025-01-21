<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

namespace PowerBoard\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Controllers\Admin\WidgetController;
use PowerBoard\Controllers\Webhooks\PaymentController;
use PowerBoard\Enums\SettingsSectionEnum;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Util\MasterWidgetBlock;

class ActionsService extends AbstractSingleton {
	protected const PROCESS_OPTIONS_FUNCTION    = 'process_admin_options';
	protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
	protected const SECTION_HOOK                = 'woocommerce_get_sections';
	protected static $instance                  = null;

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function __construct() {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action(
			'before_woocommerce_init',
			function () {
				$this->addCompatibilityWithWooCommerce();
				$this->add_payment_method_to_checkout();
				$this->addSettingsActions();
				$this->addOrderActions();
			}
		);
	}

	protected function addCompatibilityWithWooCommerce(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', POWER_BOARD_PLUGIN_FILE );
		}
	}

	/**
	 * Add new payment method on checkout page
	 * Uses a function (add_action) from WordPress
	 */
	protected function add_payment_method_to_checkout() {
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
	}

	/**
	 * Uses a function (add_action) from WordPress
	 */
	protected function addSettingsActions(): void {
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
	protected function addOrderActions() {
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
		add_action( 'woocommerce_order_refunded', [ $payment_controller, 'after_refund_process' ], 10, 1 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_api_power-board-webhook', [ $payment_controller, 'webhook' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wc_ajax_power-board-create-charge-intent', [ $widget_controller, 'create_checkout_intent' ] );
	}
}
