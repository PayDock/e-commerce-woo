<?php

namespace PowerBoard\Controllers\Admin;

use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Util\WalletsBlock;
use WP_REST_Request;

class WidgetController
{
    public function createWalletCharge(WP_REST_Request $request)
    {
        $settings = SettingsService::getInstance();

        $loggerRepository = new LogRepository();

        $request = $request->get_json_params();
        $result = [];

        foreach (WalletPaymentMethods::cases() as $payment) {
            $key = strtolower($payment->name);
            $seesionKey = WalletsBlock::WALLETS_SESSION_KEY.$payment->name.$request['order_id'];
            if ($settings->isWalletEnabled($payment)) {
                if (
                    !empty($_SESSION[$seesionKey])
                    && (
                        !empty($_SESSION[$seesionKey.'json_data'])
                        && ($_SESSION[$seesionKey.'json_data'] === json_encode($request))
                    )
                ) {
                    $result[$key] = $_SESSION[$seesionKey];
                    continue;
                }
                $_SESSION[$seesionKey.'json_data'] = json_encode($request);
                $reference = WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $payment->name
                    ? $request['order_id'].'_'.microtime()
                    : $request['order_id'];

                $chargeRequest = [
                    'amount' => round($request['total']['total_items'] / 100, 2),
                    'currency' => $request['total']['currency_code'],
                    'reference' => (string) $reference,
                    'customer' => [
                        'payment_source' => [
                            'gateway_id' => $settings->getWalletGatewayId($payment),
                        ]
                    ],
                    'meta' => [
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
                        'data' => [],
                    ];

                    if ($payment->name === WalletPaymentMethods::AFTERPAY()->name) {
                        $chargeRequest['fraud']['mode'] = 'passive';
                    }
                }

                if ($payment->name === WalletPaymentMethods::AFTERPAY()->name) {
                    $chargeRequest['meta']['success_url'] = wc_get_checkout_url()
                        .'?afterpay_success=true&&direct_charge='
                        .($settings->isWalletDirectCharge($payment) ? 'true':'false');
                    $chargeRequest['meta']['error_url'] = wc_get_checkout_url()
                        .'?afterpay_success=false&&direct_charge='
                        .($settings->isWalletDirectCharge($payment) ? 'true':'false');

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
                $result[$key] = SDKAdapterService::getInstance()
                    ->createWalletCharge($chargeRequest, $settings->isWalletDirectCharge($payment));

                $result[strtolower($payment->name)]['county'] = $request['address']['country'] ?? '';

                if ($payment->name === WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name) {
                    $result[$key]['pay_later'] = 'yes' === $settings->isPayPallSmartButtonPayLater();
                }

                if (
                    ($payment->name === WalletPaymentMethods::AFTERPAY()->name)
                    && empty($_SESSION[WalletsBlock::AFTERPAY_SESSION_KEY])
                ) {
                    $_SESSION[WalletsBlock::AFTERPAY_SESSION_KEY] = $result[$key]['resource']['data']['charge']['_id'];
                }

                if (!empty($result[$key]['error'])) {
                    $operation = ucfirst(strtolower($result[$key]['resource']['type'] ?? 'undefined'));
                    $status = $result[$key]['error']['message'] ?? 'empty status';
                    $message = $result[$key]['error']['details'][0]['gateway_specific_description']
                        ?? 'empty message';

                    $loggerRepository->createLogRecord('', $operation, $status, $message, LogRepository::ERROR);
                }

                $_SESSION[$seesionKey] = $result[$key];
            }
        }


        return rest_ensure_response($result);
    }
}
