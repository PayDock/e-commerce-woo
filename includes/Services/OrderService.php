<?php

namespace Paydock\Services;

use Paydock\Hooks\ActivationHook;

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
				$order->set_status( ActivationHook::CUSTOM_STATUSES[ $custom_status ], $status_note );
				$order->update_meta_data( ActivationHook::CUSTOM_STATUS_META_KEY, $custom_status );
				$order->save();
		}
	}

	public function iniPaydockOrderButtons( $order ) {
		$orderCustomStatus = $order->get_meta( ActivationHook::CUSTOM_STATUS_META_KEY );
		$orderStatus       = $order->get_status();
		$capturedAmount    = $order->get_meta( 'capture_amount' );
		$totalRefaund      = $order->get_total_refunded();
		$orderTotal      = (float) $order->get_total(false);
		if ( in_array( $orderStatus, [
				'pending',
				'failed',
				'cancelled',
				'on-hold',
			] )
		     || in_array( $orderCustomStatus, [
				'paydock-requested',
				'wc-paydock-requested',
				'paydock-refunded',
				'WC-paydock-refunded',
				'paydock-authorize',
				'wc-paydock-authorize'
			] )
		     || ( $orderTotal == $totalRefaund )
		     || ( $capturedAmount == $totalRefaund )
		) {
			wp_enqueue_style(
				'hide-refund-button-styles',
				PAYDOCK_PLUGIN_URL . 'assets/css/admin/hide-refund-button.css',
				[],
				PAYDOCK_PLUGIN_VERSION
			);
		}
		if ( in_array( $orderStatus, [
				'processing',
				'on-hold',
			] ) && in_array( $orderCustomStatus, [
				'paydock-authorize',
				'wc-paydock-authorize',
				'paydock-paid',
				'wc-paydock-paid',
				'wc-paydock-p-paid',
				'paydock-p-paid'
			] ) ) {
			$this->templateService->includeAdminHtml( 'paydock-capture-block', compact( 'order' ) );
			wp_enqueue_script(
				'paydock-capture-block',
				PAYDOCK_PLUGIN_URL . 'assets/js/admin/paydock-capture-block.js',
				[],
				time(),
				true
			);
			wp_localize_script( 'paydock-capture-block', 'paydockCaptureBlockSettings', [
				'wpnonce' => esc_attr( wp_create_nonce( 'capture-or-cancel' ) ),
			] );
		}
		if ( in_array( $orderStatus, [
				'on-hold',
			] ) ) {
			wp_enqueue_style(
				'hide-on-hold-buttons',
				PAYDOCK_PLUGIN_URL . 'assets/css/admin/hide-on-hold-buttons.css',
				[],
				PAYDOCK_PLUGIN_VERSION
			);
		}
	}

	public function statusChangeVerification( $orderId, $oldStatusKey, $newStatusKey, $order ) {
		$order->update_meta_data( 'status_change_verification_failed', "" );
		if ( ( $oldStatusKey == $newStatusKey ) || ! empty( $GLOBALS['paydock_is_updating_order_status'] ) || null === $orderId ) {
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
			'refunded'   => [ 'cancelled', 'failed', 'refunded' ],
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
					__( 'You can not change status from "%1$s"  to "%2$s"', 'paydock' ),
					$oldStatusName,
					$newStatusName
				);
				$GLOBALS['paydock_is_updating_order_status'] = true;
				$order->update_meta_data( 'status_change_verification_failed', 1 );
				$order->update_status( $oldStatusKey, $error );
				update_option( 'paydock_status_change_error', $error );
				unset( $GLOBALS['paydock_is_updating_order_status'] );
				throw new \Exception( esc_html( $error ) );
			}
		}
	}

	public function informationAboutPartialCaptured( $orderId ) {

		$order = wc_get_order( $orderId );

		if ( is_object( $order ) ) {

			$capturedAmount = $order->get_meta( 'capture_amount' );

			if ( ! empty( $capturedAmount ) ) {
					$this->templateService->includeAdminHtml( 'information-about-partial-captured', compact( 'order', 'capturedAmount' ) );
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
