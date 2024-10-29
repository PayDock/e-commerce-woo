<?php

namespace WooPlugin\Hooks;

use WooPlugin\Contracts\Hook;
use WooPlugin\Contracts\Repository;
use WooPlugin\WooPluginPlugin;

class ActivationHook implements Hook {

	public function __construct() {
	}

	public static function handle(): void {
		$instance = new self();

		$repositories = array_map( function ( $className ) {
			return new $className();
		}, WooPluginPlugin::REPOSITORIES );

		array_map( [ $instance, 'runMigration' ], $repositories );
	}

	protected function runMigration( Repository $repository ): void {
		$repository->createTable();
	}

	protected function updateOrderStatus( $orders, $new_status ) {
		foreach ( $orders as $order ) {
			$order->set_status( $new_status );
			$order->save();
		}
	}
}
