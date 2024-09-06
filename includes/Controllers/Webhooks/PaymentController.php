<?php

namespace PowerBoard\Controllers\Webhooks;

use PowerBoard\Enums\ChargeStatuses;
use PowerBoard\Enums\NotificationEvents;
use PowerBoard\Hooks\ActivationHook;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\SDKAdapterService;

class PaymentController {
	private $status_update_hooks = [];

	public function capturePayment() {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'capture-or-cancel' ) ) {
			wp_send_json_error( [ 'message' => __( 'Error: Security check', 'power-board' ) ] );

			return;
		}

		$orderId = ! empty( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : null;
		$error   = null;
		if ( ! $orderId ) {
			$error = __( 'The order is not found.', 'power-board'  );
		} else {
			$order = wc_get_order( $orderId );
			/*if ( ! in_array( $order->get_meta( ActivationHook::CUSTOM_STATUS_META_KEY ), [
				'pb-authorize',
				'wc-pb-authorize'
			] ) ) {
				$error = __( 'The order has been authorized and is awaiting approval.', 'power-board'  );
			}*/
		}

		if ( is_object( $order ) ) {
			$order->calculate_totals();
			$orderTotal = $order->get_total();
		} else {
			$orderTotal = false;
		}

		// if ( ! empty( $orderTotal ) ) {
		// 	$amount = $orderTotal;
		// } else {
			$amount = wc_format_decimal( $_POST['amount'] );
		// }

		$loggerRepository   = new LogRepository();
		$powerBoardChargeId = $order->get_meta( 'power_board_charge_id' );
		if ( ! $error ) {
			$charge = SDKAdapterService::getInstance()->capture( [
				'charge_id' => $powerBoardChargeId,
				'amount'    => $amount,
			] );
			if ( ! empty( $charge['resource']['data']['status'] ) && 'complete' == $charge['resource']['data']['status'] ) {
				$newChargeId = $charge['resource']['data']['_id'];
				$newStatus   = $orderTotal > $amount ? 'pb-p-paid' : 'pb-paid';
				$loggerRepository->createLogRecord(
					$newChargeId,
					'Capture',
					$newStatus,
					'',
					LogRepository::SUCCESS
				);
				$order->update_meta_data( 'capture_amount', $amount );
				$order->update_meta_data( 'power_board_charge_id', $newChargeId );
				$order->update_meta_data( 'pb_directly_charged', 1 );
				$order->payment_complete();
				$order->save();

				OrderService::updateStatus( $orderId, $newStatus );
				wp_send_json_success( [
					'message' => __( 'The capture process was successful.', 'woocommerce' ),
				] );
			} else {
				if ( ! empty( $result['error'] ) ) {
					if ( is_array( $result['error'] ) ) {
						$result['error'] = wp_json_encode( $result['error'] );
					}
					$error = $result['error'];
				} else {
					$error = __( 'The capture process has failed; please try again.', 'woocommerce' );
				}
			}
		}
		if ( $error ) {
			$loggerRepository->createLogRecord( $powerBoardChargeId, 'Capture', 'error', $error, LogRepository::ERROR );
			wp_send_json_error( [ 'message' => $error ] );
		}
	}

	public function cancelAuthorised() {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'capture-or-cancel' ) ) {
			wp_send_json_error( [ 'message' => __( 'Error: Security check', 'power-board' ) ] );

			return;
		}

		$orderId = ! empty( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : null;
		$error   = null;
		$order   = wc_get_order( $orderId );
		if ( ! $order ) {
			$error = __( 'The order is not found.', 'woocommerce' );
		}
		$loggerRepository   = new LogRepository();
		$powerBoardChargeId = $order->get_meta( 'power_board_charge_id' );
		if ( ! $error ) {
			$result = SDKAdapterService::getInstance()->cancelAuthorised( [ 'charge_id' => $powerBoardChargeId ] );

			if ( ! empty( $result['resource']['data']['status'] ) && 'cancelled' == $result['resource']['data']['status'] ) {
				$loggerRepository->createLogRecord(
					$powerBoardChargeId,
					'Cancel-authorised',
					'wc-pb-cancelled',
					'',
					LogRepository::SUCCESS
				);
				$order->payment_complete();
				OrderService::updateStatus( $orderId, 'pb-cancelled' );
				wp_send_json_success(
					[ 'message' => __( 'The payment has been cancelled successfully. ', 'woocommerce' ) ]
				);
			} else {
				if ( ! empty( $result['error'] ) ) {
					if ( is_array( $result['error'] ) ) {
						$result['error'] = wp_json_encode( $result['error'] );
					}
					$error = $result['error'];
				} else {
					$error = __( 'The payment cancellation process has failed. Please try again.', 'woocommerce' );
				}
			}
		}
		if ( $error ) {
			$loggerRepository->createLogRecord(
				$powerBoardChargeId,
				'Cancel-authorised',
				'error',
				$error,
				LogRepository::ERROR
			);
			wp_send_json_error( [ 'message' => $error ] );
		}
	}

	public function refundProcess( $refund, $args ) {
		if ( ! empty( $args['from_webhook'] ) && true === $args['from_webhook'] ) {
			return;
		}

		$orderId       = $args['order_id'];
		$order         = wc_get_order( $orderId );

		if ( empty($args['amount']) && is_object( $order ) ) {
			$amount = $order->get_total();
		} else {
			$amount = $args['amount'];
		}

		$captureAmount = (float) $order->get_meta( 'capture_amount' );

		$directlyCharged = $order->get_meta( 'pb_directly_charged' );

		$totalRefunded = (float) $order->get_total_refunded();

		$statusChangeVerificationFailed = $order->get_meta( 'status_change_verification_failed' );
		if ( $_POST['action'] === 'edit_order' && $statusChangeVerificationFailed ) {
			$refund->set_amount(0);
			$refund->set_total(0);
			$refund->set_parent_id( 0 );
			return;
		}

		if ( ! in_array( $order->get_status(), [
				'processing',
				'refunded'
			] ) || ( false === strpos( $order->get_payment_method(), PowerBoardPlugin::PLUGIN_PREFIX ) ) ) {
			return;
		}

		$loggerRepository = new LogRepository();

		$powerBoardChargeId = $order->get_meta( 'power_board_charge_id' );
		if ( $captureAmount && $totalRefunded > $captureAmount ) {
			$totalRefunded = $captureAmount;
		}

		if ($_POST['action'] === 'edit_order') {
			$amountToRefund =  !$directlyCharged && $captureAmount <= $amount ? ($captureAmount * 100 - $totalRefunded * 100) / 100 : $amount;
			$refund->set_amount($amountToRefund);
			$refund->set_total( $amountToRefund * -1 );
		} else {
			$amountToRefund = $amount;
		}

		$result = SDKAdapterService::getInstance()->refunds( [
			'charge_id' => $powerBoardChargeId,
			'amount'    => $amountToRefund,
		] );
		if ( ! empty( $result['resource']['data']['status'] ) && in_array(
				$result['resource']['data']['status'],
				[ 'refunded', 'refund_requested' ]
			) ) {
			$newRefundedId = end( $result['resource']['data']['transactions'] )['_id'];
			if ( $captureAmount ) {
				$status = $totalRefunded < $captureAmount ? 'wc-pb-p-refund' : 'wc-pb-refunded';
			} else {
				$status = $totalRefunded < $order->get_total() ? 'wc-pb-p-refund' : 'wc-pb-refunded';
			}

			$order->update_meta_data( 'power_board_refunded_status', $status );
			$status_note = __( 'The refund', 'woocommerce' )
			               . " {$amountToRefund} "
			               . __( 'has been successfully.', 'woocommerce' );

			$order->payment_complete();

			remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
			OrderService::updateStatus( $orderId, $status, $status_note );

			$order->update_meta_data( 'api_refunded_id', $newRefundedId );
			$order->save();

			$loggerRepository->createLogRecord( $newRefundedId, 'Refunded', $status, '', LogRepository::SUCCESS );
		} else {
			if ( ! empty( $result['error'] ) ) {
				if ( is_array( $result['error'] ) ) {
					$result['error'] = implode( '; ', $result['error'] );
				}
				$loggerRepository->createLogRecord(
					$powerBoardChargeId,
					'Refund',
					'error',
					$result['error'],
					LogRepository::ERROR
				);
				throw new \Exception( esc_html( $result['error'] ) );
			} else {
				$error = __( 'The refund process has failed; please try again.', 'woocommerce' );
				$loggerRepository->createLogRecord( $powerBoardChargeId, 'Refunded', 'error', $error,
					LogRepository::ERROR );
				throw new \Exception( esc_html( $error ) );
			}
		}
	}

	public function afterRefundProcess( $orderId, $refundId ) {

		$order = wc_get_order( $orderId );

		if ( is_object( $order ) ) {

			$powerBoardRefundedStatus = $order->get_meta( 'power_board_refunded_status' );
			if ( $powerBoardRefundedStatus ) {
				remove_action( 'woocommerce_order_status_refunded', 'wc_order_fully_refunded' );
				OrderService::updateStatus( $orderId, $powerBoardRefundedStatus );
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
				case NotificationEvents::FRAUD_CHECK_IN_REVIEW()->name:
				case NotificationEvents::FRAUD_CHECK_IN_REVIEW_ASYNC_APPROVED()->name:
				case NotificationEvents::FRAUD_CHECK_TRANSACTION_IN_REVIEW_ASYNC_APPROVED()->name:
				case NotificationEvents::FRAUD_CHECK_SUCCESS()->name:
				case NotificationEvents::FRAUD_CHECK_TRANSACTION_IN_REVIEW_APPROVED()->name:
				case NotificationEvents::FRAUD_CHECK_FAILED()->name:
				case NotificationEvents::FRAUD_CHECK_TRANSACTION_IN_REVIEW_DECLINED()->name:
					$result = $this->webhookProcess( $input );
					break;
				case NotificationEvents::STANDALONE_FRAUD_CHECK_SUCCESS()->name:
				case NotificationEvents::STANDALONE_FRAUD_CHECK_FAILED()->name:
				case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_APPROVED()->name:
				case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_DECLINED()->name:
				case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_ASYNC_APPROVED()->name:
				case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_ASYNC_DECLINED()->name:
					$result = $this->fraudProcess( $input );
					break;
				case NotificationEvents::REFUND_SUCCESS()->name:
					$result = $this->refundSuccessProcess( $input );
					break;
				default:
					$result = false;
			}
		}

		echo $result ? 'Ok' : 'Fail';

		exit;
	}

	private function webhookProcess( array $input ): bool {
		$data = $input['data'];

		if ( strpos( $data['reference'], '_' ) === false ) {
			$orderId = (int) $data['reference'];
		} else {
			$referenceArray = explode( '_', $data['reference'] );
			$orderId        = (int) reset( $referenceArray );
		}

		$order = wc_get_order( $orderId );

		if ( false === $order || 'checkout-draft' === $order->get_status() ) {
			return false;
		}

		$chargeId        = $data['_id'] ?? '';
		$status          = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
		$isAuthorization = $data['authorization'] ?? 0;
		$orderTotal      = $order->get_total();

		switch ( strtoupper( $status ) ) {
			case ChargeStatuses::COMPLETE()->name:
				$captureAmount = wc_format_decimal( $data['transaction']['amount'] );
				$orderStatus   = $orderTotal > $captureAmount ? 'wc-pb-p-paid' : 'wc-pb-paid';
				$order->update_meta_data( 'capture_amount', $captureAmount );
				$order->save();
				break;
			case ChargeStatuses::PENDING()->name:
			case ChargeStatuses::PRE_AUTHENTICATION_PENDING()->name:
				$orderStatus = $isAuthorization ? 'wc-pb-authorize' : 'wc-pb-pending';
				break;
			case ChargeStatuses::CANCELLED()->name:
				$orderStatus = 'wc-pb-cancelled';
				break;
			case ChargeStatuses::REFUNDED()->name:
				$orderStatus = 'wc-pb-refunded';
				break;
			case ChargeStatuses::REQUESTED()->name:
				$orderStatus = 'wc-pb-requested';
				break;
			case ChargeStatuses::DECLINED()->name:
			case ChargeStatuses::FAILED()->name:
				$orderStatus = 'wc-pb-failed';
				break;
			default:
				$orderStatus = $order->get_status();
		}

		OrderService::updateStatus( $orderId, $orderStatus );
		$order->update_meta_data( 'power_board_charge_id', $chargeId );
		$order->save();

		$loggerRepository = new LogRepository();
		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$orderStatus,
			'',
			in_array( $orderStatus, [ 'wc-pb-paid', 'wc-pb-p-paid', 'wc-pb-authorize', 'wc-pb-pending' ]
			) ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return true;
	}

	private function fraudProcess( array $input ): bool {
		$loggerRepository = new LogRepository();
		$data             = $input['data'];

		if ( strpos( $data['reference'], '_' ) === false ) {
			$orderId = (int) $data['reference'];
		} else {
			$referenceArray = explode( '_', $data['reference'] );
			$orderId        = (int) reset( $referenceArray );
		}

		$order       = wc_get_order( $orderId );
		$fraudId     = $data['_id'];
		$fraudStatus = $data['status'];

		$optionName = "power_board_fraud_{$orderId}";

		if ( 'complete' !== $fraudStatus ) {
			$operation = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
			$status    = 'wc-pb-failed';

			delete_option( $optionName );
			OrderService::updateStatus( $orderId, $status );

			$loggerRepository->createLogRecord(
				$fraudId,
				$operation,
				$status,
				''
			);

			return true;
		}

		$options = get_option( $optionName );

		if ( false === $options || false === $order ) {
			return false;
		}

		$paymentSource = $data['customer']['payment_source'];
		if ( ! empty( $options['gateway_id'] ) ) {
			$paymentSource['gateway_id'] = $options['gateway_id'];
		}

		$chargeArgs = [
			'amount'          => (float) $order->get_total(),
			'reference'       => (string) $order->get_id(),
			'currency'        => strtoupper( $order->get_currency() ),
			'customer'        => [
				'first_name'     => $order->get_billing_first_name(),
				'last_name'      => $order->get_billing_last_name(),
				'email'          => $order->get_billing_email(),
				'phone'          => $order->get_billing_phone(),
				'payment_source' => $paymentSource,
			],
			'fraud_charge_id' => $fraudId,
			'capture'         => $options['capture'],
		];

		if ( ! empty( $options['charge3dsid'] ) ) {
			$chargeArgs['_3ds_charge_id'] = $options['charge3dsid'];
		}

		if ( ! empty( $options['_3ds'] ) ) {
			$chargeArgs['_3ds'] = $options['_3ds'];
		}

		if ( ! empty( $options['cvv'] ) ) {
			$chargeArgs['customer']['payment_source']['card_ccv'] = $options['cvv'];
		}

		delete_option( $optionName );

		$response = SDKAdapterService::getInstance()->createCharge( $chargeArgs );
		$chargeId = ! empty( $response['resource']['data']['_id'] ) ? $response['resource']['data']['_id'] : '';

		if ( ! empty( $response['error'] ) ) {
			$message = SDKAdapterService::getInstance()->errorMessageToString( $response );
			$loggerRepository->createLogRecord(
				$chargeId ?? '',
				'Charge',
				'UnfulfilledCondition',
				__( 'Can\'t charge.', 'power-board' ) . $message,
				LogRepository::ERROR
			);

			return false;
		}

		if ( ! empty( $options['_3ds'] ) ) {
			$attachResponse = SDKAdapterService::getInstance()->fraudAttach( $chargeId,
				[ 'fraud_charge_id' => $fraudId ] );
			if ( ! empty( $attachResponse['error'] ) ) {
				$message = SDKAdapterService::getInstance()->errorMessageToString( $attachResponse );
				$loggerRepository->createLogRecord(
					$chargeId ?? '',
					'Fraud Attach',
					'UnfulfilledCondition',
					__( 'Can\'t fraud attach.', 'power-board' ) . $message,
					LogRepository::ERROR
				);

				return false;
			}
		}

		$status          = ucfirst( strtolower( $response['resource']['data']['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $response['resource']['data']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		$markAsSuccess   = false;

		if ( $isAuthorization && in_array( $status, [ 'Pending', 'Pre_authentication_pending' ] ) ) {
			$status = 'wc-pb-authorize';
		} else {
			$markAsSuccess = true;
			$isCompleted   = 'Complete' === $status;
			$status        = $isCompleted ? 'wc-pb-paid' : 'wc-pb-pending';
		}

		OrderService::updateStatus( $orderId, $status );
		$order->update_meta_data( 'power_board_charge_id', $chargeId );
		$order->save();

		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$status,
			'',
			$markAsSuccess ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return true;
	}

	private function refundSuccessProcess( array $input ): bool {
		sleep( 2 );

		$data = $input['data'];

		if ( empty( $data['transaction'] ) ) {
			return false;
		}

		if ( strpos( $data['reference'], '_' ) === false ) {
			$orderId = (int) $data['reference'];
		} else {
			$referenceArray = explode( '_', $data['reference'] );
			$orderId        = (int) reset( $referenceArray );
		}

		$order = wc_get_order( $orderId );

		if ( false === $order || $order->get_meta( 'api_refunded_id' ) === $data['transaction']['_id'] ) {
			return false;
		}

		$orderTotal    = $order->get_total();
		$captureAmount = $order->get_meta( 'capture_amount' );
		if ( $captureAmount && ( $orderTotal > $captureAmount ) ) {
			$orderTotal = $captureAmount;
		}

		$chargeId     = $data['_id'] ?? '';
		$status       = ucfirst( strtolower( $data['status'] ?? 'undefined' ) );
		$operation    = ucfirst( strtolower( $data['type'] ?? 'undefined' ) );
		$refundAmount = wc_format_decimal( $data['transaction']['amount'] );

		switch ( strtoupper( $status ) ) {
			case ChargeStatuses::REFUNDED()->name:
			case ChargeStatuses::REFUND_REQUESTED()->name:
				if ( $refundAmount < $orderTotal ) {
					$orderStatus = 'wc-pb-p-refund';
				} else {
					$orderStatus = 'wc-pb-refunded';
				}
				$order->update_meta_data( 'power_board_refunded_status', $orderStatus );
				$order->save();
				break;
			default:
				$orderStatus = $order->get_status();
		}

		$status_notes = __( 'The refund', 'woocommerce' )
		                . " {$refundAmount} "
		                . __( 'has been successfully.', 'woocommerce' );
		$order->payment_complete();
		OrderService::updateStatus( $orderId, $orderStatus, $status_notes );

		$result = wc_create_refund( [
			'amount'         => $refundAmount,
			'reason'         => __( 'The refund', 'woocommerce' ) . " {$refundAmount} " . __(
					'has been successfully.',
					'woocommerce'
				),
			'order_id'       => $orderId,
			'refund_payment' => false,
			'from_webhook'   => true,
		] );

		$loggerRepository = new LogRepository();
		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$orderStatus,
			$result instanceof \WP_Error ? $result->get_error_message() : '',
			in_array( $orderStatus, [ 'wc-pb-paid', 'wc-pb-authorize', 'wc-pb-pending' ]
			) ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return true;
	}
}
