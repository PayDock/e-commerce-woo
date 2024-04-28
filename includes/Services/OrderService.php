<?php

namespace Paydock\Services;

class OrderService {

	protected $templateService;

	public function __construct() {
		if ( is_admin() ) {
			$this->templateService = new TemplateService( $this );
		}
	}

	public function iniPaydockOrderButtons( $order ) {
		$orderStatus = $order->get_status();
		if ( in_array( $orderStatus, [ 
			'paydock-pending',
			'paydock-failed',
			'paydock-refunded',
			'paydock-authorize',
			'paydock-cancelled',
		] ) ) {
			$this->templateService->includeAdminHtml( 'hide-refund-button' );
		}
		if ( in_array( $orderStatus, [ 
			'paydock-authorize',
			'paydock-paid'
		] ) ) {
			$this->templateService->includeAdminHtml( 'paydock-capture-block', compact( 'order', 'order' ) );
		}
	}

	public function statusChangeVerification( $orderId, $oldStatusKey, $newStatusKey, $order ) {
		if ( ( $oldStatusKey == $newStatusKey ) || ! empty( $GLOBALS['is_updating_paydock_order_status'] ) || null === $orderId ) {
			return;
		}

		$rulesForStatuses = [ 
			'paydock-paid' => [ 
				'paydock-refunded',
				'paydock-p-refund',
				'cancelled',
				'paydock-cancelled',
				'refunded',
				'paydock-failed',
				'paydock-pending',
			],
			'paydock-refunded' => [ 'paydock-paid', 'cancelled', 'paydock-failed', 'refunded' ],
			'paydock-p-refund' => [ 'paydock-paid', 'paydock-refunded', 'refunded', 'cancelled', 'paydock-failed' ],
			'paydock-authorize' => [ 
				'paydock-paid',
				'paydock-cancelled',
				'paydock-failed',
				'cancelled',
				'paydock-pending',
			],
			'paydock-cancelled' => [ 'paydock-failed', 'cancelled' ],
			'paydock-requested' => [ 
				'paydock-paid',
				'paydock-failed',
				'cancelled',
				'paydock-pending',
				'paydock-authorize',
			],
		];
		if ( ! empty( $rulesForStatuses[ $oldStatusKey ] ) ) {
			if ( ! in_array( $newStatusKey, $rulesForStatuses[ $oldStatusKey ] ) ) {
				$newStatusName = wc_get_order_status_name( $newStatusKey );
				$oldStatusName = wc_get_order_status_name( $oldStatusKey );
				$error = __(
					'You can not change status from "' . $oldStatusName . '"  to "' . $newStatusName . '"',
					'woocommerce'
				);
				$GLOBALS['is_updating_paydock_order_status'] = true;
				$order->update_status( $oldStatusKey, $error );
				update_option( 'paydock_status_change_error', $error );
				unset( $GLOBALS['is_updating_paydock_order_status'] );
				throw new \Exception( $error );
			}
		}
	}

	public function displayStatusChangeError() {
		$screen = get_current_screen();
		if ( 'woocommerce_page_wc-orders' == $screen->id ) {
			$message = get_option( 'paydock_status_change_error', '' );
			if ( ! empty( $message ) ) {
				echo '<div class=\'notice notice-error is-dismissible\'><p>' . esc_html( $message ) . '</p></div>';
				delete_option( 'paydock_status_change_error' );
			}
		}
	}
}
