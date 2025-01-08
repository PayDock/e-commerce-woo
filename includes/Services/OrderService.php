<?php

namespace PowerBoard\Services;

use Exception;

class OrderService {

	protected $template_service;

	public function __construct() {
		if ( is_admin() ) {
			$this->template_service = new TemplateService( $this );
			add_action( 'admin_notices', [ $this, 'display_status_change_error' ] );
		}
	}

	public static function update_status( $id, $new_status, $status_note = null ) {
		$order = wc_get_order( $id );

		if ( is_object( $order ) ) {
			$order->set_status( $new_status, $status_note );
			$order->save();
		}
	}

	public function init_power_board_order_buttons( $order ) {
		$order_status        = $order->get_status();
		$captured_amount     = $order->get_meta( 'capture_amount' );
		$total_refund        = $order->get_total_refunded();
		$order_total         = (float) $order->get_total( false );
		if ( in_array( $order_status, array( 'pending', 'failed', 'cancelled', 'on-hold', 'refunded' ) )
			|| ( $order_total === $total_refund )
			|| ( $captured_amount === $total_refund )
		) {
			wp_enqueue_style(
				'hide-refund-button-styles',
				POWER_BOARD_PLUGIN_URL . 'assets/css/admin/hide-refund-button.css',
				[],
				POWER_BOARD_PLUGIN_VERSION
			);
		}

		if ( $order_status == 'on-hold' ) {
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
			'processing' => array( 'refunded', 'cancelled', 'failed', 'pending', 'completed' ),
			'refunded'   => array( 'cancelled', 'failed', 'refunded' ),
			'cancelled'  => array( 'failed', 'cancelled' ),
		);
		if ( ! empty( $rulesForStatuses[ $old_status_key ] ) ) {
			if ( ! in_array( $new_status_key, $rulesForStatuses[ $old_status_key ] ) ) {
				$new_status_name = wc_get_order_status_name( $new_status_key );
				$old_status_name = wc_get_order_status_name( $old_status_key );
				$error           = sprintf(
					__( 'You can not change status from "%1$s"  to "%2$s"', 'power-board' ),
					$old_status_name,
					$new_status_name
				);
				$GLOBALS['power_board_is_updating_order_status'] = true;
				$order->update_meta_data( 'status_change_verification_failed', 1 );
				$order->update_status( $old_status_key, $error );
				set_transient( 'power_board_status_change_error_' . get_current_user_id(), $error, 300 );
				unset( $GLOBALS['power_board_is_updating_order_status'] );
				throw new Exception( esc_html( $error ) );
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
		$error_message = get_transient( 'power_board_status_change_error_' . get_current_user_id() );
		if ( $error_message ) {
			echo '<div id="power-board-error-message" class="notice notice-error is-dismissible"><p>' . esc_html( $error_message ) . '</p></div>';
			echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#message.updated.notice.notice-success").hide();
			});
			</script>';
			delete_transient( 'power_board_status_change_error_' . get_current_user_id() );
		}
	}
}
