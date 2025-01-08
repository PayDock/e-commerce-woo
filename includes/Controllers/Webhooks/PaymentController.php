<?php

namespace PowerBoard\Controllers\Webhooks;

use Exception;
use PowerBoard\Enums\ChargeStatuses;
use PowerBoard\Enums\NotificationEvents;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\SDKAdapterService;
use WP_Error;

class PaymentController {

	public function capture_payment() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'power-board' ) ) );

			return;
		}

		$wp_nonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wp_nonce, 'capture-or-cancel' ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: Security check', 'power-board' ) ) );

			return;
		}

		$order_id = ! empty( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : null;
		$error    = null;
		if ( ! $order_id ) {
			$error = __( 'The order is not found.', 'power-board' );
		} else {
			$order = wc_get_order( $order_id );
		}

		if ( is_object( $order ) ) {
			$order->calculate_totals();
		}

		$amount = wc_format_decimal( $_POST['amount'] );

		$logger_repository     = new LogRepository();
		$power_board_charge_id = $order->get_meta( 'power_board_charge_id' );
		if ( ! $error ) {
			$charge = SDKAdapterService::get_instance()->capture(
				array(
					'charge_id' => $power_board_charge_id,
					'amount'    => $amount,
				)
			);
			if ( ! empty( $charge['resource']['data']['status'] ) && 'complete' === $charge['resource']['data']['status'] ) {
				$new_charge_id = $charge['resource']['data']['_id'];
				$new_status    = 'processing';
				$logger_repository->createLogRecord(
					$new_charge_id,
					'Capture',
					$new_status,
					'',
					LogRepository::SUCCESS
				);
				$order->update_meta_data( 'capture_amount', $amount );
				$order->update_meta_data( 'power_board_charge_id', $new_charge_id );
				$order->update_meta_data( 'pb_directly_charged', 1 );
				$order->payment_complete();
				$order->save();

				OrderService::update_status( $order_id, $new_status );
				wp_send_json_success(
					array(
						'message' => __( 'The capture process was successful.', 'power-board' ),
					)
				);
			} elseif ( ! empty( $charge['error'] ) ) {
				if ( is_array( $charge['error'] ) ) {
					$charge['error'] = wp_json_encode( $charge['error'] );
				}
					$error = $charge['error'];
			} else {
				$error = __( 'The capture process has failed; please try again.', 'power-board' );
			}
		}
		if ( $error ) {
			$logger_repository->createLogRecord( $power_board_charge_id, 'Capture', 'error', $error, LogRepository::ERROR );
			wp_send_json_error( array( 'message' => $error ) );
		}
	}

	public function cancel_authorised() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'power-board' ) ) );

			return;
		}

		$wp_nonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wp_nonce, 'capture-or-cancel' ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: Security check', 'power-board' ) ) );

			return;
		}

		$order_id = ! empty( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : null;
		$error    = null;
		$order    = wc_get_order( $order_id );
		if ( ! $order ) {
			$error = __( 'The order is not found.', 'power-board' );
		}
		$logger_repository     = new LogRepository();
		$power_board_charge_id = $order->get_meta( 'power_board_charge_id' );
		if ( ! $error ) {
			$result = SDKAdapterService::get_instance()->cancel_authorised( array( 'charge_id' => $power_board_charge_id ) );

			if ( ! empty( $result['resource']['data']['status'] ) && 'cancelled' === $result['resource']['data']['status'] ) {
				$logger_repository->createLogRecord(
					$power_board_charge_id,
					'Cancel-authorised',
					'cancelled',
					'',
					LogRepository::SUCCESS
				);
				$order->payment_complete();
				OrderService::update_status( $order_id, 'cancelled' );
				wp_send_json_success(
					array( 'message' => __( 'The payment has been cancelled successfully. ', 'power-board' ) )
				);
			} elseif ( ! empty( $result['error'] ) ) {
				if ( is_array( $result['error'] ) ) {
					$result['error'] = wp_json_encode( $result['error'] );
				}
					$error = $result['error'];
			} else {
				$error = __( 'The payment cancellation process has failed. Please try again.', 'power-board' );
			}
		}
		if ( $error ) {
			$logger_repository->createLogRecord(
				$power_board_charge_id,
				'Cancel-authorised',
				'error',
				$error,
				LogRepository::ERROR
			);
			wp_send_json_error( array( 'message' => $error ) );
		}
	}

	public function refund_process( $refund, $args ) {
		if ( ! empty( $args['from_webhook'] ) && true === $args['from_webhook'] ) {
			return;
		}

		$order_id = $args['order_id'];
		$order    = wc_get_order( $order_id );

		if ( empty( $args['amount'] ) && is_object( $order ) ) {
			$amount = $order->get_total();
		} else {
			$amount = $args['amount'];
		}

		$capture_amount = (float) $order->get_meta( 'capture_amount' );
		$total_refunded = (float) $order->get_total_refunded();

		if ( ! in_array(
			$order->get_status(),
			array(
				'processing',
				'refunded',
				'completed',
			),
			true
		) || ( false === strpos( $order->get_payment_method(), PLUGIN_PREFIX ) ) ) {
			return;
		}

		$logger_repository = new LogRepository();

		$power_board_charge_id = $order->get_meta( 'power_board_charge_id' );
		if ( $capture_amount && $total_refunded > $capture_amount ) {
			$total_refunded = $capture_amount;
		}

		$directly_charged = $order->get_meta( 'pb_directly_charged' );
		if ( 'edit_order' === $_POST['action'] ) {
			$amount_to_refund = ! $directly_charged && $capture_amount <= $amount ? ( $capture_amount * 100 - $total_refunded * 100 ) / 100 : $amount;
			$refund->set_amount( $amount_to_refund );
			$refund->set_total( $amount_to_refund * -1 );
		} else {
			$amount_to_refund = $amount;
		}

		if ( 'edit_order' === $_POST['action'] && $order->get_meta( 'status_change_verification_failed' ) ) {
			$refund->set_amount( 0 );
			$refund->set_total( 0 );
			$refund->set_parent_id( 0 );
			return;
		}

		$result = SDKAdapterService::get_instance()->refunds(
			array(
				'charge_id' => $power_board_charge_id,
				'amount'    => $amount_to_refund,
			)
		);
		if ( ! empty( $result['resource']['data']['status'] ) && in_array(
			$result['resource']['data']['status'],
			array( 'refunded', 'refund_requested' ),
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

		if ( ( null === $input && json_last_error() !== JSON_ERROR_NONE ) || empty( $input['event'] ) ) {
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
				case NotificationEvents::TRANSACTION_SUCCESS()->name:
				case NotificationEvents::TRANSACTION_FAILURE()->name:
					$result = $this->webhook_process( $input );
					break;
				case NotificationEvents::REFUND_SUCCESS()->name:
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

		if ( false === $order || 'checkout-draft' === $order->get_status() || ! $charge_id || $charge_id !== $order->get_meta( 'power_board_charge_id' ) ) {
			return false;
		}

		$status           = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$operation        = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
		$is_authorization = $data['authorization'] ?? 0;
		$order_total      = $order->get_total();

		switch ( strtoupper( $status ) ) {
			case ChargeStatuses::COMPLETE()->name:
				$capture_amount = wc_format_decimal( $data['transaction']['amount'] );
				$order_status   = 'processing';
				$order->update_meta_data( 'capture_amount', $capture_amount );
				$order->save();
				break;
			case ChargeStatuses::PENDING()->name:
			case ChargeStatuses::PRE_AUTHENTICATION_PENDING()->name:
				$order_status = $is_authorization ? 'on-hold' : 'pending';
				break;
			case ChargeStatuses::CANCELLED()->name:
				$order_status = 'cancelled';
				break;
			case ChargeStatuses::REFUNDED()->name:
				$order_status = 'refunded';
				break;
			case ChargeStatuses::REQUESTED()->name:
				$order_status = 'processing';
				break;
			case ChargeStatuses::DECLINED()->name:
			case ChargeStatuses::FAILED()->name:
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
				array( 'processing', 'on-hold', 'pending' ),
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

		if ( false === $order || $order->get_meta( 'api_refunded_id' ) === $data['transaction']['_id'] || ! $charge_id || $charge_id !== $order->get_meta( 'power_board_charge_id' ) ) {
			return false;
		}

		$order_total    = $order->get_total();
		$capture_amount = $order->get_meta( 'capture_amount' );

		$status        = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$operation     = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
		$refund_amount = wc_format_decimal( $data['transaction']['amount'] );

		switch ( strtoupper( $status ) ) {
			case ChargeStatuses::REFUNDED()->name:
			case ChargeStatuses::REFUND_REQUESTED()->name:
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
			array(
				'amount'         => $refund_amount,
				'reason'         => __( 'The refund', 'power-board' ) . " {$refund_amount} " . __(
					'has been successfully.',
					'power-board'
				),
				'order_id'       => $order_id,
				'refund_payment' => false,
				'from_webhook'   => true,
			)
		);

		$logger_repository = new LogRepository();
		$logger_repository->createLogRecord(
			$charge_id,
			$operation,
			$order_status,
			$result instanceof WP_Error ? $result->get_error_message() : '',
			in_array(
				$order_status,
				array( 'processing', 'on-hold', 'pending' ),
				true
			) ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return true;
	}
}
