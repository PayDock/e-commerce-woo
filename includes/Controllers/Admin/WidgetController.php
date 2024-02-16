<?php

namespace Paydock\Controllers\Admin;

use Paydock\Enums\WalletPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\BrowserDetection;
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
        $detector = new BrowserDetection();
        $browser = $detector->getBrowser($_SERVER['HTTP_USER_AGENT']);
        $os = $detector->getOS($_SERVER['HTTP_USER_AGENT']);
        $isSafariOrIOS = ('iOS' === $os['os_name'])
            || (bool) $browser['browser_safari_original']
            || (bool) $browser['browser_ios_webview'];


        foreach (WalletPaymentMethods::cases() as $payment) {
            if (
                ((WalletPaymentMethods::APPLE_PAY()->name === $payment->name) && !$isSafariOrIOS)
                || ((WalletPaymentMethods::GOOGLE_PAY()->name === $payment->name) && $isSafariOrIOS)
            ) {
                continue;
            }

            if ($settings->isWalletEnabled($payment)) {
                $key = strtolower($payment->name);
                $chargeRequest = [
                    'amount' => round($request['cartTotals']['total_items'] / 100, 2),
                    'currency' => $request['cartTotals']['currency_code'],
                    'customer' => [
                        'payment_source' => [
                            'gateway_id' => $settings->getWalletGatewayId($payment)
                        ]
                    ],
                    'meta' => [
                        'store_name' => get_bloginfo('name'),
                        'success_url' => 'http://paydock.localhost',
                        'error_url' => 'http://paydock.localhost'
                    ],
                ];


                if (
                    $settings->isWalletFraud($payment)
                    && !empty($fraudService = $settings->getWalletFraudServiceId($payment))
                ) {
                    $chargeRequest['fraud'] = [
                        'service_id' => $fraudService,
                        'data' => [],
                        'mode' => 'active',
                    ];
                }

                $result[$key] = SDKAdapterService::getInstance()
                    ->createWalletCharge($chargeRequest, $settings->isWalletDirectCharge($payment));

                $result[strtolower($payment->name)]['county'] = $request['billingAddress']['country'] ?? '';

                if ($payment->name === WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name) {
                    $result[$key]['payLater'] = $settings->isPayPallSmartButtonPayLater();
                }

                if (!empty($result[$key]['error'])) {
                    $operation = ucfirst(strtolower($exception->response['resource']['type'] ?? 'undefined'));
                    $status = $exception->response['error']['message'] ?? 'empty status';
                    $message = $exception->response['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

                    $loggerRepository->createLogRecord('', $operation, $status, $message, LogRepository::ERROR);
                } else {
                    $loggerRepository->createLogRecord(
                        $result[$key]['resource']['data']['charge']['_id'],
                        ucfirst($result[$key]['resource']['type']),
                        str_replace('_', ' ', $result[$key]['resource']['data']['charge']['status']),
                        'Wallet: '.str_replace('_', ' ', $key),
                        LogRepository::DEFAULT
                    );
                }
            }
        }

        return rest_ensure_response($result);
    }
}
