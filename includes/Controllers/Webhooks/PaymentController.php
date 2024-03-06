<?php

namespace Paydock\Controllers\Webhooks;


use Paydock\Enums\SettingGroups;
use Paydock\Repositories\LogRepository;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\Validation\ConnectionValidationService;
use WpOrg\Requests\Exception;

class PaymentController
{
    public function capturePayment()
    {
        $orderId = $_POST['order_id'] ?? null;
        $error = null;
        if (!$orderId) {
            $error = __('The order does not found.');
        } else {
            $order = wc_get_order($orderId);
            if ($order->get_status() != 'paydock-authorize') {
                $error = __('The order should be have status "paydock-authorize"', 'woocommerce');
            }
        }
        $loggerRepository = new LogRepository();
        $paydockChargeId = get_post_meta($orderId, 'paydock_charge_id', true);
        if (!$error) {
            $charge = SDKAdapterService::getInstance()->capture(['charge_id' => $paydockChargeId]);
            if (!empty($charge['resource']['data']['status']) && $charge['resource']['data']['status'] == 'complete') {
                $newChargeId = $charge['resource']['data']['_id'];
                $loggerRepository->createLogRecord($newChargeId, 'Capture', 'wc-paydock-paid', '', LogRepository::SUCCESS);
                update_post_meta($orderId, 'paydock_charge_id', $newChargeId);
                $order->set_status('wc-paydock-paid');
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
                    $error = __('The capture process has been failed, please try again.', 'woocommerce');
                }
            }
        }
        if ($error) {
            $loggerRepository->createLogRecord($paydockChargeId, 'Capture', 'error', $error, LogRepository::ERROR);
            wp_send_json_error(['message' => $error]);
        }
    }

    public function cancelAuthorised()
    {
        $orderId = $_POST['order_id'] ?? null;
        $error = null;
        if (!$orderId) {
            $error = __('The order does not found.', 'woocommerce');
        } else {
            $order = wc_get_order($orderId);
            if ($order->get_status() != 'paydock-authorize') {
                $error = __('The order should be have status "paydock-authorize"', 'woocommerce');
            }
        }
        $loggerRepository = new LogRepository();
        $paydockChargeId = get_post_meta($orderId, 'paydock_charge_id', true);
        if (!$error) {
            $result = SDKAdapterService::getInstance()->cancelAuthorised(['charge_id' => $paydockChargeId]);
            if (!empty($result['resource']['data']['status']) && $result['resource']['data']['status'] == 'cancelled') {
                $loggerRepository->createLogRecord($paydockChargeId, 'Cancel-authorised', 'wc-paydock-cancelled', '', LogRepository::SUCCESS);
                $order->set_status('wc-paydock-cancelled');
                $order->payment_complete();
                $order->save();
                wp_send_json_success(['message' => __('The capture cancelled process has been successfully.', 'woocommerce')]);
            } else {
                if (!empty($result['error'])) {
                    if (is_array($result['error'])) {
                        $result['error'] = json_encode($result['error']);
                    }
                    $error = $result['error'];
                } else {
                    $error = __('The capture cancelled process has been failed, please try again', 'woocommerce');
                }
            }
        }
        if ($error) {
            $loggerRepository->createLogRecord($paydockChargeId, 'Cancel-authorised', 'error', $error, LogRepository::ERROR);
            wp_send_json_error(['message' => $error]);
        }
    }

    public function refundProcess($refund, $args)
    {
        $orderId = $args['order_id'];
        $amount = $args['amount'];
        $order = wc_get_order($orderId);
        $loggerRepository = new LogRepository();
        if (in_array($order->get_status(), ['paydock-paid', 'paydock-p-refund'])) {
            $totalRefunded = 0;
            $refunds = $order->get_refunds();
            foreach ($refunds as $refund) {
                $totalRefunded += $refund->get_amount();
            }
            $orderTotal = $order->get_total();
            $paydockChargeId = get_post_meta($orderId, 'paydock_charge_id', true);
            $result = SDKAdapterService::getInstance()->refunds(['charge_id' => $paydockChargeId, 'amount' => $amount]);
            if (!empty($result['resource']['data']['status']) && in_array($result['resource']['data']['status'], ['refunded', 'refund_requested'])) {
                $newRefundedId = $result['resource']['data']['_id'];
                $status = $totalRefunded < $orderTotal ? 'wc-paydock-p-refund' : 'wc-paydock-refunded';
                update_post_meta($orderId, 'paydock_refunded_status', $status);
                $order->set_status($status);
                $order->update_status($status, __('The refund' . $amount . ' has been successfully.', 'woocommerce'));
                $order->payment_complete();
                $order->save();
                $loggerRepository->createLogRecord($newRefundedId, 'Refunded', $status, '', LogRepository::SUCCESS);
            } else {
                if (!empty($result['error'])) {
                    if (is_array($result['error'])) {
                        $result['error'] = json_encode($result['error']);
                    }
                    $loggerRepository->createLogRecord($paydockChargeId, 'refund', 'error', $result['error'], LogRepository::ERROR);
                    throw new \Exception($result['error']);
                } else {
                    $error = __('The refund process has been failed, please try again.', 'woocommerce');
                    $loggerRepository->createLogRecord($paydockChargeId, 'Refunded', 'error', $error, LogRepository::ERROR);
                    throw new \Exception($error);
                }
            }
        }
    }

    public function afterRefundProcess($orderId, $refundId)
    {
        $paydockRefundedStatus = get_post_meta($orderId, 'paydock_refunded_status', true);
        if ($paydockRefundedStatus) {
            $order = wc_get_order($orderId);
            $order->update_status($paydockRefundedStatus);
            delete_post_meta($orderId, 'paydock_refunded_status');
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
            switch ($input['event']) {
                case ConnectionValidationService::WEBHOOK_EVENT_TRANSACTION_SUCCESS_NAME:
                    $result = $this->successProcess($input);
                    break;
                case ConnectionValidationService::WEBHOOK_EVENT_TRANSACTION_FAILURE_NAME:
                    $result = $this->failureProcess($input);
                    break;
                case ConnectionValidationService::WEBHOOK_EVENT_FRAUD_CHECK_SUCCESS_NAME:
                case ConnectionValidationService::WEBHOOK_EVENT_FRAUD_CHECK_IN_REVIEW_APPROVED_NAME:
                    $result = $this->fraudSuccessProcess($input);
                    break;
                default:
                    $result = false;
            }
        }

        echo $result ? 'Ok' : 'Fail';

        exit;
    }

    private function successProcess(array $input): bool
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

        $isCompleted = false;
        $markAsSuccess = false;
        if ($isAuthorization && in_array($status, ['Pending', 'Pre_authentication_pending'])) {
            $status = 'wc-paydock-authorize';
        } else {
            $markAsSuccess = true;
            $isCompleted = 'Complete' === $status;
            $status = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-pending';
        }

        $order->set_status($status);
        $order->save();
        update_post_meta($order->get_id(), 'paydock_charge_id', $chargeId);

        $loggerRepository = new LogRepository();
        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            $markAsSuccess ? LogRepository::SUCCESS : LogRepository::DEFAULT
        );

        return true;
    }

    private function failureProcess(array $input): bool
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

        $isPending = 'Pending' === $status;
        if ($isAuthorization && in_array($status, ['Pending', 'Pre_authentication_pending'])) {
            $status = 'wc-paydock-authorize';
        } else {
            $status = $isPending ? 'wc-paydock-pending' : 'wc-paydock-failed';
        }

        $order->set_status($status);
        $order->save();
        update_post_meta($order->get_id(), 'paydock_charge_id', $chargeId);

        $loggerRepository = new LogRepository();
        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            LogRepository::DEFAULT
        );

        return true;
    }

    private function fraudSuccessProcess(array $input): bool
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

        $optionName = "paydock_fraud_{$orderId}";
        $options = get_option($optionName);

        if ($options === false || $order === false) {
            return false;
        }

        $paymentSource = $data['customer']['payment_source'];
        if (!empty($options['gateway_id'])) {
            $paymentSource['gateway_id'] = $options['gateway_id'];
        }

        $chargeArgs = [
            'amount' => (float) $order->get_total(),
            'reference' => (string) $order->get_id(),
            'currency' => strtoupper($order->get_currency()),
            'customer' => [
                'payment_source' => $paymentSource
            ],
            'fraud_charge_id' => $fraudId,
            'capture' => $options['capture']
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
            $loggerRepository->createLogRecord($chargeId ?? '', 'Charge', 'UnfulfilledCondition', __('Can\'t charge.', PAY_DOCK_TEXT_DOMAIN) . $message, LogRepository::ERROR);

            return false;
        }

        $status = ucfirst(strtolower($response['resource']['data']['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($response['resource']['data']['type'] ?? 'undefined'));
        $isAuthorization = $response['resource']['data']['authorization'] ?? 0;
        $isCompleted = false;
        $markAsSuccess = false;

        if ($isAuthorization && in_array($status, ['Pending', 'Pre_authentication_pending'])) {
            $status = 'wc-paydock-authorize';
        } else {
            $markAsSuccess = true;
            $isCompleted = 'Complete' === $status;
            $status = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-pending';
        }

        $order->set_status($status);
        $order->save();
        update_post_meta($order->get_id(), 'paydock_charge_id', $chargeId);

        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            $markAsSuccess ? LogRepository::SUCCESS : LogRepository::DEFAULT
        );

        return true;
    }
}
