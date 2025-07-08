<?php
declare( strict_types=1 );

namespace WooPlugin;

use WooPlugin\Services\ActionsService;
use WooPlugin\Services\FiltersService;
use WooPlugin\Services\Assets\AdminAssetsService;

if ( ! class_exists( '\WooPlugin\WooPluginPlugin' ) ) {

	final class WooPluginPlugin {
		protected static ?WooPluginPlugin $instance = null;

		/**
		 * Uses a function (add_filter) from WordPress
		 */
		protected function __construct() {
			ActionsService::get_instance();
			FiltersService::get_instance();

			if ( is_admin() ) {
				global $pagenow;

				if (
					$pagenow === 'plugins.php' ||
					(
						$pagenow === 'admin.php' &&
						isset( $_GET['page'], $_GET['tab'], $_GET['section'] ) &&
						$_GET['page'] === 'wc-settings' &&
						$_GET['tab'] === 'checkout' &&
						$_GET['section'] === PLUGIN_PREFIX
					)
				) {
					AdminAssetsService::get_instance();
				}
			}

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
				#classic-wooPluginCheckout_wrapper button,
				#standaloneWidget button,
				#standaloneWidget #afterpay-checkout-button,
				#standaloneWidget #gpay-button-online-api-id,
				#standaloneWidget .zip-button,
				#standaloneWidget apple-pay-button,
				#standaloneWidget .paypal-buttons button {
					background-image: none !important;
				}
				#classic-wooPluginCheckout_wrapper iframe {
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
