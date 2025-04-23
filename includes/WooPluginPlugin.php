<?php
declare( strict_types=1 );

namespace WooPlugin;

use WooPlugin\Services\ActionsService;
use WooPlugin\Services\FiltersService;

if ( ! class_exists( '\WooPlugin\WooPluginPlugin' ) ) {

	final class WooPluginPlugin {
		protected static ?WooPluginPlugin $instance = null;

		public static function get_instance(): WooPluginPlugin {
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
			/* @noinspection PhpUndefinedFunctionInspection */
			register_activation_hook( __FILE__, [ $this, 'check_the_directory' ] );
			/* @noinspection PhpUndefinedFunctionInspection */
			add_action( 'admin_init', [ $this, 'check_the_directory' ] );
		}

		public function check_the_directory(): void {
			/* @noinspection PhpUndefinedFunctionInspection */
			$current_dir = basename( plugin_dir_path( __DIR__ ) );
			/* @noinspection PhpUndefinedFunctionInspection */
			$main_file   = plugin_basename( plugin_dir_path( __DIR__ ) ) . '/plugin.php';
			$implied_dir = PLUGIN_NAME_KEY;

			if ( $current_dir !== $implied_dir ) {
				if ( ! function_exists( 'deactivate_plugins' ) ) {
					/* @noinspection PhpUndefinedConstantInspection */
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				/* @noinspection PhpUndefinedFunctionInspection */
				if ( is_plugin_active( plugin_basename( $main_file ) ) ) {
					/* @noinspection PhpUndefinedFunctionInspection */
					deactivate_plugins( plugin_basename( $main_file ) );
				}

				/* @noinspection PhpUndefinedFunctionInspection */
				$user_id = function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0;

				/* @noinspection PhpUndefinedFunctionInspection */
				set_transient(
					PLUGIN_PREFIX . '_status_change_error_' . $user_id,
					'Error: The plugin must be installed in the "' . $implied_dir . '" directory. Current one is: "' . $current_dir . '". Please delete the plugin and install it again.',
					300
				);

				/* @noinspection PhpUndefinedFunctionInspection */
				add_action( 'admin_head', [ $this, 'wrong_dir_style' ] );
				/* @noinspection PhpUndefinedFunctionInspection */
				add_action( 'admin_notices', [ $this, 'wrong_dir_notice' ] );
			}
		}

		/**
		 * Used on admin_head hook
         * phpcs:disable WordPress.Security.NonceVerification -- processed through the WooCommerce form handler
		 *
		 * @noinspection PhpUnused
		 */
		public function wrong_dir_style(): void {
			if ( isset( $_GET['activate'] ) ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				$user_id = function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0;
				/* @noinspection PhpUndefinedFunctionInspection */
				$error = get_transient( PLUGIN_PREFIX . '_status_change_error_' . $user_id );

				if ( $error ) {
					echo '<style>#message.updated.notice{display:none;}</style>';
				}
			}
		}
        // phpcs:enable

		/**
		 * Used on admin_notices hook
		 *
		 * @noinspection PhpUnused
		 */
		public function wrong_dir_notice(): void {
			/* @noinspection PhpUndefinedFunctionInspection */
			$user_id = function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0;
			/* @noinspection PhpUndefinedFunctionInspection */
			$error = get_transient( PLUGIN_PREFIX . '_status_change_error_' . $user_id );

			if ( $error ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
				/* @noinspection PhpUndefinedFunctionInspection */
				delete_transient( PLUGIN_PREFIX . '_status_change_error_' . $user_id );
			}
		}
	}
}
