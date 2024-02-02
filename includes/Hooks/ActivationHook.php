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
use Paydock\Repositories\LogRepository;
use Paydock\Services\SettingsService;

class ActivationHook implements Hook
{

    public function __construct()
    {
    }

    public static function handle(): void
    {
        $instance = new self();

        $repositories = array_map(fn(string $className) => new $className, PaydockPlugin::REPOSITORIES);

        array_map([$instance, 'runMigration'], $repositories);

        $instance->createLogs();
    }

    protected function runMigration(Repository $repository): void
    {
        $repository->createTable();
    }

    private function createLogs(): void
    {
        $stubData = [
            [
                '5ec63451b12c99579e46ee31',
                'Charges',
                'Pending',
                '',
                LogRepository::DEFAULT
            ],
            [
                '5ec63445b12c99579e46ee27',
                'Charges',
                'Completed',
                '',
                LogRepository::SUCCESS
            ],
            [
                '5ec63445b12c99579e46ee27',
                'Charges',
                'UnfulfilledCondition',
                'Charge authenticated using different currency',
                LogRepository::ERROR
            ],
        ];
        $repository = new LogRepository();

        foreach ($stubData as $stub) {
            $repository->createLogRecord(...$stub);
        }
    }
}
