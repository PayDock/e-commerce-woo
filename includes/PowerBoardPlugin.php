<?php

namespace PowerBoard;

use PowerBoard\Abstracts\AbstractSingleton;
use PowerBoard\Hooks\ActivationHook;
use PowerBoard\Hooks\DeactivationHook;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\ActionsService;
use PowerBoard\Services\FiltersService;

if ( ! class_exists( '\PowerBoard\PowerBoardPlugin' ) ) {

	final class PowerBoardPlugin extends AbstractSingleton {

		public const REPOSITORIES = [
			LogRepository::class,
		];

		protected static $instance = null;

		protected $paymentService = null;

		protected function __construct() {

			add_filter( 'woocommerce_locate_template', [ $this, 'my_account_order_pay_template' ], 10, 3 );

			register_activation_hook( POWER_BOARD_PLUGIN_FILE, [ ActivationHook::class, 'handle' ] );
			register_deactivation_hook( POWER_BOARD_PLUGIN_FILE, [ DeactivationHook::class, 'handle' ] );

			ActionsService::getInstance();
			FiltersService::getInstance();

		}

		public function my_account_order_pay_template( $template, $template_name, $template_path ) {

			global $woocommerce;

			$_template = $template;

			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}

			$plugin_path = untrailingslashit( plugin_dir_path( POWER_BOARD_PLUGIN_FILE ) ) . '/templates/';
			$template = locate_template( [ $template_path . $template_name, $template_name ] );

			if ( file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}

			if ( ! $template ) {
				$template = $_template;
			}

			return $template;

		}

	}

}
