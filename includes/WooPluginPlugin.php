<?php

namespace WooPlugin;

use WooPlugin\Abstracts\AbstractSingleton;
use WooPlugin\Hooks\ActivationHook;
use WooPlugin\Hooks\DeactivationHook;
use WooPlugin\Hooks\UninstallHook;
use WooPlugin\Repositories\LogRepository;
use WooPlugin\Services\ActionsService;
use WooPlugin\Services\FiltersService;

if ( ! class_exists( '\WooPlugin\WooPluginPlugin' ) ) {
	final class WooPluginPlugin extends AbstractSingleton {
		public const REPOSITORIES = [
			LogRepository::class,
		];

		public const PLUGIN_PREFIX = PLUGIN_PREFIX;
		public const PLUGIN_TEXT_DOMAIN = PLUGIN_TEXT_DOMAIN;

		public const VERSION = '1.0.0';

		protected static $instance = null;

		protected $paymentService = null;

		protected function __construct() {
			register_activation_hook( PLUGIN_FILE, [ ActivationHook::class, 'handle' ] );
			register_deactivation_hook( PLUGIN_FILE, [ DeactivationHook::class, 'handle' ] );
			register_uninstall_hook( PLUGIN_FILE, [ UninstallHook::class, 'handle' ] );

			ActionsService::getInstance();
			FiltersService::getInstance();
		}
	}
}
