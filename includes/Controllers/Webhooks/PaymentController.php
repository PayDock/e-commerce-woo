<?php

namespace PowerBoard\Controllers\Webhooks;


use PowerBoard\Enums\ChargeStatuses;
use PowerBoard\Enums\NotificationEvents;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\SDKAdapterService;

class PaymentController
{
    public function capturePayment()
    {
        $orderId = $_POST['order_id'] ?? null;
        $error = null;
        if (!$orderId) {
            $error = __('The order is not found.');
        } else {
            $order = wc_get_order($orderId);
            if ($order->get_status() != 'pb-authorize') {
                $error = __('The order should be have status "authorize"', 'woocommerce');
            }
        }
        $loggerRepository = new LogRepository();
        $powerBoardChargeId = get_post_meta($orderId, 'power_board_charge_id', true);
        if (!$error) {
            $charge = SDKAdapterService::getInstance()->capture(['charge_id' => $powerBoardChargeId]);
            if (!empty($charge['resource']['data']['status']) && $charge['resource']['data']['status'] == 'complete') {
                $newChargeId = $charge['resource']['data']['_id'];
                $loggerRepository->createLogRecord(
                    $newChargeId,
                    'Capture',
                    'wc-pb-paid',
                    '',
                    LogRepository::SUCCESS
                );
                update_post_meta($orderId, 'power_board_charge_id', $newChargeId);
                $order->set_status('wc-pb-paid');
                $order->payment_complete();
                $order->save();
                wp_send_json_success(['message' => __('The capture process has been successfully.', 'woocommerce')]);
            } else {
                if (!empty($result['error'])) {
                    if (is_array($result['error'])) {
                        $result['error'] = json_encode($result['error']);
                    }
                    $error = $result['error'];
                } else {
                    $error = __('The capture process has failed; please try again.', 'woocommerce');
                }
            }
        }
        if ($error) {
            $loggerRepository->createLogRecord($powerBoardChargeId, 'Capture', 'error', $error, LogRepository::ERROR);
            wp_send_json_error(['message' => $error]);
        }
    }

    public function cancelAuthorised()
    {
        $orderId = $_POST['order_id'] ?? null;
        $error = null;
        if (!$orderId || !($order = wc_get_order($orderId))) {
            $error = __('The order is not found.', 'woocommerce');
        }
        $loggerRepository = new LogRepository();
        $powerBoardChargeId = get_post_meta($orderId, 'power_board_charge_id', true);
        if (!$error) {
            $result = SDKAdapterService::getInstance()->cancelAuthorised(['charge_id' => $powerBoardChargeId]);

            if (!empty($result['resource']['data']['status']) && $result['resource']['data']['status'] == 'cancelled') {
                $loggerRepository->createLogRecord(
                    $powerBoardChargeId,
                    'Cancel-authorised',
                    'wc-pb-cancelled',
                    '',
                    LogRepository::SUCCESS
                );
                $order->set_status('wc-pb-cancelled');
                $order->payment_complete();
                $order->save();
                wp_send_json_success(
                    ['message' => __('The payment has been cancelled successfully. ', 'woocommerce')]
                );
            } else {
                if (!empty($result['error'])) {
                    if (is_array($result['error'])) {
                        $result['error'] = json_encode($result['error']);
                    }
                    $error = $result['error'];
                } else {
                    $error = __('The payment cancellation process has failed. Please try again.', 'woocommerce');
                }
            }
        }
        if ($error) {
            $loggerRepository->createLogRecord(
                $powerBoardChargeId,
                'Cancel-authorised',
                'error',
                $error,
                LogRepository::ERROR
            );
            wp_send_json_error(['message' => $error]);
        }
    }

