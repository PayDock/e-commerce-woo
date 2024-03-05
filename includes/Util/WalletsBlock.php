<?php

namespace PowerBoard\Util;

use PowerBoard\Abstract\AbstractBlock;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\Checkout\WalletsPaymentService;
use PowerBoard\Services\SettingsService;

class WalletsBlock extends AbstractBlock
{
    public const AFTERPAY_SESSION_KEY = PowerBoardPlugin::PLUGIN_PREFIX.'_afterpay_payment_session_token';

    protected const SCRIPT = 'wallets-form';

    protected $name = 'power_board_wallets_block';

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

        if (!empty($_SESSION[WalletsBlock::AFTERPAY_SESSION_KEY])) {
            $result['afterpayChargeId'] = $_SESSION[WalletsBlock::AFTERPAY_SESSION_KEY];
        }

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
