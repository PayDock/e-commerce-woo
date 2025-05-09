<?php
declare( strict_types=1 );

namespace WooPlugin\Controllers\Integrations;

use Exception;
use WooPlugin\Services\OrderService;
use WooPlugin\Services\SDKAdapterService;

class PaymentController {
	/**
	 * Handles refund process
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

		if ( ! $order || strpos( $order->get_payment_method(), PLUGIN_PREFIX ) === false ) {
			return;
		}

		$charge_id = $order->get_meta( '_' . PLUGIN_PREFIX . '_charge_id' );
		if ( empty( $charge_id ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$error = __( 'Unable to process refund. The payment for this order was not successfully completed.', PLUGIN_TEXT_DOMAIN );
			/* @noinspection PhpUndefinedFunctionInspection */
			throw new Exception( esc_html( $error ) );
		}

		if ( isset( $args['amount'] ) && $args['amount'] <= 0 ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$error = __( 'Please enter a valid amount and/or stock quantity to process a refund', PLUGIN_TEXT_DOMAIN );
			/* @noinspection PhpUndefinedFunctionInspection */
			throw new Exception( esc_html( $error ) );
		}

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
				'cancelled',
			],
			true
		) ) {
			return;
		}

		/* @noinspection PhpUndefinedFunctionInspection */

		$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

		$amount_to_refund = round( (float) $amount, 2 );
		if ( ( $action === 'edit_order' || $action === 'editpost' ) ) {
			if ( $order->get_meta( '_status_change_verification_failed' ) === '1' ) {
				$refund->set_amount( 0 );
				$refund->set_total( 0 );
				$refund->set_parent_id( 0 );
				return;
			}

			$refund->set_amount( $amount_to_refund );
			$refund->set_total( $amount_to_refund * -1 );
		}

		$result = SDKAdapterService::get_instance()->refunds(
			[
				'charge_id' => $charge_id,
				'amount'    => $amount_to_refund,
			]
		);

		if ( ! empty( $result['resource']['data']['status'] ) && in_array(
			$result['resource']['data']['status'],
			[ 'refunded', 'refund_requested' ],
			true
		) ) {
			$status = 'refunded';

			/* @noinspection PhpUndefinedFunctionInspection */
			$status_note = __( 'The refund of', PLUGIN_TEXT_DOMAIN )
							. " $amount_to_refund "
							. __( 'has been successfully processed.', PLUGIN_TEXT_DOMAIN );

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
				if (
					$result['error']['code'] === 'UnfulfilledCondition'
					&& $result['error']['details']['path'] === 'status'
					&& strpos( $result['error']['message'], 'refund request' ) !== false
				) {
					/* @noinspection PhpUndefinedFunctionInspection */
					$result['error'] = __( 'The previous refund is not yet finished, please try again later', PLUGIN_TEXT_DOMAIN );
				} else {
					$result['error'] = implode( '; ', $result['error'] );
				}
			}
			$order->add_order_note( PLUGIN_TEXT_NAME . ' refund failed: ' . $result['error'] );
			/* @noinspection PhpUndefinedFunctionInspection */
			throw new Exception( esc_html( $result['error'] ) );
		} else {
			/* @noinspection PhpUndefinedFunctionInspection */
			$error = __( 'The refund process has failed; please try again.', PLUGIN_TEXT_DOMAIN );

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
		$order = wc_get_order( $order_id );

		if ( ! $order || strpos( $order->get_payment_method(), PLUGIN_PREFIX ) === false ) {
			return;
		}

		$order_status = $order->get_status();

		if ( is_object( $order ) && $order_status !== 'refunded' ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
			OrderService::update_status( $order_id, 'refunded' );
			$order->save();
		}
	}
}
