<?php

namespace PowerBoard\Util;

use PowerBoard\Abstract\AbstractBlock;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\Checkout\BankAccountPaymentService;
use PowerBoard\Services\SettingsService;

final class BankAccountBlock extends AbstractBlock
{
    protected const SCRIPT = 'bank-account-form';

    protected $name = 'power_board_bank_account_block';

    protected BankAccountPaymentService $gateway;

    public function initialize()
    {
        $this->gateway = new BankAccountPaymentService();
    }

    public function get_payment_method_data(): array
    {
        $settingsService = SettingsService::getInstance();
        $userTokens = [];
        if (is_user_logged_in()) {
            $userTokens['tokens'] = (new UserTokenRepository)->getUserTokens();
        }

        return array_merge($userTokens, [
            // Wordpress data
            'isUserLoggedIn' => is_user_logged_in(),
            'isSandbox' => $settingsService->isSandbox(),
            // Woocommerce data
            'amount' => WC()->cart->total,
            'currency' => strtoupper(get_woocommerce_currency()),
            // Widget
            'title' => $settingsService->getWidgetPaymentBankAccountTitle(),
            'description' => $settingsService->getWidgetPaymentBankAccountDescription(),
            'styles' => $settingsService->getWidgetStyles(),
            // Bank Account
            'gatewayId' => $settingsService->getBankAccountGatewayId(),
            // SaveBankAccount
            'bankAccountSaveAccount' => $settingsService->getBankAccountSaveAccount(),
            'bankAccountSaveAccountOption' => $settingsService->getBankAccountSaveAccountOption(),
            // Tokens & keys
            'publicKey' => $settingsService->getPublicKey(),
            'selectedToken' => '',
            'paymentSourceToken' => '',
            // Other
            'supports' => array_filter($this->gateway->supports, [$this->gateway, 'supports'])
        ]);
    }
}
