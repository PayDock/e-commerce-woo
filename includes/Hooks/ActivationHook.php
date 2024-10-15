<?php

namespace WooPlugin\Hooks;

use WooPlugin\Contracts\Hook;
use WooPlugin\Contracts\Repository;
use WooPlugin\WooPluginPlugin;

class ActivationHook implements Hook {

	public const CUSTOM_STATUSES = [
		'pb-failed'       => 'failed',
		'wc-pb-failed'    => 'failed',
		'pb-pending'      => 'pending',
		'wc-pb-pending'   => 'pending',
		'pb-paid'         => 'processing',
		'pb-authorize'    => 'on-hold',
		'pb-requested'    => 'processing',
		'pb-p-paid'       => 'processing',
		'wc-pb-paid'      => 'processing',
		'wc-pb-authorize' => 'on-hold',
		'wc-pb-requested' => 'processing',
		'wc-pb-p-paid'    => 'processing',
		'pb-cancelled'    => 'cancelled',
		'wc-pb-cancelled' => 'cancelled',
		'pb-refunded'     => 'refunded',
		'pb-p-refund'     => 'refunded',
		'wc-pb-refunded'  => 'refunded',
		'wc-pb-p-refund'  => 'refunded',
	];

	public const CUSTOM_STATUS_META_KEY = PLUGIN_PREFIX . '_custom_status';

	public function __construct() {
	}

	public static function handle(): void {
		$instance = new self();

		$repositories = array_map( function ( $className ) {
			return new $className();
		}, WooPluginPlugin::REPOSITORIES );

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
