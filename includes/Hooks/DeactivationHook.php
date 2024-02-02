<?php

namespace Paydock\Hooks;

use Paydock\Contracts\Hook;
use Paydock\Contracts\Repository;
use Paydock\Enums\APMsSettings;
use Paydock\Enums\BankAccountSettings;
use Paydock\Enums\CardSettings;
use Paydock\Enums\CredentialSettings;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Enums\WalletSettings;
use Paydock\PaydockPlugin;
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

        $repositories = array_map(fn(string $className) => new $className, PaydockPlugin::REPOSITORIES);

        array_map([$instance, 'runMigration'], $repositories);
    }

    protected function runMigration(Repository $repository): void
    {
        $repository->dropTable();
    }
}
