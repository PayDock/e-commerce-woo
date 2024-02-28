<?php

namespace Paydock\Util;

use Paydock\Abstract\AbstractBlock;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Repositories\UserCustomerRepository;
use Paydock\Services\Checkout\ApmsPaymentService;
use Paydock\Services\SettingsService;

final class ApmBlock extends AbstractBlock
{
    protected const SCRIPT = 'apms';

    protected $name = 'paydock_apms';

    protected ApmsPaymentService $gateway;

    public function initialize()
    {
        $this->gateway = new ApmsPaymentService();
    }

    public function get_payment_method_data(): array
    {
        $settingsService = SettingsService::getInstance();
        $userCustomers =[];
        if (is_user_logged_in()) {
            $userCustomers = [
                'customers' => (new UserCustomerRepository)->getUserCustomers()
            ];
        }

        return array_merge($userCustomers, [
            // Wordpress data
            'isUserLoggedIn' => is_user_logged_in(),
            'isSandbox' => $settingsService->isSandbox(),
            // Woocommerce data
            'amount' => WC()->cart->total,
            'currency' => strtoupper(get_woocommerce_currency()),
            // Widget
            'title' => $settingsService->getWidgetPaymentAPMTitle(),
            'description' => $settingsService->getWidgetPaymentAPMDescription(),
            'styles' => $settingsService->getWidgetStyles(),
            // Apms
            'afterpayEnable' => $settingsService->isAPMsEnabled(OtherPaymentMethods::AFTERPAY()),
            'zippayEnable' => $settingsService->isAPMsEnabled(OtherPaymentMethods::ZIPPAY()),
            'gatewayType' => '',
            'gatewayId' => '',
            'afterpayGatewayId' => $settingsService->getAPMsGatewayId(OtherPaymentMethods::AFTERPAY()),
            'zippayGatewayId' => $settingsService->getAPMsGatewayId(OtherPaymentMethods::ZIPPAY()),
            // Tokens & keys
            'publicKey' => $settingsService->getPublicKey(),
            'paymentSourceToken' => '',
            // SaveCard
            'apmSaveCard' => false,
            'apmSaveCardChecked' => false,
            'afterpaySaveCard' => $settingsService->isAPMsSaveCard(OtherPaymentMethods::AFTERPAY()),
            'zippaySaveCard' => $settingsService->isAPMsSaveCard(OtherPaymentMethods::ZIPPAY()),
            // DirectCharge
            'directCharge' => false,
            'afterpayDirectCharge' => $settingsService->isAPMsDirectCharge(OtherPaymentMethods::AFTERPAY()),
            'zippayDirectCharge' => $settingsService->isAPMsDirectCharge(OtherPaymentMethods::ZIPPAY()),
            // Fraud
            'fraud' => false,
            'fraudServiceId' => '',
            'afterpayFraud' => $settingsService->isAPMsFraud(OtherPaymentMethods::AFTERPAY()),
            'zippayFraud' => $settingsService->isAPMsFraud(OtherPaymentMethods::ZIPPAY()),
            'afterpayFraudServiceId' => $settingsService->getAPMsFraudServiceId(OtherPaymentMethods::AFTERPAY()),
            'zippayFraudServiceId' => $settingsService->getAPMsFraudServiceId(OtherPaymentMethods::ZIPPAY()),
            // Other
            'supports' => array_filter($this->gateway->supports, [$this->gateway, 'supports'])
        ]);
    }
}
