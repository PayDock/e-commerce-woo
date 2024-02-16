<?php

namespace Paydock\Util;

use Paydock\Abstract\AbstractBlock;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Services\Checkout\WalletsPaymentService;
use Paydock\Services\SettingsService;

class WalletsBlock extends AbstractBlock
{
    protected const SCRIPT = 'wallets-form';

    protected $name = 'paydock_wallets_block';

    protected WalletsPaymentService $gateway;

    public function initialize()
    {
        $this->gateway = new WalletsPaymentService();
    }

    public function get_payment_method_data(): array
    {
        $settings = SettingsService::getInstance();

        $result = [
            'title' => $settings->getWidgetPaymentWalletTitle(),
            'description' => $settings->getWidgetPaymentWalletDescription(),
            'publicKey' => $settings->getPublicKey(),
            'isSandbox' => $settings->isSandbox(),
            'styles' => $settings->getWidgetStyles(),
        ];

        foreach (WalletPaymentMethods::cases() as $payment) {
            if ($settings->isWalletEnabled($payment)) {
                $result['wallets'][strtolower($payment->name)] = [
                    'gatewayId' => $settings->getWalletGatewayId($payment),
                    'fraud' => $settings->isWalletFraud($payment),
                    'fraudServiceId' => $settings->getWalletFraudServiceId($payment),
                    'directCharge' => $settings->getWalletFraudServiceId($payment),
                ];
            }
            if ($payment->name === WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name) {
                $result[strtolower($payment->name)]['payLater'] = $settings->isPayPallSmartButtonPayLater();
            }
        }

        return $result;
    }
}
