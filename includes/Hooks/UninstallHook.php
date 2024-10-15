<?php

namespace WooPlugin\Hooks;

use WooPlugin\Contracts\Hook;
use WooPlugin\Contracts\Repository;
use WooPlugin\WooPluginPlugin;

class UninstallHook implements Hook {

	public function __construct() {
	}

	public static function handle(): void {
		$repositories = array_map( function (string $className) {
			return new $className();
		}, WooPluginPlugin::REPOSITORIES );
		array_map( [ new self(), 'runMigration' ], $repositories );
	}

	protected function runMigration( Repository $repository ): void {
		$repository->dropTable();
	}
}
