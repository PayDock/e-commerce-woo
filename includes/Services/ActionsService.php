<?php

namespace PowerBoard\Services;

use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Controllers\Admin\WidgetController;
use PowerBoard\Controllers\Webhooks\PaymentController;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Services\Checkout\BankAccountPaymentService;
use PowerBoard\Util\AfterpayAPMsBlock;
use PowerBoard\Util\AfterpayWalletBlock;
use PowerBoard\Util\ApplePayWalletBlock;
use PowerBoard\Util\BankAccountBlock;
use PowerBoard\Util\GooglePayWalletBlock;
use PowerBoard\Util\PayPalWalletBlock;
use PowerBoard\Util\PowerBoardGatewayBlocks;
use PowerBoard\Util\ZipAPMsBlock;

class ActionsService extends AbstractSingleton {
	protected const PROCESS_OPTIONS_FUNCTION = 'process_admin_options';
	protected const PROCESS_OPTIONS_HOOK_PREFIX = 'woocommerce_update_options_payment_gateways_';
	protected const SECTION_HOOK = 'woocommerce_get_sections';
	protected static $instance = null;

	protected function __construct() {
		add_action( 'before_woocommerce_init', function () {
			$this->addCompatibilityWithWooCommerce();
			$this->addPaymentActions();
			$this->addPaymentMethodToChekout();
			$this->addSettingsActions();
			$this->addEndpoints();
			$this->addOrderActions();
		} );
	}

	protected function addCompatibilityWithWooCommerce(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', POWER_BOARD_PLUGIN_FILE );
		}
	}

	protected function addPaymentActions() {
		$payments = [
			'power_board_bank_account_gateway' => new BankAccountPaymentService(),
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
				POWER_BOARD_PLUGIN_FILE,
				true
			);
		} );

		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			function ( PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new PowerBoardGatewayBlocks() );
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
			register_rest_route( 'power-board/v1', '/wallets/charge',
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
		add_action( 'woocommerce_order_item_add_action_buttons', [ $orderService, 'iniPowerBoardOrderButtons' ], 10,
			2 );
		add_action( 'woocommerce_order_status_changed', [ $orderService, 'statusChangeVerification' ], 20, 4 );
		add_action( 'woocommerce_admin_order_totals_after_total',
			[ $orderService, 'informationAboutPartialCaptured' ] );
		add_action( 'admin_notices', [ $orderService, 'displayStatusChangeError' ] );
		add_action( 'wp_ajax_power-board-capture-charge', [ $paymentController, 'capturePayment' ] );
		add_action( 'wp_ajax_power-board-cancel-authorised', [ $paymentController, 'cancelAuthorised' ] );
		add_action( 'woocommerce_create_refund', [ $paymentController, 'refundProcess' ], 10, 2 );
		add_action( 'woocommerce_order_refunded', [ $paymentController, 'afterRefundProcess' ], 10, 2 );
		add_action( 'woocommerce_api_power-board-webhook', [ $paymentController, 'webhook' ] );
    }
}
