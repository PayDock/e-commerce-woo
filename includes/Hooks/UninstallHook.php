<?php

namespace PowerBoard\Hooks;

use PowerBoard\Contracts\Hook;
use PowerBoard\Contracts\Repository;
use PowerBoard\Enums\PowerBoardPluginEnum;

class UninstallHook implements Hook {

	public function __construct() {
	}

	public static function handle(): void {
		$repositories = array_map(
			function ( string $className ) {
				return new $className();
			},
			PowerBoardPluginEnum::REPOSITORIES
		);
		array_map( [ new self(), 'runMigration' ], $repositories );
	}

	protected function runMigration( Repository $repository ): void {
		$repository->dropTable();
	}
}
