<?php

namespace PowerBoard\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Controllers\Admin\WidgetController;
use PowerBoard\Controllers\Webhooks\PaymentController;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Util\MasterWidgetBlock;

class ActionsService extends AbstractSingleton {
	protected const PROCESS_OPTIONS_FUNCTION = 'process_admin_options';
	protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
	protected const SECTION_HOOK = 'woocommerce_get_sections';
	protected static $instance = null;

	protected function __construct() {
		add_action( 'before_woocommerce_init', function () {
			$this->addCompatibilityWithWooCommerce();
			$this->addPaymentMethodToChekout();
			$this->addSettingsActions();
			$this->addOrderActions();
		} );

		add_filter( 'gettext', array( $this, 'cba_refund_msg' ), 20, 3 );
	}

	public function cba_refund_msg( $translated_text, $text, $domain ) {

		if ( 'woocommerce' === $domain ) {

			$message_to_change = strpos( $translated_text, 'Invalid refund amount' );

			if ( $message_to_change !== false ) {
				$translated_text = 'The requested refund amount exceeds the available charge/Transaction amount';
			}

		}

		return $translated_text;

	}

	protected function addCompatibilityWithWooCommerce(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', POWER_BOARD_PLUGIN_FILE );
		}
	}

	/**
	 * Add new payment method on chekout page
	 */
	protected function addPaymentMethodToChekout() {
		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			return;
		}

		add_action( 'before_woocommerce_init', function () {
			FeaturesUtil::declare_compatibility(
				'cart_checkout_blocks',
				POWER_BOARD_PLUGIN_FILE,
				true
			);
		} );

		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new MasterWidgetBlock() );
			}
		);
	}

	protected function addSettingsActions(): void {
		foreach ( SettingsTabs::cases() as $settingsTab ) {
			add_action( self::PROCESS_OPTIONS_HOOK_PREFIX . $settingsTab->value, [
				$settingsTab->getSettingService(),
				self::PROCESS_OPTIONS_FUNCTION,
			] );
			add_action( self::SECTION_HOOK, function ( $systemTabs ) use ( $settingsTab ) {
				return array_merge( $systemTabs, [
					$settingsTab->value => '',
				] );
			} );
		}
	}

	protected function addOrderActions() {
		$orderService      = new OrderService();
		$paymentController = new PaymentController();
		$widgetController = new WidgetController();

		add_action( 'woocommerce_order_item_add_action_buttons', [ $orderService, 'init_power_board_order_buttons'], 10,
			2 );
		add_action( 'woocommerce_order_status_changed', [ $orderService, 'status_change_verification' ], 20, 4 );
		add_action( 'woocommerce_admin_order_totals_after_total',
			[ $orderService, 'information_about_partial_captured' ] );
		add_action( 'admin_notices', [ $orderService, 'display_status_change_error' ] );
		add_action( 'wp_ajax_power-board-capture-charge', [ $paymentController, 'capture_payment' ] );
		add_action( 'wp_ajax_power-board-cancel-authorised', [ $paymentController, 'cancel_authorised' ] );
		add_action( 'woocommerce_create_refund', [ $paymentController, 'refund_process' ], 10, 2 );
		add_action( 'woocommerce_order_refunded', [ $paymentController, 'after_refund_process' ], 10, 2 );
		add_action( 'woocommerce_api_power-board-webhook', [ $paymentController, 'webhook' ] );
		add_action( 'wc_ajax_power-board-create-charge-intent', [ $widgetController, 'create_checkout_intent'], 10, 1 );
    }
}
