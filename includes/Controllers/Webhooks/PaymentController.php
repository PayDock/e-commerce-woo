<?php
declare( strict_types=1 );

namespace PowerBoard\Controllers\Webhooks;

use Exception;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\SDKAdapterService;

class PaymentController {
	/**
	 * Handles refund process on PowerBoard
	 * Uses functions (sanitize_text_field, __, remove_action and esc_html) from WordPress
	 * Uses a function (wc_get_order) from WooCommerce
	 * phpcs:disable WordPress.Security.NonceVerification -- processed through the WooCommerce form handler
	 *
	 * @throws Exception If is refund has failed
	 */
	public function refund_process( $refund, $args ): void {
		$order_id = $args['order_id'];
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_get_order( $order_id );

		if ( empty( $args['amount'] ) && is_object( $order ) ) {
			$amount = $order->get_total();
		} else {
			$amount = $args['amount'];
		}

		if ( ! in_array(
			$order->get_status(),
			[
				'processing',
				'refunded',
				'completed',
			],
			true
		) || ( strpos( $order->get_payment_method(), POWER_BOARD_PLUGIN_PREFIX ) ) === false ) {
			return;
		}

		$power_board_charge_id = $order->get_meta( '_power_board_charge_id' );

		/* @noinspection PhpUndefinedFunctionInspection */

		$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

		$amount_to_refund = $amount;
		if ( $action === 'edit_order' ) {
			$refund->set_amount( $amount_to_refund );
			$refund->set_total( $amount_to_refund * -1 );

		}

		if ( $action === 'edit_order' && $order->get_meta( '_status_change_verification_failed' ) ) {
			$refund->set_amount( 0 );
			$refund->set_total( 0 );
			$refund->set_parent_id( 0 );
			return;
		}

		$result = SDKAdapterService::get_instance()->refunds(
			[
				'charge_id' => $power_board_charge_id,
				'amount'    => $amount_to_refund,
			]
		);

		if ( ! empty( $result['resource']['data']['status'] ) && in_array(
			$result['resource']['data']['status'],
			[ 'refunded', 'refund_requested' ],
			true
		) ) {
			$new_refunded_id = end( $result['resource']['data']['transactions'] )['_id'];

			$status = 'refunded';

			/* @noinspection PhpUndefinedFunctionInspection */
			$status_note = __( 'The refund of', 'power-board' )
							. " $amount_to_refund "
							. __( 'has been successfully processed.', 'power-board' );

			$order->payment_complete();

			/* @noinspection PhpUndefinedFunctionInspection */
			remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
			if ( $order->get_status() === $status ) {
				$order->add_order_note( $status_note );
			} else {
				OrderService::update_status( $order_id, $status, $status_note );
			}

			$order->save();
		} elseif ( ! empty( $result['error'] ) ) {
			if ( is_array( $result['error'] ) ) {
				$result['error'] = implode( '; ', $result['error'] );
			}
			/* @noinspection PhpUndefinedFunctionInspection */
			throw new Exception( esc_html( $result['error'] ) );
		} else {
			/* @noinspection PhpUndefinedFunctionInspection */
			$error = __( 'The refund process has failed; please try again.', 'power-board' );

			/* @noinspection PhpUndefinedFunctionInspection */
			throw new Exception( esc_html( $error ) );
		}
	}
	// phpcs:enable

	/**
	 * Uses a function (remove_action) from WordPress
	 * Uses a function (wc_get_order) from WooCommerce
	 */
	public function after_refund_process( $order_id ): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();

		if ( is_object( $order ) && $order_status !== 'refunded' ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
			OrderService::update_status( $order_id, 'refunded' );
			$order->save();
		}
	}
}
