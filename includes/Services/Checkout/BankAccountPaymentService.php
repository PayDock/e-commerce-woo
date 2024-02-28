<?php

namespace Paydock\Services\Checkout;

use Exception;
use Paydock\Abstract\AbstractPaymentService;
use Paydock\Exceptions\LoggedException;
use Paydock\Repositories\LogRepository;
use Paydock\Services\ProcessPayment\BankAccountProcessor;
use Paydock\Services\SettingsService;

class BankAccountPaymentService extends AbstractPaymentService
{
    public function __construct()
    {
        $settings = SettingsService::getInstance();

        $this->id = 'paydock_bank_account_gateway';
        $this->title = $settings->getWidgetPaymentBankAccountTitle();
        $this->description = $settings->getWidgetPaymentBankAccountDescription();

        parent::__construct();
    }

    public function is_available()
    {
        return SettingsService::getInstance()->isEnabledPayment()
            && SettingsService::getInstance()->isBankAccountEnabled();
    }

    public function payment_scripts()
    {
        return SettingsService::getInstance()->getWidgetScriptUrl();
    }

    public function process_payment($order_id, $retry = true, $force_customer = false)
    {
        $order = wc_get_order($order_id);

        $loggerRepository = new LogRepository();
        $chargeId = '';

        try {
            $processor = new BankAccountProcessor($order, $_POST);

            $response = $processor->run($order_id);
            $chargeId = !empty($response['resource']['data']['_id']) ? $response['resource']['data']['_id'] : '';
        } catch (LoggedException $exception) {
            wc_add_notice(
                __('Error:', PAY_DOCK_TEXT_DOMAIN).' '.$exception->getMessage(),
                'error'
            );

            $operation = ucfirst(strtolower($exception->response['resource']['type'] ?? 'undefined'));
            $status = $exception->response['error']['message'] ?? 'empty status';
            $message = $exception->response['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

            $loggerRepository->createLogRecord($chargeId, $operation, $status, $message, LogRepository::ERROR);

            return [
                'result' => 'fail'
            ];
        } catch (Exception $exception) {
            wc_add_notice(
                __('Error:', PAY_DOCK_TEXT_DOMAIN).' '.$exception->getMessage(),
                'error'
            );

            return [
                'result' => 'fail',
                'error' => $exception->getMessage()
            ];
        }

        $status = ucfirst(strtolower($response['resource']['data']['transactions'][0]['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($response['resource']['type'] ?? 'undefined'));
        $isAuthorization = $response['resource']['data']['authorization'] ?? 0;
        $isCompleted = false;
        $markAsSuccess = false;
        if ($isAuthorization && $status == 'Pending'){
            $status = 'wc-paydock-authorize';
        } else {
            $markAsSuccess = true;
            $isCompleted = 'Complete' === $status;
            $status = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-requested';
        }
        $order->set_status($status);
        $order->payment_complete();
        $order->save();
        update_post_meta($order->get_id(), 'paydock_charge_id', $chargeId);
        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            $markAsSuccess ? LogRepository::SUCCESS : LogRepository::DEFAULT);

        return [
            'result' => 'success', 'redirect' => $this->get_return_url($order)
        ];
    }

    public function webhook()
    {

    }
}
