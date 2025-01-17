<?php

namespace PowerBoard\Services;

use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Services\Checkout\MasterWidgetPaymentService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

class FiltersService extends AbstractSingleton {
	protected static $instance = null;

	protected function __construct() {
		$this->addWooCommerceFilters();
		$this->addSettingsLink();
	}

	/**
	 * Uses a function (add_filter) from WordPress
	 */
	protected function addWooCommerceFilters(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'woocommerce_payment_gateways', [ $this, 'registerInWooCommercePaymentClass' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'woocommerce_thankyou_order_received_text', [ $this, 'woocommerceThankyouOrderReceivedText' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'plugins_loaded', [ $this, 'woo_text_override' ] );
	}

	/**
	 * Uses functions (add_filter, plugin_basename) from WordPress
	 */
	protected function addSettingsLink(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		add_filter( 'plugin_action_links_' . plugin_basename( POWER_BOARD_PLUGIN_FILE ), [ $this, 'getSettingLink' ] );
	}

	/**
	 * Uses a function (is_admin) from WordPress
	 */
	public function registerInWooCommercePaymentClass( array $methods ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_admin() ) {
			$methods[] = WidgetConfigurationSettingService::class;
		} else {
			$methods[] = MasterWidgetPaymentService::class;
		}

		return $methods;
	}

	/**
	 * Uses functions (absint, get_query_var, get_option and __) from WordPress
	 * Uses a function (wc_get_order) from WooCommerce
	 */
	public function woocommerceThankyouOrderReceivedText( $text ) {
		/* @noinspection PhpUndefinedFunctionInspection */
		$order_id = absint( get_query_var( 'order-received' ) );
		/* @noinspection PhpUndefinedFunctionInspection */
		$options = get_option( 'power_board_fraud_' . $order_id );
		/* @noinspection PhpUndefinedFunctionInspection */
		$order  = wc_get_order( $order_id );
		$status = $order->get_status();

		if ( $options === false && $status !== 'processing' ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			return __( 'Thank you. Your order has been received.', 'power-board' );
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		return __( 'Your order is being processed. We\'ll get back to you shortly', 'power-board' );
	}

	/**
	 * Uses functions (admin_url and __) from WordPress
	 */
	public function getSettingLink( array $links ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		array_unshift(
			$links,
			sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . PLUGIN_PREFIX ),
				__( 'Settings', 'power-board' )
			)
		);

		return $links;
	}

	public function my_account_classic_payment_edit( $actions, $order ) {

		$order_status = $order->get_status();

		if ( $order_status !== 'pending' ) {
			unset( $actions['pay'] );
			unset( $actions['cancel'] );
		}

		return $actions;
	}

	/**
	 * Uses functions (plugin_dir_path and load_textdomain) from WordPress
	 */
	public function woo_text_override() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$mofile = plugin_dir_path( __FILE__ ) . 'languages/woo-override-en_US.mo';
		/* @noinspection PhpUndefinedFunctionInspection */
		load_textdomain( 'woocommerce', $mofile );
	}
}
