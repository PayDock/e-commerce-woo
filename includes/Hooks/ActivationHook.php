<?php

namespace PayDock\Hooks;

use Paydock\Contracts\Hook;
use Paydock\Contracts\Repository;
use Paydock\PaydockPlugin;

class ActivationHook implements Hook {

	public const CUSTOM_STATUSES = [
		'paydock-failed'       => 'failed',
		'wc-paydock-failed'    => 'failed',
		'paydock-pending'      => 'pending',
		'wc-paydock-pending'   => 'pending',
		'paydock-paid'         => 'processing',
		'paydock-authorize'    => 'on-hold',
		'paydock-requested'    => 'processing',
		'paydock-p-paid'       => 'processing',
		'wc-paydock-paid'      => 'processing',
		'wc-paydock-authorize' => 'on-hold',
		'wc-paydock-requested' => 'processing',
		'wc-paydock-p-paid'    => 'processing',
		'paydock-cancelled'    => 'cancelled',
		'wc-paydock-cancelled' => 'cancelled',
		'paydock-refunded'     => 'refunded',
		'paydock-p-refund'     => 'refunded',
		'wc-paydock-refunded'  => 'refunded',
		'wc-paydock-p-refund'  => 'refunded',
	];

	public const CUSTOM_STATUS_META_KEY = 'paydock_custom_status';

	public function __construct() {
	}

	public static function handle(): void {
		$instance = new self();

		$repositories = array_map( function ( $className ) {
			return new $className();
		}, PaydockPlugin::REPOSITORIES );

		array_map( [ $instance, 'runMigration' ], $repositories );

		$instance->fixOrderStatuses();
	}

	protected function runMigration( Repository $repository ): void {
		$repository->createTable();
	}

	protected function fixOrderStatuses() {
		foreach ( self::CUSTOM_STATUSES as $custom_status => $new_Status ) {
			$this->updateOrderStatus( wc_get_orders( [ 'status' => $custom_status ] ), $new_Status );
		}
	}

	protected function updateOrderStatus( $orders, $new_status ) {
		foreach ( $orders as $order ) {
			$order->update_meta_data( self::CUSTOM_STATUS_META_KEY, $order->get_status() );
			$order->set_status( $new_status );
			$order->save();
		}
	}
}
