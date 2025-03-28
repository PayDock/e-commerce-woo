<?php

require_once __DIR__ . '/../vendor/autoload.php';

\WP_Mock::setUsePatchwork(true);
\WP_Mock::bootstrap();

\WP_Mock::userFunction('__pb_token_accessor', [
	'return' => 'abc123',
]);

require_once __DIR__ . '/../includes/helpers.php';

if ( ! defined( 'POWER_BOARD_PLUGIN_VERSION' ) ) {
	define( 'POWER_BOARD_PLUGIN_VERSION', '1.0.0' );
}

if ( ! defined( 'POWER_BOARD_PLUGIN_NAME' ) ) {
	define( 'POWER_BOARD_PLUGIN_NAME', 'power-board' );
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $code;
		public $message;

		public function __construct( $code = '', $message = '' ) {
			$this->code    = $code;
			$this->message = $message;
		}
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ): bool {
		return $thing instanceof WP_Error;
	}
}

if ( ! function_exists( 'wc_get_logger' ) ) {
	function wc_get_logger() {
		return new class {
			public function log( $level, $message, $context = [] ) {
				// file_put_contents('php://stdout', "[{$level}] {$message}\n");
			}
		};
	}
}
