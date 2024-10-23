<?php

namespace WooPlugin\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use WooPlugin\Abstracts\AbstractSingleton;
use WooPlugin\Controllers\Admin\WidgetController;
use WooPlugin\Controllers\Webhooks\PaymentController;
use WooPlugin\Enums\SettingsTabs;
use WooPlugin\Services\Checkout\BankAccountPaymentService;
use WooPlugin\Util\AfterpayAPMsBlock;
use WooPlugin\Util\AfterpayWalletBlock;
use WooPlugin\Util\ApplePayWalletBlock;
use WooPlugin\Util\BankAccountBlock;
use WooPlugin\Util\GooglePayWalletBlock;
use WooPlugin\Util\PayPalWalletBlock;
use WooPlugin\Util\PluginGatewayBlocks;
use WooPlugin\Util\ZipAPMsBlock;

class ActionsService extends AbstractSingleton {
	protected const PROCESS_OPTIONS_FUNCTION = 'process_admin_options';
	protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
	protected const SECTION_HOOK = 'woocommerce_get_sections';
	protected static $instance = null;

	protected function __construct() {
		add_action( 'init', function () {
			if ( ! session_id() ) {
				session_start();
			}
		} );

		add_action( 'before_woocommerce_init', function () {
			$this->addCompatibilityWithWooCommerce();
			$this->addPaymentActions();
			$this->addPaymentMethodToChekout();
			$this->addSettingsActions();
			$this->addEndpoints();
			$this->addOrderActions();
		} );

		add_filter( 'gettext', array( $this, 'custom_refund_msg' ), 20, 3 );
	}

	public function custom_refund_msg( $translated_text, $text, $domain ) {

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
			FeaturesUtil::declare_compatibility( 'custom_order_tables', PLUGIN_FILE );
		}
	}

	protected function addPaymentActions() {
		$payments = [
			PLUGIN_PREFIX . '_bank_account_gateway' => new BankAccountPaymentService(),
		];
		foreach ( $payments as $paymentKey => $payment ) {
			add_action(
				'woocommerce_update_options_payment_gateways_' . $paymentKey,
				[ $payment, 'process_admin_options' ]
			);
			add_action( 'woocommerce_scheduled_subscription_payment_' . $paymentKey, [
				$payment,
				'process_subscription_payment',
			], 10, 2 );
			add_action( 'woocommerce_after_checkout_billing_form', [
				$payment,
				'woocommerce_before_checkout_form',
			], 10, 1 );
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
				PLUGIN_FILE,
				true
			);
		} );

		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new PluginGatewayBlocks() );
				$payment_method_registry->register( new BankAccountBlock() );
				$payment_method_registry->register( new ApplePayWalletBlock() );
				$payment_method_registry->register( new GooglePayWalletBlock() );
				$payment_method_registry->register( new AfterpayWalletBlock() );
				$payment_method_registry->register( new PayPalWalletBlock() );
				$payment_method_registry->register( new AfterpayAPMsBlock() );
				$payment_method_registry->register( new ZipAPMsBlock() );
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

	protected function addEndpoints() {
		add_action( 'rest_api_init', function () {
			register_rest_route( PLUGIN_TEXT_DOMAIN . '/v1', '/wallets/charge',
				[ // nosemgrep: audit.php.wp.security.rest-route.permission-callback.return-true  -- /wallets/charge is a public endpoint and doesn't need any permission checks.
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ new WidgetController(), 'createWalletCharge' ],
					'permission_callback' => '__return_true',
				] );
		} );
	}

	protected function addOrderActions() {
		$orderService      = new OrderService();
		$paymentController = new PaymentController();
		$widgetController = new WidgetController();

		add_action( 'woocommerce_order_item_add_action_buttons', [ $orderService, 'iniPluginOrderButtons' ], 10,
			2 );
		add_action( 'woocommerce_order_status_changed', [ $orderService, 'statusChangeVerification' ], 20, 4 );
		add_action( 'woocommerce_admin_order_totals_after_total',
			[ $orderService, 'informationAboutPartialCaptured' ] );
		add_action( 'admin_notices', [ $orderService, 'displayStatusChangeError' ] );
		add_action( 'wp_ajax_plugin-capture-charge', [ $paymentController, 'capturePayment' ] );
		add_action( 'wp_ajax_plugin-cancel-authorised', [ $paymentController, 'cancelAuthorised' ] );
		add_action( 'woocommerce_create_refund', [ $paymentController, 'refundProcess' ], 10, 2 );
		add_action( 'woocommerce_order_refunded', [ $paymentController, 'afterRefundProcess' ], 10, 2 );
		add_action( 'woocommerce_api_plugin-webhook', [ $paymentController, 'webhook' ] );
		add_action( 'wc_ajax_create-wallet-charge', [ $widgetController, 'createWalletChargeClassic' ] );
    }
}
