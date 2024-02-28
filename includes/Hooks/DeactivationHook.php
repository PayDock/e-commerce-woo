<?php

namespace PowerBoard\Hooks;

use PowerBoard\Contracts\Hook;
use PowerBoard\Contracts\Repository;
use PowerBoard\Enums\APMsSettings;
use PowerBoard\Enums\BankAccountSettings;
use PowerBoard\Enums\CardSettings;
use PowerBoard\Enums\CredentialSettings;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Enums\WalletSettings;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Plugin;
use PowerBoard\Services\SettingsService;

class DeactivationHook implements Hook
{

    public function __construct()
    {
    }

    public static function handle(): void
    {
        $instance = new self();

        $repositories = array_map(fn(string $className) => new $className, PowerBoardPlugin::REPOSITORIES);

        array_map([$instance, 'runMigration'], $repositories);
    }

    protected function runMigration(Repository $repository): void
    {
        $repository->dropTable();
    }
}