    public function refundProcess($refund, $args)
    {
        $orderId = $args['order_id'];
        $amount = $args['amount'];
        $order = wc_get_order($orderId);

        if (!in_array($order->get_status(), ['pb-paid', 'pb-p-refund', 'wc-pb-refunded'])) {
            return;
        }

        $loggerRepository = new LogRepository();

        $totalRefunded = 0;
        $refunds = $order->get_refunds();
        foreach ($refunds as $refund) {
            $totalRefunded += $refund->get_amount();
        }
        $orderTotal = $order->get_total();
        $powerBoardChargeId = get_post_meta($orderId, 'power_board_charge_id', true);
        $result = SDKAdapterService::getInstance()->refunds(['charge_id' => $powerBoardChargeId, 'amount' => $amount]);
        if (!empty($result['resource']['data']['status']) && in_array(
                $result['resource']['data']['status'],
                ['refunded', 'refund_requested']
            )) {
            $newRefundedId = $result['resource']['data']['_id'];
            $status = $totalRefunded < $orderTotal ? 'wc-pb-p-refund' : 'wc-pb-refunded';
            update_post_meta($orderId, 'power_board_refunded_status', $status);
            $order->set_status($status);
            $order->update_status(
                $status,
                __('The refund', 'woocommerce')." {$amount} ".__('has been successfully.', 'woocommerce')
            );
            $order->payment_complete();
            $order->save();
            $loggerRepository->createLogRecord($newRefundedId, 'Refunded', $status, '', LogRepository::SUCCESS);
        } else {
            if (!empty($result['error'])) {
                if (is_array($result['error'])) {
                    $result['error'] = json_encode($result['error']);
                }
                $loggerRepository->createLogRecord(
                    $powerBoardChargeId,
                    'Refund',
                    'error',
                    $result['error'],
                    LogRepository::ERROR
                );
                throw new \Exception($result['error']);
            } else {
                $error = __('The refund process has failed; please try again.', 'woocommerce');
                $loggerRepository->createLogRecord($powerBoardChargeId, 'Refunded', 'error', $error, LogRepository::ERROR);
                throw new \Exception($error);
            }
        }
    }

    public function afterRefundProcess($orderId, $refundId)
    {
        $powerBoardRefundedStatus = get_post_meta($orderId, 'power_board_refunded_status', true);
        if ($powerBoardRefundedStatus) {
            $order = wc_get_order($orderId);
            $order->update_status($powerBoardRefundedStatus);
            delete_post_meta($orderId, 'power_board_refunded_status');
        }
    }

    public function webhook(): void
    {
        $input = json_decode(file_get_contents("php://input"), true);

        if (($input === null && json_last_error() !== JSON_ERROR_NONE) || empty($input['event'])) {
            return;
        }

        (new LogRepository())->createLogRecord(
            '',
            'Webhook',
            'Received',
            $input['event'],
            LogRepository::SUCCESS
        );

        $result = false;
        if (!empty($input['data']['reference'])) {
            switch (strtoupper($input['event'])) {
                case NotificationEvents::TRANSACTION_SUCCESS()->name:
                case NotificationEvents::TRANSACTION_FAILURE()->name:
                case NotificationEvents::FRAUD_CHECK_IN_REVIEW()->name:
                case NotificationEvents::FRAUD_CHECK_IN_REVIEW_ASYNC_APPROVED()->name:
                case NotificationEvents::FRAUD_CHECK_TRANSACTION_IN_REVIEW_ASYNC_APPROVED()->name:
                case NotificationEvents::FRAUD_CHECK_SUCCESS()->name:
                case NotificationEvents::FRAUD_CHECK_TRANSACTION_IN_REVIEW_APPROVED()->name:
                case NotificationEvents::FRAUD_CHECK_FAILED()->name:
                case NotificationEvents::FRAUD_CHECK_TRANSACTION_IN_REVIEW_DECLINED()->name:
                    $result = $this->webhookProcess($input);
                    break;
                case NotificationEvents::STANDALONE_FRAUD_CHECK_SUCCESS()->name:
                case NotificationEvents::STANDALONE_FRAUD_CHECK_FAILED()->name:
                case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_APPROVED()->name:
                case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_DECLINED()->name:
                case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_ASYNC_APPROVED()->name:
                case NotificationEvents::STANDALONE_FRAUD_CHECK_IN_REVIEW_ASYNC_DECLINED()->name:
                    $result = $this->fraudProcess($input);
                    break;
                case NotificationEvents::REFUND_SUCCESS()->name:
                    $result = $this->refundSuccessProcess($input);
                    break;
                default:
                    $result = false;
            }
        }

        echo $result ? 'Ok' : 'Fail';

        exit;
    }

