<?php

namespace Paydock\Controllers\Admin;

use Paydock\Abstracts\AbstractWalletBlock;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;
use WP_REST_Request;

class WidgetController
{
    public function createWalletCharge(WP_REST_Request $request)
    {
        $settings = SettingsService::getInstance();

        $loggerRepository = new LogRepository();

        $request = $request->get_json_params();
        $result = [];
        $isAfterPay = false;

        switch ($request['type']) {
            case 'afterpay':
                $isAfterPay = true;
                $payment = WalletPaymentMethods::AFTERPAY();
                break;
            case 'apple-pay':
                $payment = WalletPaymentMethods::APPLE_PAY();
                break;
            case 'google-pay':
                $payment = WalletPaymentMethods::GOOGLE_PAY();
                break;
            case 'pay-pal':
                $payment = WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
                break;
        }

        $key = strtolower($payment->name);
        if ($settings->isWalletEnabled($payment)) {
            $reference = $request['order_id'];

            $chargeRequest = [
                'amount'    => round($request['total']['total_price'] / 100, 2),
                'currency'  => $request['total']['currency_code'],
                'reference' => (string) $reference,
                'customer'  => [
                    'first_name'     => $request['address']['first_name'],
                    'last_name'      => $request['address']['last_name'],
                    'email'          => $request['address']['email'],
                    'phone'          => $request['address']['phone'],
                    'payment_source' => [
                        'gateway_id' => $settings->getWalletGatewayId($payment),
                    ],
                ],
                'meta'      => [
                    'store_name' => get_bloginfo('name'),
                ],
            ];

            if ($payment->name === WalletPaymentMethods::APPLE_PAY()->name) {
                $chargeRequest['customer']['payment_source']['wallet_type'] = 'apple';
            }

            if (
                $settings->isWalletFraud($payment)
                && !empty($fraudService = $settings->getWalletFraudServiceId($payment))
            ) {
                $chargeRequest['fraud'] = [
                    'service_id' => $fraudService,
                    'data'       => [],
                ];
            }

            if ($isAfterPay) {
                $chargeRequest['meta']['success_url'] = wc_get_checkout_url()
                    .'?afterpay_success=true&&direct_charge='
                    .($settings->isWalletDirectCharge($payment) ? 'true' : 'false');
                $chargeRequest['meta']['error_url'] = wc_get_checkout_url()
                    .'?afterpay_success=false&&direct_charge='
                    .($settings->isWalletDirectCharge($payment) ? 'true' : 'false');

                $chargeRequest['customer']['payment_source']['address_line1'] = $request['address']['address_1'];
                $chargeRequest['customer']['payment_source']['address_line2'] =
                    !empty(trim($request['address']['address_2']))
                        ? $request['address']['address_2']
                        : $request['address']['address_1'];
                $chargeRequest['customer']['payment_source']['address_line3'] = $request['address']['address_1'];
                $chargeRequest['customer']['payment_source']['address_city'] = $request['address']['city'];
                $chargeRequest['customer']['payment_source']['address_state'] = $request['address']['state'];
                $chargeRequest['customer']['payment_source']['address_country'] = $request['address']['country'];
                $chargeRequest['customer']['payment_source']['address_postcode'] = $request['address']['postcode'];
            }

            $result = SDKAdapterService::getInstance()
                ->createWalletCharge($chargeRequest, $settings->isWalletDirectCharge($payment));

            $result['county'] = $request['address']['country'] ?? '';

            if ($payment->name === WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name) {
                $result['pay_later'] = 'yes' === $settings->isPayPallSmartButtonPayLater();
            }

            if ($isAfterPay && empty($_SESSION[AbstractWalletBlock::AFTERPAY_SESSION_KEY])) {
                $_SESSION[AbstractWalletBlock::AFTERPAY_SESSION_KEY] = $result['resource']['data']['charge']['_id'];
            }

            if (!empty($result[$key]['error'])) {
                $operation = ucfirst(strtolower($result[$key]['resource']['type'] ?? 'undefined'));
                $status = $result[$key]['error']['message'] ?? 'empty status';
                $message = $result[$key]['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

                $loggerRepository->createLogRecord('', $operation, $status, $message, LogRepository::ERROR);
            }
        }


        return rest_ensure_response($result);
    }
}
