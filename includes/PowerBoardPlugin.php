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
			ActionsService::get_instance();
			FiltersService::get_instance();
		}
	}
}
