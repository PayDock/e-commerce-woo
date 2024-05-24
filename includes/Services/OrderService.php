<?php

namespace PowerBoard\Services;

class OrderService {

	protected $templateService;

	public function __construct() {
		if ( is_admin() ) {
			$this->templateService = new TemplateService( $this );
		}
	}

	public function iniPowerBoardOrderButtons( $order ) {
		$orderStatus = $order->get_status();
		if ( in_array( $orderStatus, [
			'pb-pending',
			'pb-failed',
			'pb-refunded',
			'pb-authorize',
			'pb-cancelled',
		] ) ) {
			$this->templateService->includeAdminHtml( 'hide-refund-button' );
		}
		if ( in_array( $orderStatus, [
			'pb-authorize',
			'pb-paid',
			'pb-p-paid',
		] ) ) {
			$this->templateService->includeAdminHtml( 'power-board-capture-block', compact( 'order', 'order' ) );
		}
	}

	public function statusChangeVerification( $orderId, $oldStatusKey, $newStatusKey, $order ) {
		if ( ( $oldStatusKey == $newStatusKey ) || ! empty( $GLOBALS['is_updating_power_board_order_status'] ) || null === $orderId ) {
			return;
		}

		$rulesForStatuses = [
			'pb-paid'      => [
				'pb-refunded',
				'pb-p-refund',
				'cancelled',
				'pb-cancelled',
				'refunded',
				'pb-failed',
				'pb-pending',
			],
			'pb-p-paid'    => [
				'pb-refunded',
				'pb-p-refund',
				'cancelled',
				'pb-cancelled',
				'refunded',
				'pb-failed',
				'pb-pending',
			],
			'pb-refunded'  => [ 'pb-paid', 'pb-p-paid', 'cancelled', 'pb-failed', 'refunded' ],
			'pb-p-refund'  => [ 'pb-paid', 'pb-p-paid', 'pb-refunded', 'refunded', 'cancelled', 'pb-failed' ],
			'pb-authorize' => [
				'pb-paid',
				'pb-p-paid',
				'pb-cancelled',
				'pb-failed',
				'cancelled',
				'pb-pending',
			],
			'pb-cancelled' => [ 'pb-failed', 'cancelled' ],
			'pb-requested' => [
				'pb-paid',
				'pb-p-paid',
				'pb-failed',
				'cancelled',
				'pb-pending',
				'pb-authorize',
			],
		];
		if ( ! empty( $rulesForStatuses[ $oldStatusKey ] ) ) {
			if ( ! in_array( $newStatusKey, $rulesForStatuses[ $oldStatusKey ] ) ) {
				$newStatusName                                   = wc_get_order_status_name( $newStatusKey );
				$oldStatusName                                   = wc_get_order_status_name( $oldStatusKey );
				$error                                           = __(
					'You can not change status from "' . $oldStatusName . '"  to "' . $newStatusName . '"',
					'woocommerce'
				);
				$GLOBALS['is_updating_power_board_order_status'] = true;
				$order->update_status( $oldStatusKey, $error );
				update_option( 'power_board_status_change_error', $error );
				unset( $GLOBALS['is_updating_power_board_order_status'] );
				throw new \Exception( $error );
			}
		}
	}

	public function informationAboutPartialCaptured( $orderId ) {
		$capturedAmount = get_post_meta( $orderId, 'capture_amount' );
		$order          = wc_get_order( $orderId );
		if ( $capturedAmount && is_array( $capturedAmount ) && in_array( $order->get_status(), [
				'pb-failed',
				'pb-pending',
				'pb-paid',
				'pb-authorize',
				'pb-cancelled',
				'pb-p-refund',
				'pb-requested',
				'pb-p-paid',
			] ) ) {
			$capturedAmount = reset( $capturedAmount );
			if ( $order->get_total() > $capturedAmount ) {
				$this->templateService->includeAdminHtml( 'information-about-partial-captured',
					compact( 'order', 'capturedAmount' ) );
			}
		}
	}


	public function displayStatusChangeError() {
		$screen = get_current_screen();
		if ( 'woocommerce_page_wc-orders' == $screen->id ) {
			$message = get_option( 'power_board_status_change_error', '' );
			if ( ! empty( $message ) ) {
				echo '<div class=\'notice notice-error is-dismissible\'><p>' . esc_html( $message ) . '</p></div>';
				delete_option( 'power_board_status_change_error' );
			}
		}
	}
}
