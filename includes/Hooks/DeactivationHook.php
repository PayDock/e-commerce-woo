<?php

namespace Paydock\Hooks;

use Paydock\Contracts\Hook;
use Paydock\Enums\APMsSettings;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;
use Paydock\Plugin;
use Paydock\Services\SettingsService;

class DeactivationHook implements Hook
{

    public function __construct()
    {
    }

    public static function handle(): void
    {
        $instance = new self();
    }
}
