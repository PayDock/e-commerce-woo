<?php

namespace PowerBoard\Controllers\Webhooks;

use Exception;
use PowerBoard\Enums\ChargeStatusesEnum;
use PowerBoard\Enums\NotificationEventsEnum;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\SDKAdapterService;
use WP_Error;

class PaymentController {
	/**
	 * Handles refund process on PowerBoard
	 *
	 * @throws Exception If is refund has failed
	 */
	public function refund_process( $refund, $args ) {
		if ( ! empty( $args['from_webhook'] ) && $args['from_webhook'] === true ) {
			return;
		}

		$order_id = $args['order_id'];
		$order    = wc_get_order( $order_id );

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

		$logger_repository = new LogRepository();

		$power_board_charge_id = $order->get_meta( 'power_board_charge_id' );

		$action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';

		if ( $action === 'edit_order' ) {
			$amount_to_refund = $amount;

			$refund->set_amount( $amount_to_refund );
			$refund->set_total( $amount_to_refund * -1 );

		} else {
			$amount_to_refund = $amount;
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

			$order->update_meta_data( 'power_board_refunded_status', $status );
			$status_note = __( 'The refund', 'power-board' )
							. " {$amount_to_refund} "
							. __( 'has been successfully.', 'power-board' );

			$order->payment_complete();

			remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
			OrderService::update_status( $order_id, $status, $status_note );

			$order->update_meta_data( 'api_refunded_id', $new_refunded_id );
			$order->save();

			$logger_repository->createLogRecord( $new_refunded_id, 'Refunded', $status, '', LogRepository::SUCCESS );
		} elseif ( ! empty( $result['error'] ) ) {
			if ( is_array( $result['error'] ) ) {
				$result['error'] = implode( '; ', $result['error'] );
			}
			$logger_repository->createLogRecord(
				$power_board_charge_id,
				'Refund',
				'error',
				$result['error'],
				LogRepository::ERROR
			);
			throw new Exception( esc_html( $result['error'] ) );
		} else {
			$error = __( 'The refund process has failed; please try again.', 'power-board' );
			$logger_repository->createLogRecord(
				$power_board_charge_id,
				'Refunded',
				'error',
				$error,
				LogRepository::ERROR
			);
			throw new Exception( esc_html( $error ) );
		}
	}

	public function after_refund_process( $order_id, $refundId ) {
		$order = wc_get_order( $order_id );

		if ( is_object( $order ) ) {

			$power_board_refunded_status = $order->get_meta( 'power_board_refunded_status' );
			if ( $power_board_refunded_status ) {
				remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
				OrderService::update_status( $order_id, $power_board_refunded_status );
				$order->update_meta_data( 'power_board_refunded_status', '' );
				$order->save();
			}
		}
	}

	public function webhook(): void {
		$input = json_decode( file_get_contents( 'php://input' ), true );

		if ( ( $input === null && json_last_error() !== JSON_ERROR_NONE ) || empty( $input['event'] ) ) {
			return;
		}

		( new LogRepository() )->createLogRecord(
			'',
			'Webhook',
			'Received',
			$input['event'],
			LogRepository::SUCCESS
		);

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

	private function webhook_process( array $input ): bool {
		$data = $input['data'];

		if ( strpos( $data['reference'], '_' ) === false ) {
			$order_id = (int) $data['reference'];
		} else {
			$reference_array = explode( '_', $data['reference'] );
			$order_id        = (int) reset( $reference_array );
		}

		$order     = wc_get_order( $order_id );
		$charge_id = $data['_id'] ?? '';

		if ( $order === false || $order->get_status() === 'checkout-draft' || ! $charge_id || $charge_id !== $order->get_meta( 'power_board_charge_id' ) ) {
			return false;
		}

		$status           = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$operation        = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
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

		$logger_repository = new LogRepository();
		$logger_repository->createLogRecord(
			$charge_id,
			$operation,
			$order_status,
			'',
			in_array(
				$order_status,
				[ 'processing', 'on-hold', 'pending' ],
				true
			) ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return true;
	}

	private function refund_success_process( array $input ): bool {
		sleep( 2 );

		$data = $input['data'];

		if ( empty( $data['transaction'] ) ) {
			return false;
		}

		if ( strpos( $data['reference'], '_' ) === false ) {
			$order_id = (int) $data['reference'];
		} else {
			$reference_array = explode( '_', $data['reference'] );
			$order_id        = (int) reset( $reference_array );
		}

		$order     = wc_get_order( $order_id );
		$charge_id = $data['_id'] ?? '';

		if ( $order === false || $order->get_meta( 'api_refunded_id' ) === $data['transaction']['_id'] || ! $charge_id || $charge_id !== $order->get_meta( 'power_board_charge_id' ) ) {
			return false;
		}

		$status        = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$operation     = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
		$refund_amount = wc_format_decimal( $data['transaction']['amount'] );

		switch ( strtoupper( $status ) ) {
			case ChargeStatusesEnum::REFUNDED:
			case ChargeStatusesEnum::REFUND_REQUESTED:
				$order_status = 'refunded';
				$order->update_meta_data( 'power_board_refunded_status', $order_status );
				$order->save();
				break;
			default:
				$order_status = $order->get_status();
		}

		$status_notes = __( 'The refund', 'power-board' )
						. " {$refund_amount} "
						. __( 'has been successfully.', 'power-board' );
		$order->payment_complete();
		OrderService::update_status( $order_id, $order_status, $status_notes );

		$result = wc_create_refund(
			[
				'amount'         => $refund_amount,
				'reason'         => __( 'The refund', 'power-board' ) . " {$refund_amount} " . __(
					'has been successfully.',
					'power-board'
				),
				'order_id'       => $order_id,
				'refund_payment' => false,
				'from_webhook'   => true,
			]
		);

		$logger_repository = new LogRepository();
		$logger_repository->createLogRecord(
			$charge_id,
			$operation,
			$order_status,
			$result instanceof WP_Error ? $result->get_error_message() : '',
			in_array(
				$order_status,
				[ 'processing', 'on-hold', 'pending' ],
				true
			) ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return true;
	}
}
