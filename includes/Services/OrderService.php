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

	public static function updateStatus( $id, $custom_status, $status_note = null ) {

		$order = wc_get_order( $id );

		if ( is_object( $order ) ) {

			$partial_refund = strpos( $custom_status, 'pb-p-refund' );

			if ( $partial_refund === false ) {

				$order->set_status( ActivationHook::CUSTOM_STATUSES[ $custom_status ], $status_note );
				$order->update_meta_data( ActivationHook::CUSTOM_STATUS_META_KEY, $custom_status );
				$order->save();

			} else {

				if ( ! empty( $status_note ) ) {
					$order->add_order_note( $status_note );
				}

			}

		}

	}

	public function iniPowerBoardOrderButtons( $order ) {
		$orderCustomStatus = $order->get_meta( ActivationHook::CUSTOM_STATUS_META_KEY );
		$orderStatus       = $order->get_status();
		$capturedAmount    = $order->get_meta( 'capture_amount' );
		$totalRefaund      = $order->get_total_refunded();
		$orderTotal      = (float) $order->get_total(false);
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
		     || ( $orderTotal == $totalRefaund )
		     || ( $capturedAmount == $totalRefaund )
		) {
			wp_enqueue_style(
				'hide-refund-button-styles',
				POWER_BOARD_PLUGIN_URL . 'assets/css/admin/hide-refund-button.css',
				[],
				POWER_BOARD_PLUGIN_VERSION
			);
		}
		if ( in_array( $orderStatus, [
				'processing',
				'on-hold',
			] ) && in_array( $orderCustomStatus, [
				'pb-authorize',
				'wc-pb-authorize',
				'pb-paid',
				'wc-pb-paid',
				'wc-pb-p-paid',
				'pb-p-paid'
			] ) ) {
			$this->templateService->includeAdminHtml( 'power-board-capture-block', compact( 'order' ) );
			wp_enqueue_script(
				'power-board-capture-block',
				POWER_BOARD_PLUGIN_URL . 'assets/js/admin/power-board-capture-block.js',
				[],
				time(),
				true
			);
			wp_localize_script( 'power-board-capture-block', 'powerBoardCaptureBlockSettings', [
				'wpnonce' => esc_attr( wp_create_nonce( 'capture-or-cancel' ) ),
			] );
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
				'completed',
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

		$order = wc_get_order( $orderId );

		if ( is_object( $order ) ) {

			$capturedAmount = $order->get_meta( 'capture_amount' );

			if ( ! empty( $capturedAmount ) ) {

				// if ( $order->get_total() > $capturedAmount ) {
					$this->templateService->includeAdminHtml( 'information-about-partial-captured', compact( 'order', 'capturedAmount' ) );
				// }

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
