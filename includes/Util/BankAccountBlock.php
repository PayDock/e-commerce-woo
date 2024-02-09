<?php

namespace Paydock\Util;

use Paydock\Abstract\AbstractBlock;
use Paydock\Services\Checkout\BankAccountPaymentService;
use Paydock\Services\SettingsService;

final class BankAccountBlock extends AbstractBlock
{
    protected const SCRIPT = 'bank-account-form';

    protected $name = 'paydock_bank_account_block';

    protected BankAccountPaymentService $gateway;

    public function initialize()
    {
        $this->gateway = new BankAccountPaymentService();
    }

    public function get_payment_method_data(): array
    {
        $settings = SettingsService::getInstance();

        $userId = get_current_user_id();

        $meta_value = get_user_meta($userId, 'paydock_customers', true);

        return [
            'title' => $settings->getWidgetPaymentBangAccountTitle(),
            'description' => $settings->getWidgetPaymentBangAccountDescription(),
            'gatewayId' => $settings->getBankAccountGatewayId(),
            'saveAccount' => $settings->getBankAccountSaveAccount(),
            'saveAccountType' => $settings->getBankAccountSaveAccountOption(),
            'publicKey' => $settings->getPublicKey(),
            'showSaveDataCheckBox' => $userId > 0,
            'vaults' => empty($meta_value) ? [] : json_decode($meta_value, true),
        ];
    }
}
