<?php
declare( strict_types=1 );

namespace PowerBoard;

use PowerBoard\Services\ActionsService;
use PowerBoard\Services\FiltersService;

if ( ! class_exists( '\PowerBoard\PowerBoardPlugin' ) ) {

	final class PowerBoardPlugin {
		protected static ?PowerBoardPlugin $instance = null;

		public static function get_instance(): PowerBoardPlugin {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Uses a function (add_filter) from WordPress
		 */
		protected function __construct() {
			/* @noinspection PhpUndefinedFunctionInspection */
			add_filter( 'woocommerce_locate_template', [ $this, 'my_account_order_pay_template' ], 10, 3 );

			ActionsService::get_instance();
			FiltersService::get_instance();
		}

		/**
		 * Uses functions (get_template_directory_uri, untrailingslashit, plugin_dir_path and locate_template) from WordPress
		 */
		public function my_account_order_pay_template( $template, $template_name, $template_path ): string {
			$_template = $template;

			if ( ! $template_path ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				$template_path = get_template_directory_uri();
			}
			/* @noinspection PhpUndefinedFunctionInspection */
			$plugin_path = untrailingslashit( plugin_dir_path( POWER_BOARD_PLUGIN_FILE ) ) . '/templates/';
			/* @noinspection PhpUndefinedFunctionInspection */
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