    private function webhookProcess(array $input): bool
    {
        $data = $input['data'];

        if (strpos($data['reference'], '_') === false) {
            $orderId = (int) $data['reference'];
        } else {
            $referenceArray = explode('_', $data['reference']);
            $orderId = (int) reset($referenceArray);
        }

        $order = wc_get_order($orderId);

        if ($order === false) {
            return false;
        }

        $chargeId = $data['_id'] ?? '';
        $status = ucfirst(strtolower($data['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($data['type'] ?? 'undefined'));
        $isAuthorization = $data['authorization'] ?? 0;

        switch (strtoupper($status)) {
            case ChargeStatuses::COMPLETE()->name:
                $orderStatus = 'wc-pb-paid';
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
            case ChargeStatuses::FAILED()->name:
                $orderStatus = 'wc-pb-failed';
                break;
            default:
                $orderStatus = $order->get_status();
        }

        $order->set_status($orderStatus);
        $order->save();
        update_post_meta($order->get_id(), 'power_board_charge_id', $chargeId);

        $loggerRepository = new LogRepository();
        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $orderStatus,
            '',
            in_array($orderStatus, ['wc-pb-paid', 'wc-pb-authorize', 'wc-pb-pending']
            ) ? LogRepository::SUCCESS : LogRepository::DEFAULT
        );

        return true;
    }

    private function fraudProcess(array $input): bool
    {
        $loggerRepository = new LogRepository();
        $data = $input['data'];

        if (strpos($data['reference'], '_') === false) {
            $orderId = (int) $data['reference'];
        } else {
            $referenceArray = explode('_', $data['reference']);
            $orderId = (int) reset($referenceArray);
        }

        $order = wc_get_order($orderId);
        $fraudId = $data['_id'];
        $fraudStatus = $data['status'];

        $optionName = "power_board_fraud_{$orderId}";

        if ($fraudStatus !== 'complete') {
            $operation = ucfirst(strtolower($data['type'] ?? 'undefined'));
            $status = 'wc-pb-failed';

            delete_option($optionName);
            $order->set_status($status);
            $order->save();

            $loggerRepository->createLogRecord(
                $fraudId,
                $operation,
                $status,
                ''
            );

            return true;
        }

        $options = get_option($optionName);

        if ($options === false || $order === false) {
            return false;
        }

        $paymentSource = $data['customer']['payment_source'];
        if (!empty($options['gateway_id'])) {
            $paymentSource['gateway_id'] = $options['gateway_id'];
        }

        $chargeArgs = [
            'amount'          => (float) $order->get_total(),
            'reference'       => (string) $order->get_id(),
            'currency'        => strtoupper($order->get_currency()),
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

        if (!empty($options['charge3dsid'])) {
            $chargeArgs['_3ds_charge_id'] = $options['charge3dsid'];
        }

        if (!empty($options['_3ds'])) {
            $chargeArgs['_3ds'] = $options['_3ds'];
        }

        if (!empty($options['cvv'])) {
            $chargeArgs['customer']['payment_source']['card_ccv'] = $options['cvv'];
        }

        delete_option($optionName);

        $response = SDKAdapterService::getInstance()->createCharge($chargeArgs);
        $chargeId = !empty($response['resource']['data']['_id']) ? $response['resource']['data']['_id'] : '';

        if (!empty($response['error'])) {
            $message = SDKAdapterService::getInstance()->errorMessageToString($response);
            $loggerRepository->createLogRecord(
                $chargeId ?? '',
                'Charge',
                'UnfulfilledCondition',
                __('Can\'t charge.', POWER_BOARD_TEXT_DOMAIN).$message,
                LogRepository::ERROR
            );

            return false;
        }

        if (!empty($options['_3ds'])) {
            $attachResponse = SDKAdapterService::getInstance()->fraudAttach($chargeId, ['fraud_charge_id' => $fraudId]);
            if (!empty($attachResponse['error'])) {
                $message = SDKAdapterService::getInstance()->errorMessageToString($attachResponse);
                $loggerRepository->createLogRecord(
                    $chargeId ?? '',
                    'Fraud Attach',
                    'UnfulfilledCondition',
                    __('Can\'t fraud attach.', POWER_BOARD_TEXT_DOMAIN).$message,
                    LogRepository::ERROR
                );

                return false;
            }
        }

        $status = ucfirst(strtolower($response['resource']['data']['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($response['resource']['data']['type'] ?? 'undefined'));
        $isAuthorization = $response['resource']['data']['authorization'] ?? 0;
        $isCompleted = false;
        $markAsSuccess = false;

        if ($isAuthorization && in_array($status, ['Pending', 'Pre_authentication_pending'])) {
            $status = 'wc-pb-authorize';
        } else {
            $markAsSuccess = true;
            $isCompleted = 'Complete' === $status;
            $status = $isCompleted ? 'wc-pb-paid' : 'wc-pb-pending';
        }

        $order->set_status($status);
        $order->save();
        update_post_meta($order->get_id(), 'power_board_charge_id', $chargeId);

        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            $markAsSuccess ? LogRepository::SUCCESS : LogRepository::DEFAULT
        );

        return true;
    }

    private function refundSuccessProcess(array $input): bool
    {
        $data = $input['data'];

        if (empty($data['transaction'])) {
            return false;
        }

        if (strpos($data['reference'], '_') === false) {
            $orderId = (int) $data['reference'];
        } else {
            $referenceArray = explode('_', $data['reference']);
            $orderId = (int) reset($referenceArray);
        }

        $order = wc_get_order($orderId);

        if ($order === false) {
            return false;
        }

        $orderTotal = $order->get_total();
        $chargeId = $data['_id'] ?? '';
        $status = ucfirst(strtolower($data['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($data['type'] ?? 'undefined'));
        $refundAmount = wc_format_decimal($data['transaction']['amount']);

        switch (strtoupper($status)) {
            case ChargeStatuses::REFUNDED()->name:
            case ChargeStatuses::REFUND_REQUESTED()->name:
                if ($refundAmount < $orderTotal) {
                    $orderStatus = 'wc-pb-p-refund';
                } else {
                    $orderStatus = 'wc-pb-refunded';
                }
                update_post_meta($orderId, 'power_board_refunded_status', $orderStatus);
                break;
            default:
                $orderStatus = $order->get_status();
        }

        $order->set_status($orderStatus);
        $order->update_status(
            $orderStatus,
            __('The refund', 'woocommerce')." {$refundAmount} ".__('has been successfully.', 'woocommerce')
        );
        $order->payment_complete();
        $order->save();

        wc_create_refund([
            'amount'         => $refundAmount,
            'reason'         => __('The refund', 'woocommerce')." {$refundAmount} ".__(
                    'has been successfully.',
                    'woocommerce'
                ),
            'order_id'       => $orderId,
            'refund_payment' => true,
        ]);

        $loggerRepository = new LogRepository();
        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $orderStatus,
            '',
            in_array($orderStatus, ['wc-pb-paid', 'wc-pb-authorize', 'wc-pb-pending']
            ) ? LogRepository::SUCCESS : LogRepository::DEFAULT
        );

        return true;
    }
}
