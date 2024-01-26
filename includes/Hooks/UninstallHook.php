<?php

namespace Paydock\Hooks;

use Paydock\Contracts\Hook;
use Paydock\Contracts\Repository;
use Paydock\PaydockPlugin;

class UninstallHook implements Hook
{

    public function __construct()
    {
    }

    public static function handle(): void
    {
        $repositories = array_map(fn(string $className) => new $className, PaydockPlugin::REPOSITORIES);
        array_map([new self(), 'runMigration'], $repositories);
    }

    protected function runMigration(Repository $repository): void
    {
        $repository->dropTable();
    }
}
