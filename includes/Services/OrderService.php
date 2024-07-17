<?php

namespace PowerBoard\Services;

use PowerBoard\Hooks\ActivationHook;

class OrderService {

	protected $templateService;

	public function __construct() {
		if ( is_admin() ) {
			$this->templateService = new TemplateService( $this );
		}
	}

	public function iniPowerBoardOrderButtons( $order ) {
		$orderCustomStatus = $order->get_meta( ActivationHook::CUSTOM_STATUS_META_KEY );
		$orderStatus       = $order->get_status();
		$capturedAmount    = get_post_meta( $order->get_id(), 'capture_amount' );
		$capturedAmount    = is_array( $capturedAmount ) ? reset( $capturedAmount ) : $capturedAmount;
		$totalRefaund      = $order->get_total_refunded();
		if ( in_array( $orderStatus, [
				'pending',
				'failed',
				'cancelled',
			] )
		     || in_array( $orderCustomStatus, [
				'pb-requested',
				'wc-pb-requested',
				'pb-refunded',
				'WC-pb-refunded',
				'pb-authorize',
				'wc-pb-authorize'
			] )
		     || ( $order->get_total() == $totalRefaund )
		     || ( $capturedAmount == $totalRefaund )
		) {
			$this->templateService->includeAdminHtml( 'hide-refund-button' );
		}
		if ( in_array( $orderStatus, [
				'processing',
			] ) && in_array( $orderCustomStatus, [
				'pb-authorize',
				'wc-pb-authorize',
				'pb-paid',
				'wc-pb-paid',
				'wc-pb-p-paid',
				'pb-p-paid'
			] ) ) {
			$this->templateService->includeAdminHtml( 'power-board-capture-block', compact( 'order' ) );
		}
	}

	public function statusChangeVerification( $orderId, $oldStatusKey, $newStatusKey, $order ) {
		if ( ( $oldStatusKey == $newStatusKey ) || ! empty( $GLOBALS['power_board_is_updating_order_status'] ) || null === $orderId ) {
			return;
		}
		$rulesForStatuses = [
			'processing' => [
				'refunded',
				'cancelled',
				'failed',
				'pending',
			],
			'refunded'   => [ 'processing', 'cancelled', 'failed', 'refunded' ],
			'cancelled'  => [ 'failed', 'cancelled' ],
		];
		if ( ! empty( $rulesForStatuses[ $oldStatusKey ] ) ) {
			if ( ! in_array( $newStatusKey, $rulesForStatuses[ $oldStatusKey ] ) ) {
				$newStatusName                                   = wc_get_order_status_name( $newStatusKey );
				$oldStatusName                                   = wc_get_order_status_name( $oldStatusKey );
				$error                                           = sprintf(
				/* translators: %1$s: Old status of processing order.
				 * translators: %2$s: New status of processing order.
				 */
					__( 'You can not change status from "%1$s"  to "%2$s"', 'power-board' ),
					$oldStatusName,
					$newStatusName
				);
				$GLOBALS['power_board_is_updating_order_status'] = true;
				$order->update_status( $oldStatusKey, $error );
				update_option( 'power_board_status_change_error', $error );
				unset( $GLOBALS['power_board_is_updating_order_status'] );
				throw new \Exception( esc_html( $error ) );
			}
		}
	}

	public function informationAboutPartialCaptured( $orderId ) {
		$capturedAmount = get_post_meta( $orderId, 'capture_amount' );
		$order          = wc_get_order( $orderId );
		if ( $capturedAmount && is_array( $capturedAmount ) && in_array( $order->get_status(), [
				'failed',
				'pending',
				'processing',
				'refunded',
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

	public static function updateStatus( $id, $custom_status, $status_note = null ) {
		$order = wc_get_order( $id );

		$order->set_status( ActivationHook::CUSTOM_STATUSES[ $custom_status ], $status_note );
		$order->update_meta_data( ActivationHook::CUSTOM_STATUS_META_KEY, $custom_status );
		$order->save();
	}
}
