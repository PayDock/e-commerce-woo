<?php

namespace PowerBoard\Hooks;

use PowerBoard\Contracts\Hook;
use PowerBoard\Contracts\Repository;
use PowerBoard\Enums\PowerBoardPluginEnum;

class ActivationHook implements Hook {

	public function __construct() {
	}

	public static function handle(): void {
		$instance = new self();

		$repositories = array_map(
			function ( $className ) {
				return new $className();
			},
			PowerBoardPluginEnum::REPOSITORIES
		);

		array_map( [ $instance, 'runMigration' ], $repositories );
	}

	protected function runMigration( Repository $repository ): void {
		$repository->createTable();
	}
}
