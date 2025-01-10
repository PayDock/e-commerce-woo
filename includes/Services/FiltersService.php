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

	protected function addWooCommerceFilters(): void {
		add_filter( 'woocommerce_payment_gateways', [ $this, 'registerInWooCommercePaymentClass' ] );
		add_filter( 'woocommerce_thankyou_order_received_text', [ $this, 'woocommerceThankyouOrderReceivedText' ] );
		add_filter( 'plugins_loaded', [ $this, 'woo_text_override' ] );
	}

	protected function addSettingsLink(): void {
		add_filter( 'plugin_action_links_' . plugin_basename( POWER_BOARD_PLUGIN_FILE ), [ $this, 'getSettingLink' ] );
	}

	public function registerInWooCommercePaymentClass( array $methods ): array {
		if ( is_admin() ) {
			$methods[] = WidgetConfigurationSettingService::class;
		} else {
			$methods[] = MasterWidgetPaymentService::class;
		}

		return $methods;
	}

	public function woocommerceThankyouOrderReceivedText( $text ) {
		$order_id = absint( get_query_var( 'order-received' ) );
		$options  = get_option( "power_board_fraud_{$order_id}" );
		$order    = wc_get_order( $order_id );
		$status   = $order->get_status();

		if ( $options === false && $status !== 'processing' ) {
			return __( 'Thank you. Your order has been received.', 'power-board' );
		}

		return __( 'Your order is being processed. We\'ll get back to you shortly', 'power-board' );
	}

	public function getSettingLink( array $links ): array {
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

	public function woo_text_override() {
		$mofile = plugin_dir_path( __FILE__ ) . 'languages/woo-override-en_US.mo';
		load_textdomain( 'woocommerce', $mofile );
	}
}
