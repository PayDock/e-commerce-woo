<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\ProcessPayment\ApmProcessor;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;

abstract class AbstractAPMsPaymentService extends AbstractPaymentService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $settings = SettingsService::getInstance();
        $paymentMethod = $this->getAPMsType();

        $this->id = 'power_board_'.$paymentMethod->getId().'_a_p_m_s_gateway';
        $this->title = $settings->getWidgetPaymentAPMTitle($paymentMethod);
        $this->description = $settings->getWidgetPaymentAPMDescription($paymentMethod);

        parent::__construct();
    }

    abstract protected function getAPMsType(): OtherPaymentMethods;

    /**
     * Check If The Gateway Is Available For Use
     *
     * @access public
     * @return bool
     */
    function is_available()
    {
        return SettingsService::getInstance()->isEnabledPayment()
            && SettingsService::getInstance()->isAPMsEnabled($this->getAPMsType());
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
        $description = sprintf(
            __('Order №%s from %s.', 'pb-for-woocommerce'),
            $order->get_order_number(),
            $siteName
        );

        $loggerRepository = new LogRepository();
        $chargeId = '';

        try {
            $processor = new ApmProcessor($_POST);

            $response = $processor->run($order);

            if (!empty($response['error']) || empty($response['resource']['data']['_id'])) {
                $message = SDKAdapterService::getInstance()->errorMessageToString($response);
                throw new Exception(__('Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.', POWER_BOARD_TEXT_DOMAIN));
            }

            $chargeId = $response['resource']['data']['_id'];
        } catch (Exception $e) {
            $loggerRepository->createLogRecord(
                $chargeId ?? '',
                'Charges',
                'UnfulfilledCondition',
                $e->getMessage(),
                LogRepository::ERROR
            );
            throw new RouteException(
                'woocommerce_rest_checkout_process_payment_error',
                __('Error:', POWER_BOARD_TEXT_DOMAIN).' '.$e->getMessage()
            );
        }

        $status = ucfirst(strtolower($response['resource']['data']['transactions'][0]['status'] ?? 'undefined'));
        $operation = ucfirst(strtolower($response['resource']['type'] ?? 'undefined'));
        $isAuthorization = $response['resource']['data']['authorization'] ?? 0;
        $isCompleted = false;
        if ($isAuthorization && $status == 'Pending') {
            $status = 'wc-pb-authorize';
        } else {
            $isCompleted = 'complete' === strtolower($status);
            $status = $isCompleted ? 'wc-pb-paid' : 'wc-pb-pending';
        }
        $order->set_status($status);
        $order->payment_complete();
        $order->save();

        update_post_meta($order->get_id(), 'power_board_charge_id', $chargeId);


        WC()->cart->empty_cart();

        $loggerRepository->createLogRecord(
            $chargeId,
            $operation,
            $status,
            '',
            $status == 'wc-pb-paid' ? LogRepository::SUCCESS : LogRepository::DEFAULT
        );

        return [
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        ];
    }

    public function payment_scripts()
    {
        return SettingsService::getInstance()->getWidgetScriptUrl();
    }

    public function webhook()
    {

    }
}
