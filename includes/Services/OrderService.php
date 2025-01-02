<?php

namespace PowerBoard\Services;

use PowerBoard\Hooks\ActivationHook;

class OrderService {

	protected $template_service;

	public function __construct() {
		if ( is_admin() ) {
			$this->template_service = new TemplateService( $this );
		}
	}

	public static function update_status( $id, $custom_status, $status_note = null ) {
		$order = wc_get_order( $id );

		if ( is_object( $order ) ) {
				$order->set_status( ActivationHook::CUSTOM_STATUSES[ $custom_status ], $status_note );
				$order->update_meta_data( ActivationHook::CUSTOM_STATUS_META_KEY, $custom_status );
				$order->save();
		}
	}

	public function init_power_board_order_buttons( $order ) {
		$order_custom_status = $order->get_meta( ActivationHook::CUSTOM_STATUS_META_KEY );
		$order_status        = $order->get_status();
		$captured_amount     = $order->get_meta( 'capture_amount' );
		$total_refaund       = $order->get_total_refunded();
		$order_total         = (float) $order->get_total( false );
		if ( in_array(
			$order_status,
			array(
				'pending',
				'failed',
				'cancelled',
				'on-hold',
			)
		)
			|| in_array(
				$order_custom_status,
				array(
					'pb-requested',
					'wc-pb-requested',
					'pb-refunded',
					'WC-pb-refunded',
					'pb-authorize',
					'wc-pb-authorize',
				)
			)
			|| ( $order_total === $total_refaund )
			|| ( $captured_amount === $total_refaund )
		) {
			wp_enqueue_style(
				'hide-refund-button-styles',
				POWER_BOARD_PLUGIN_URL . 'assets/css/admin/hide-refund-button.css',
				array(),
				POWER_BOARD_PLUGIN_VERSION
			);
		}

		if ( in_array(
			$order_status,
			array(
				'on-hold',
			)
		) ) {
			wp_enqueue_style(
				'hide-on-hold-buttons',
				POWER_BOARD_PLUGIN_URL . 'assets/css/admin/hide-on-hold-buttons.css',
				array(),
				POWER_BOARD_PLUGIN_VERSION
			);
		}
	}

	public function status_change_verification( $order_id, $old_status_key, $new_status_key, $order ) {
		$order->update_meta_data( 'status_change_verification_failed', '' );
		if ( ( $old_status_key == $new_status_key ) || ! empty( $GLOBALS['power_board_is_updating_order_status'] ) || null === $order_id ) {
			return;
		}
		$rulesForStatuses = array(
			'processing' => array(
				'refunded',
				'cancelled',
				'failed',
				'pending',
				'completed',
			),
			'refunded'   => array( 'cancelled', 'failed', 'refunded' ),
			'cancelled'  => array( 'failed', 'cancelled' ),
		);
		if ( ! empty( $rulesForStatuses[ $old_status_key ] ) ) {
			if ( ! in_array( $new_status_key, $rulesForStatuses[ $old_status_key ] ) ) {
				$new_status_name = wc_get_order_status_name( $new_status_key );
				$old_status_name = wc_get_order_status_name( $old_status_key );
				$error           = sprintf(
				/*
				 * Translators: %1$s: Old status of processing order.
				 * translators: %2$s: New status of processing order.
				 */
					__( 'You can not change status from "%1$s"  to "%2$s"', 'power-board' ),
					$old_status_name,
					$new_status_name
				);
				$GLOBALS['power_board_is_updating_order_status'] = true;
				$order->update_meta_data( 'status_change_verification_failed', 1 );
				$order->update_status( $old_status_key, $error );
				update_option( 'power_board_status_change_error', $error );
				unset( $GLOBALS['power_board_is_updating_order_status'] );
				throw new \Exception( esc_html( $error ) );
			}
		}
	}

	public function information_about_partial_captured( $order_id ) {

		$order = wc_get_order( $order_id );

		if ( is_object( $order ) ) {

			$captured_amount = $order->get_meta( 'capture_amount' );

			if ( ! empty( $captured_amount ) ) {
				$this->template_service->include_admin_html( 'information-about-partial-captured', compact( 'order', 'captured_amount' ) );
			}
		}
	}

	public function display_status_change_error() {
		$screen = get_current_screen();
		if ( 'woocommerce_page_wc-orders' === $screen->id ) {
			$message = get_option( 'power_board_status_change_error', '' );
			if ( ! empty( $message ) ) {
				echo '<div class=\'notice notice-error is-dismissible\'><p>' . esc_html( $message ) . '</p></div>';
				delete_option( 'power_board_status_change_error' );
			}
		}
	}
}
