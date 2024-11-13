<?php

namespace WooPlugin\Services;

use WooPlugin\Hooks\ActivationHook;

class OrderService {

	protected $templateService;

	public function __construct() {
		if ( is_admin() ) {
			$this->templateService = new TemplateService( $this );
		}
	}

	public static function updateStatus( $id, $new_status, $status_note = null ) {
		$order = wc_get_order( $id );

		if ( is_object( $order ) ) {
			$order->set_status( $new_status, $status_note );
			$order->save();
		}
	}

	public function iniPluginOrderButtons( $order ) {
		$orderStatus       = $order->get_status();
		$capturedAmount    = $order->get_meta( 'capture_amount' );
		$totalRefund       = $order->get_total_refunded();
		$orderTotal        = (float) $order->get_total(false);

		if ( in_array( $orderStatus, [ 'pending', 'failed', 'cancelled', 'on-hold', 'refunded' ] )
			|| ( $orderTotal == $totalRefund )
			|| ( $capturedAmount == $totalRefund )
		) {
			wp_enqueue_style(
				'hide-refund-button-styles',
				PLUGIN_URL . 'assets/css/admin/hide-refund-button.css',
				[],
				PLUGIN_VERSION
			);
		}
		if ( in_array( $orderStatus, [ 'processing', 'on-hold', ] ) ) {
			$this->templateService->includeAdminHtml( PLUGIN_TEXT_DOMAIN . '-capture-block', compact( 'order' ) );
			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-capture-block',
				PLUGIN_URL . 'assets/js/admin/' . PLUGIN_TEXT_DOMAIN . '-capture-block.js',
				[],
				time(),
				true
			);
			wp_localize_script( PLUGIN_TEXT_DOMAIN . '-capture-block', 'pluginCaptureBlockSettings', [
				'wpnonce' => esc_attr( wp_create_nonce( 'capture-or-cancel' ) ),
			] );
		}
		if ( in_array( $orderStatus, [
				'on-hold',
			] ) ) {
			wp_enqueue_style(
				'hide-on-hold-buttons',
				PLUGIN_URL . 'assets/css/admin/hide-on-hold-buttons.css',
				[],
				PLUGIN_VERSION
			);
		}
	}

	public function statusChangeVerification( $orderId, $oldStatusKey, $newStatusKey, $order ) {
		$order->update_meta_data( 'status_change_verification_failed', "" );
		if ( ( $oldStatusKey == $newStatusKey ) || ! empty( $GLOBALS[PLUGIN_PREFIX . '_is_updating_order_status'] ) || null === $orderId ) {
			return;
		}
		$rulesForStatuses = [
			'processing' => [ 'refunded', 'cancelled', 'failed', 'pending', 'completed' ],
			'refunded'   => [ 'cancelled', 'failed', 'refunded' ],
			'cancelled'  => [ 'failed', 'cancelled' ],
		];
		if ( ! empty( $rulesForStatuses[ $oldStatusKey ] ) ) {
			if ( ! in_array( $newStatusKey, $rulesForStatuses[ $oldStatusKey ] ) ) {
				$newStatusName = wc_get_order_status_name( $newStatusKey );
				$oldStatusName = wc_get_order_status_name( $oldStatusKey );
				$error         = sprintf(
					__( 'You can not change status from "%1$s"  to "%2$s"', 'power-board' ),
					$oldStatusName,
					$newStatusName
				);
				$GLOBALS[PLUGIN_PREFIX . '_is_updating_order_status'] = true;
				$order->update_meta_data( 'status_change_verification_failed', 1 );
				$order->update_status( $oldStatusKey, $error );
				update_option( PLUGIN_PREFIX . '_status_change_error', $error );
				unset( $GLOBALS[PLUGIN_PREFIX . '_is_updating_order_status'] );
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
			$message = get_option( PLUGIN_PREFIX . '_status_change_error', '' );
			if ( ! empty( $message ) ) {
				echo '<div class=\'notice notice-error is-dismissible\'><p>' . esc_html( $message ) . '</p></div>';
				delete_option( PLUGIN_PREFIX . '_status_change_error' );
			}
		}
	}
}
