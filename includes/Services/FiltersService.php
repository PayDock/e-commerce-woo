<?php

namespace PowerBoard\Services;

use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Hooks\ActivationHook;
use PowerBoard\Services\Checkout\MasterWidgetPaymentService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Services\Settings\LogsSettingService;

class FiltersService extends AbstractSingleton {
	protected static $instance = null;

	protected function __construct() {
		$this->addWooCommerceFilters();
		$this->addSettingsLink();
	}

	public function ordersListNewColumn( $columns ) {
		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {
			$new_columns[ $column_name ] = $column_info;

			if ( OrderListColumns::AFTER_COLUMN === $column_name ) {
				$new_columns[ OrderListColumns::PAYMENT_SOURCE_TYPE()->get_key() ] = OrderListColumns::PAYMENT_SOURCE_TYPE()->get_label();
			}
		}

		return $new_columns;
	}

	public function ordersListNewColumnContent( $column, $order ) {
		if ( OrderListColumns::PAYMENT_SOURCE_TYPE()->get_key() === $column ) {
			$status = $order->get_meta( OrderListColumns::PAYMENT_SOURCE_TYPE()->get_key() );

			echo esc_html( is_array( $status ) ? reset( $status ) : $status );
		}
	}

	public function changeOrderAmount( $formatted_total, $order ) {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		if ( ! empty( $page ) && 'wc-orders' === $page ) {
			$capturedAmount = $order->get_meta( 'capture_amount' );
			if ( ! empty( $capturedAmount ) ) {

				if ( $capturedAmount == $order->get_total() ) {
					return $formatted_total;
				}

				$price           = wc_price( ( $capturedAmount - $order->get_total_refunded() ),
					[ 'currency' => $order->get_currency() ] );
				$originalPrice   = wc_price( $order->get_total(), [ 'currency' => $order->get_currency() ] );
				$formatted_total = sprintf(
					'<del aria-hidden="true">%1$s</del><ins>%2$s</ins>',
					$originalPrice,
					$price
				);
			}
		}

		return $formatted_total;
	}

	public function addCaptureAmountCustomColumn( $columns ) {
		$new_columns                   = ( is_array( $columns ) ) ? $columns : [];
		$new_columns['capture_amount'] = 'Capture Amount';

		return $new_columns;
	}

	protected function addWooCommerceFilters(): void {
		add_filter( 'woocommerce_payment_gateways', [ $this, 'registerInWooCommercePaymentClass' ] );
		add_filter( 'woocommerce_thankyou_order_received_text', [ $this, 'woocommerceThankyouOrderReceivedText' ] );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', [ $this, 'ordersListNewColumn' ] );
		add_filter( 'manage_woocommerce_page_wc-orders_custom_column', [ $this, 'ordersListNewColumnContent' ], 10, 2 );
		add_filter( 'woocommerce_get_formatted_order_total', [ $this, 'changeOrderAmount' ], 10, 2 );
		add_filter( 'manage_edit-shop_order_columns', [ $this, 'addCaptureAmountCustomColumn' ], 20 );
	}

	protected function addSettingsLink(): void {
		add_filter( 'plugin_action_links_' . plugin_basename( POWER_BOARD_PLUGIN_FILE ), [ $this, 'getSettingLink' ] );
	}

	public function registerInWooCommercePaymentClass( array $methods ): array {

		global $current_section;
		global $current_tab;

		if ( is_admin() ) {

			$methods[] = WidgetConfigurationSettingService::class;

			if (
				'checkout' != $current_tab ||
				in_array(
					$current_section,
					array_map(
						function ( SettingsTabs $tab ) {
							return $tab->value;
						},
						SettingsTabs::secondary()
					)
				)
			) {
				$methods[] = LogsSettingService::class;
			}
		} else {

			$methods[] = MasterWidgetPaymentService::class;

		}

		return $methods;
	}

	public function woocommerceThankyouOrderReceivedText( $text ) {
		$orderId  = absint( get_query_var( 'order-received' ) );
		$options  = get_option( "power_board_fraud_{$orderId}" );
		$order    = wc_get_order( $orderId );
		$status   = $order->get_status();
		$afterpay = filter_input( INPUT_GET, 'afterpay-error', FILTER_SANITIZE_STRING );

		if ( ! empty( $afterpay ) && ( 'true' === $afterpay ) ) {
			return __( 'Order has been cancelled', 'power-board' );
		}
		if ( false === $options && 'processing' !== $status ) {
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

		if ( $order_status != 'pending' ) {
			unset( $actions['pay'] );
			unset( $actions['cancel'] );
		}

		return $actions;
	}
}
