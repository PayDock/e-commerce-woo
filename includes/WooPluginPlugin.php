<?php
declare( strict_types=1 );

namespace WooPlugin;

use WooPlugin\Services\ActionsService;
use WooPlugin\Services\FiltersService;

if ( ! class_exists( '\WooPlugin\WooPluginPlugin' ) ) {

	final class WooPluginPlugin {
		protected static ?WooPluginPlugin $instance = null;

		/**
		 * Uses a function (add_filter) from WordPress
		 */
		protected function __construct() {
			ActionsService::get_instance();
			FiltersService::get_instance();
			// Reset button styles inside the widget
			/* @noinspection PhpUndefinedFunctionInspection */
			add_action( 'wp_head', [ $this, 'register_style_fixes' ] );
		}

		public static function get_instance(): WooPluginPlugin {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function register_style_fixes(): void {
			?>
			<style>
				#classic-powerBoardCheckout_wrapper button,
				#standaloneWidget button,
				#standaloneWidget #afterpay-checkout-button,
				#standaloneWidget #gpay-button-online-api-id,
				#standaloneWidget .zip-button,
				#standaloneWidget apple-pay-button,
				#standaloneWidget .paypal-buttons button {
					background-image: none !important;
				}
				#classic-powerBoardCheckout_wrapper iframe {
					background: transparent !important;
					display: block !important;
					max-width: 100% !important;
				}
				.checkout-overlay {
					z-index: 999 ! important;
				}
			</style>
			<?php
		}
	}
}
