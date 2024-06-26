<?php

namespace Paydock\Services\Checkout;

use Exception;
use Paydock\Abstract\AbstractPaymentService;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\ProcessPayment\ApmProcessor;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;

class ApmsPaymentService extends AbstractPaymentService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $settings = SettingsService::getInstance();

        $this->id = 'paydock_apms_gateway';
        $this->title = $settings->getWidgetPaymentAPMTitle();
        $this->description = $settings->getWidgetPaymentAPMDescription();

        parent::__construct();
    }

    /**
     * Check If The Gateway Is Available For Use
     *
     * @access public
     * @return bool
     */
    function is_available()
    {
        return SettingsService::getInstance()->isEnabledPayment()
            && (
                SettingsService::getInstance()->isAPMsEnabled(OtherPaymentMethods::AFTERPAY()) ||
                SettingsService::getInstance()->isAPMsEnabled(OtherPaymentMethods::ZIPPAY())
            );
    }

    /**
     * Process the payment and return the result.
     *
     * @since 1.0.0
     */
    public function process_payment($order_id, $retry = true, $force_customer = false)
    {
        $order = wc_get_order($order_id);

        $siteName = remove_accents(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
        $description = sprintf(__('Order №%s from %s.', 'paydock-for-woocommerce'), $order->get_order_number(),
            $siteName);

        $loggerRepository = new LogRepository();
        $chargeId = '';

        try {
            $processor = new ApmProcessor($_POST);

            $response = $processor->run($order);

            if (!empty($response['error']) || empty($response['resource']['data']['_id'])) {
                $message = SDKAdapterService::getInstance()->errorMessageToString($response);
                throw new Exception(__('Can\'t charge.'.$message, PAY_DOCK_TEXT_DOMAIN));
            }

            $chargeId = $response['resource']['data']['_id'];
        } catch (Exception $e) {
            $loggerRepository->createLogRecord($chargeId ?? '', 'Charges', 'UnfulfilledCondition', $e->getMessage(),
                LogRepository::ERROR);
            wc_add_notice(__('Error:', PAY_DOCK_TEXT_DOMAIN).' '.$e->getMessage(), 'error');
            exit;
        }

        $status = ucfirst(strtolower($response['resource']['data']['transactions'][0]['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($response['resource']['type'] ?? 'undefined'));
        $isAuthorization = $response['resource']['data']['authorization'] ?? 0;
        $isCompleted = false;
        if ($isAuthorization && $status == 'Pending') {
            $status = 'wc-paydock-authorize';
        } else {
            $isCompleted = 'complete' === strtolower($status);
            $status = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-pending';
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
            $status == 'wc-paydock-paid' ? LogRepository::SUCCESS : LogRepository::DEFAULT);

        return [
            'result' => 'success', 'redirect' => $this->get_return_url($order)
        ];
    }

    public function webhook()
    {

    }
}
