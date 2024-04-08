<?php

namespace PowerBoard\Hooks;

use PowerBoard\Contracts\Hook;
use PowerBoard\Contracts\Repository;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Plugin;

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
