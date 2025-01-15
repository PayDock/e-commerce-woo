<?php

namespace PowerBoard\Services;

use Exception;

class OrderService {
	protected TemplateService $template_service;

	/**
	 * Uses functions (is_admin and add_action) from WordPress
	 */
	public function __construct() {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_admin() ) {
			$this->template_service = new TemplateService( $this );
			/* @noinspection PhpUndefinedFunctionInspection */
			add_action( 'admin_notices', [ $this, 'display_status_change_error' ] );
		}
	}

	/**
	 * Uses a function (wc_get_order) from WooCommerce
	 */
	public static function update_status( $id, $new_status, $status_note = null ) {
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_get_order( $id );

		if ( is_object( $order ) ) {
			$order->set_status( $new_status, $status_note );
			$order->save();
		}
	}

	/**
	 * Uses a function (wp_enqueue_style) from WordPress
	 */
	public function init_power_board_order_buttons( $order ) {
		$order_status = $order->get_status();
		$total_refund = $order->get_total_refunded();
		$order_total  = (float) $order->get_total( false );
		if ( $order_total === $total_refund ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_style(
				'hide-refund-button-styles',
				POWER_BOARD_PLUGIN_URL . 'assets/css/admin/hide-refund-button.css',
				[],
				POWER_BOARD_PLUGIN_VERSION,
				'all'
			);
		}

		if ( $order_status === 'on-hold' ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_style(
				'hide-on-hold-buttons',
				POWER_BOARD_PLUGIN_URL . 'assets/css/admin/hide-on-hold-buttons.css',
				[],
				POWER_BOARD_PLUGIN_VERSION,
				'all'
			);
		}
	}

	/**
	 * Checks if status change is allowed
	 * Uses functions (__, set_transient, get_current_user_id and esc_html) from WordPress
	 * Uses a function (wc_get_order_status_name) from WooCommerce
	 *
	 * @throws Exception If status change is not allowed
	 */
	public function status_change_verification( $order_id, $old_status_key, $new_status_key, $order ) {
		$order->update_meta_data( 'status_change_verification_failed', '' );
		if ( ( $old_status_key === $new_status_key ) || ! empty( $GLOBALS['power_board_is_updating_order_status'] ) || $order_id === null ) {
			return;
		}
		$statuses_rules = [
			'processing' => [ 'refunded', 'cancelled', 'failed', 'pending', 'completed' ],
			'refunded'   => [ 'cancelled', 'failed', 'refunded' ],
			'cancelled'  => [ 'failed', 'cancelled' ],
		];
		if ( ! empty( $statuses_rules[ $old_status_key ] ) ) {
			if ( ! in_array( $new_status_key, $statuses_rules[ $old_status_key ] ) ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				$new_status_name = wc_get_order_status_name( $new_status_key );
				/* @noinspection PhpUndefinedFunctionInspection */
				$old_status_name = wc_get_order_status_name( $old_status_key );
				/* @noinspection PhpUndefinedFunctionInspection */
				$error = sprintf(
					__( 'You can not change status from "%1$s"  to "%2$s"', 'power-board' ),
					$old_status_name,
					$new_status_name
				);
				$GLOBALS['power_board_is_updating_order_status'] = true;
				$order->update_meta_data( 'status_change_verification_failed', 1 );
				$order->update_status( $old_status_key, $error );
				/* @noinspection PhpUndefinedFunctionInspection */
				set_transient( 'power_board_status_change_error_' . get_current_user_id(), $error, 300 );
				unset( $GLOBALS['power_board_is_updating_order_status'] );
				/* @noinspection PhpUndefinedFunctionInspection */
				throw new Exception( esc_html( $error ) );
			}
		}
	}

	/**
	 * Uses functions (get_transient, get_current_user_id, esc_html and delete_transient) from WordPress
	 */
	public function display_status_change_error() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$error_message = get_transient( 'power_board_status_change_error_' . get_current_user_id() );
		if ( $error_message ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			echo '<div id="power-board-error-message" class="notice notice-error is-dismissible"><p>' . esc_html( $error_message ) . '</p></div>';
			echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#message.updated.notice.notice-success").hide();
			});
			</script>';
			/* @noinspection PhpUndefinedFunctionInspection */
			delete_transient( 'power_board_status_change_error_' . get_current_user_id() );
		}
	}
}
