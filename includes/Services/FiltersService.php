<?php

namespace PowerBoard\Services;

use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\Checkout\AfterpayAPMsPaymentServiceService;
use PowerBoard\Services\Checkout\AfterpayWalletService;
use PowerBoard\Services\Checkout\ApplePayWalletService;
use PowerBoard\Services\Checkout\BankAccountPaymentService;
use PowerBoard\Services\Checkout\CardPaymentService;
use PowerBoard\Services\Checkout\GooglePayWalletService;
use PowerBoard\Services\Checkout\PayPalWalletService;
use PowerBoard\Services\Checkout\ZipAPMsPaymentServiceService;
use PowerBoard\Services\Settings\LiveConnectionSettingService;
use PowerBoard\Services\Settings\LogsSettingService;
use PowerBoard\Services\Settings\SandboxConnectionSettingService;
use PowerBoard\Services\Settings\WidgetSettingService;

class FiltersService extends AbstractSingleton {
	protected static $instance = null;

	protected function __construct() {
		$this->addWooCommerceFilters();
		$this->addSettingsLink();
	}

	public function ordersListNewColumn( $columns ) {
		$new_columns = [];

		foreach ( $columns as $column_name => $column_info ) {
			$new_columns[ $column_name ] = $column_info;

			if ( OrderListColumns::AFTER_COLUMN === $column_name ) {
				$new_columns[ OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey() ] = __(
					OrderListColumns::PAYMENT_SOURCE_TYPE()->getLabel(),
					POWER_BOARD_TEXT_DOMAIN
				);
			}
		}

		return $new_columns;
	}

	public function ordersListNewColumnContent( $column, $order ) {
		if ( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey() === $column ) {
			$status = get_post_meta( $order->get_id(), OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey() );

			echo esc_html( is_array( $status ) ? reset( $status ) : $status );
		}
	}

	public function changeOrderAmount( $formatted_total, $order ) {
		if ( isset( $_GET['page'] ) && 'wc-orders' === $_GET['page'] ) {
			$capturedAmount = get_post_meta( $order->get_id(), 'capture_amount' );
			if ( $capturedAmount && is_array( $capturedAmount ) ) {
				$capturedAmount = reset( $capturedAmount );

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
		add_filter( 'woocommerce_register_shop_order_post_statuses', [ $this, 'addCustomOrderStatuses' ] );
		add_filter( 'wc_order_statuses', [ $this, 'addCustomOrderSingleStatusesStatuses' ] );
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

		$methods[] = LiveConnectionSettingService::class;
		if ( 'checkout' != $current_tab
		     || in_array(
			     $current_section,
			     array_map(
				     function ( SettingsTabs $tab ) {
					     return $tab->value;
				     },
				     SettingsTabs::secondary()
			     )
		     ) ) {
			$methods[] = SandboxConnectionSettingService::class;
			$methods[] = WidgetSettingService::class;
			$methods[] = LogsSettingService::class;
			$methods[] = CardPaymentService::class;
			$methods[] = BankAccountPaymentService::class;
			$methods[] = ApplePayWalletService::class;
			$methods[] = GooglePayWalletService::class;
			$methods[] = AfterpayWalletService::class;
			$methods[] = PayPalWalletService::class;
			$methods[] = AfterpayAPMsPaymentServiceService::class;
			$methods[] = ZipAPMsPaymentServiceService::class;
		}


		return $methods;
	}

	public function woocommerceThankyouOrderReceivedText( $text ) {
		$orderId = absint( get_query_var( 'order-received' ) );
		$options = get_option( "power_board_fraud_{$orderId}" );
		$order   = wc_get_order( $orderId );
		$status  = $order->get_status();

		if ( isset( $_GET['afterpay-error'] ) && ( 'true' === $_GET['afterpay-error'] ) ) {
			return __( 'Order has been cancelled', 'power_board' );
		}
		if ( false === $options && 'processing' !== $status ) {
			return $text;
		}

		return __( 'Your order is being processed. We\'ll get back to you shortly', 'power_board' );
	}

	public function getSettingLink( array $links ): array {
		array_unshift(
			$links,
			sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . PowerBoardPlugin::PLUGIN_PREFIX ),
				__( 'Settings', 'power_board' )
			)
		);

		return $links;
	}

	public function addCustomOrderStatuses( $order_statuses ) {
		$order_statuses['wc-pb-paid']      = [
			'label'                     => 'Paid via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Paid via PowerBoard <span class="count">(%s)</span>',
				'Paid via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-p-paid']    = [
			'label'                     => 'Partial paid via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Partial paid via PowerBoard <span class="count">(%s)</span>',
				'Partial paid via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-pending']   = [
			'label'                     => 'Pending via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Panding via PowerBoard <span class="count">(%s)</span>',
				'Panding via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-failed']    = [
			'label'                     => 'Failed via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Failed via PowerBoard <span class="count">(%s)</span>',
				'Failed via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-authorize'] = [
			'label'                     => 'Authorized via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Authorized via PowerBoard <span class="count">(%s)</span>',
				'Authorized via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-cancelled'] = [
			'label'                     => 'Cancelled via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Cancelled via PowerBoard <span class="count">(%s)</span>',
				'Cancelled via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-refunded']  = [
			'label'                     => 'Refunded via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Refunded via PowerBoard <span class="count">(%s)</span>',
				'Refunded via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-p-refund']  = [
			'label'                     => 'Partial refunded via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Partial refunded via PowerBoard <span class="count">(%s)</span>',
				'Partial refunded via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];
		$order_statuses['wc-pb-requested'] = [
			'label'                     => 'Requested via PowerBoard',
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			// Translators: %s is the number of orders.
			'label_count'               => _n_noop(
				'Requested via PowerBoard <span class="count">(%s)</span>',
				'Requested via PowerBoard <span class="count">(%s)</span>',
				'woocommerce'
			),
		];

		return $order_statuses;
	}

	public function addCustomOrderSingleStatusesStatuses( $order_statuses ) {
		$order_statuses['wc-pb-failed']    = 'Failed via PowerBoard';
		$order_statuses['wc-pb-pending']   = 'Pending via PowerBoard';
		$order_statuses['wc-pb-paid']      = 'Paid via PowerBoard';
		$order_statuses['wc-pb-authorize'] = 'Authorized via PowerBoard';
		$order_statuses['wc-pb-cancelled'] = 'Cancelled via PowerBoard';
		$order_statuses['wc-pb-refunded']  = 'Refunded via PowerBoard';
		$order_statuses['wc-pb-p-refund']  = 'Partial refunded via PowerBoard';
		$order_statuses['wc-pb-requested'] = 'Requested via PowerBoard';
		$order_statuses['wc-pb-p-paid']    = 'Partial paid via  PowerBoard';

		return $order_statuses;
	}
}
