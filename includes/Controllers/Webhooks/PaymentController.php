<?php
/**
 * This file uses classes from WordPress
 *
 * @noinspection PhpUndefinedClassInspection
 */

namespace PowerBoard\Controllers\Webhooks;

use Exception;
use PowerBoard\Enums\ChargeStatusesEnum;
use PowerBoard\Enums\NotificationEventsEnum;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\SDKAdapterService;

class PaymentController {

	/**
	 * Handles refund process on PowerBoard
	 * Uses functions (sanitize_text_field, __, remove_action and esc_html) from WordPress
	 * Uses a function (wc_get_order) from WooCommerce
	 *
	 * @throws Exception If is refund has failed
	 */
	public function refund_process( $refund, $args ) {
		if ( ! empty( $args['from_webhook'] ) && $args['from_webhook'] === true ) {
			return;
		}

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
		) || ( strpos( $order->get_payment_method(), PLUGIN_PREFIX ) ) === false ) {
			return;
		}

		$power_board_charge_id = $order->get_meta( 'power_board_charge_id' );

		/* @noinspection PhpUndefinedFunctionInspection */

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : '';

		$amount_to_refund = $amount;
		if ( $action === 'edit_order' ) {
			$refund->set_amount( $amount_to_refund );
			$refund->set_total( $amount_to_refund * -1 );

		}

		if ( $action === 'edit_order' && $order->get_meta( 'status_change_verification_failed' ) ) {
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

			$order->update_meta_data( 'api_refunded_id', $new_refunded_id );
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

	/**
	 * Uses a function (remove_action) from WordPress
	 * Uses a function (wc_get_order) from WooCommerce
	 */
	public function after_refund_process( $order_id ) {
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_get_order( $order_id );

		if ( is_object( $order ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
			OrderService::update_status( $order_id, 'refunded' );
			$order->save();
		}
	}

	public function webhook(): void {
		$input = json_decode( file_get_contents( 'php://input' ), true );

		if ( ( $input === null && json_last_error() !== JSON_ERROR_NONE ) || empty( $input['event'] ) ) {
			return;
		}

		$result = false;
		if ( ! empty( $input['data']['reference'] ) ) {
			switch ( strtoupper( $input['event'] ) ) {
				case NotificationEventsEnum::TRANSACTION_SUCCESS:
				case NotificationEventsEnum::TRANSACTION_FAILURE:
					$result = $this->webhook_process( $input );
					break;
				case NotificationEventsEnum::REFUND_SUCCESS:
					$result = $this->refund_success_process( $input );
					break;
			}
		}

		echo $result ? 'Ok' : 'Fail';

		exit;
	}

	/**
	 * Uses a function (wc_get_order) from WooCommerce
	 */
	private function webhook_process( array $input ): bool {
		$data     = $input['data'];
		$order_id = $this->get_order_id( $data['reference'] );

		/* @noinspection PhpUndefinedFunctionInspection */
		$order     = wc_get_order( $order_id );
		$charge_id = $data['_id'] ?? '';

		if ( $order === false || $order->get_status() === 'checkout-draft' || ! $charge_id || $charge_id !== $order->get_meta( 'power_board_charge_id' ) ) {
			return false;
		}

		$status           = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$is_authorization = $data['authorization'] ?? 0;

		switch ( strtoupper( $status ) ) {
			case ChargeStatusesEnum::COMPLETE:
				$order_status = 'processing';
				$order->save();
				break;
			case ChargeStatusesEnum::PENDING:
			case ChargeStatusesEnum::PRE_AUTHENTICATION_PENDING:
				$order_status = $is_authorization ? 'on-hold' : 'pending';
				break;
			case ChargeStatusesEnum::CANCELLED:
				$order_status = 'cancelled';
				break;
			case ChargeStatusesEnum::REFUNDED:
				$order_status = 'refunded';
				break;
			case ChargeStatusesEnum::REQUESTED:
				$order_status = 'processing';
				break;
			case ChargeStatusesEnum::DECLINED:
			case ChargeStatusesEnum::FAILED:
				$order_status = 'failed';
				break;
			default:
				$order_status = $order->get_status();
		}

		OrderService::update_status( $order_id, $order_status );
		$order->update_meta_data( 'power_board_charge_id', $charge_id );
		$order->save();

		return true;
	}

	/**
	 * Uses a function (__) from WordPress
	 * Uses a function (wc_get_order, wc_format_decimal and wc_create_refund) from WooCommerce
	 */
	private function refund_success_process( array $input ): bool {
		sleep( 2 );

		$data = $input['data'];

		if ( empty( $data['transaction'] ) ) {
			return false;
		}
		$order_id = $this->get_order_id( $data['reference'] );

		/* @noinspection PhpUndefinedFunctionInspection */
		$order     = wc_get_order( $order_id );
		$charge_id = $data['_id'] ?? '';

		if ( $order === false || $order->get_meta( 'api_refunded_id' ) === $data['transaction']['_id'] || ! $charge_id || $charge_id !== $order->get_meta( 'power_board_charge_id' ) ) {
			return false;
		}

		$status = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		/* @noinspection PhpUndefinedFunctionInspection */
		$refund_amount = wc_format_decimal( $data['transaction']['amount'] );

		switch ( strtoupper( $status ) ) {
			case ChargeStatusesEnum::REFUNDED:
			case ChargeStatusesEnum::REFUND_REQUESTED:
				$order_status = 'refunded';
				break;
			default:
				$order_status = $order->get_status();
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$status_notes = __( 'The refund of', 'power-board' )
						. " $refund_amount "
						. __( 'has been successfully processed.', 'power-board' );
		$order->payment_complete();
		OrderService::update_status( $order_id, $order_status, $status_notes );

		try {
			/* @noinspection PhpUndefinedFunctionInspection */
			wc_create_refund(
				[
					'amount'         => $refund_amount,
					'reason'         => __( 'The refund of', 'power-board' ) . " $refund_amount " . __(
							'has been successfully processed.',
							'power-board'
						),
					'order_id'       => $order_id,
					'refund_payment' => false,
					'from_webhook'   => true,
				]
			);
		} catch ( Exception $error ) {
			return false;
		}

		return true;
	}

	private function get_order_id( string $reference ): int {
		if ( strpos( $reference, '_' ) === false ) {
			return (int) $reference;
		} else {
			$reference_array = explode( '_', $reference );
			return (int) reset( $reference_array );
		}
	}
}
