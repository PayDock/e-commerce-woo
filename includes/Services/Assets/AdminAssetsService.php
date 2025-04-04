<?php
declare( strict_types=1 );

namespace WooPlugin\Services\Assets;

class AdminAssetsService {
	private const SCRIPTS = [
		'handle-settings-select-option',
		'handle-environment-changes',
		'handle-config-template-error-styles',
		'deactivation-confirmation',
	];

	private const PREFIX = 'admin';

	private const SCRIPT_PREFIX = 'script';

	private const URL_SCRIPT_PREFIX  = 'assets/js/admin/';
	private const URL_SCRIPT_POSTFIX = '.js';

	public function __construct() {
		/**
		 * Use hook admin_enqueue_scripts
		 *
		 * @noinspection PhpUndefinedFunctionInspection
		 */
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts( string $hook ): void {
		$allowed_pages = [ 'woocommerce_page_wc-settings', 'plugins.php' ];

		if ( ! in_array( $hook, $allowed_pages, true ) ) {
			return;
		}

		foreach ( self::SCRIPTS as $script ) {
			$script_name = $this->get_script_name( $script );

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_register_script(
				$script_name,
				plugins_url( $this->get_script_path( $script ), PLUGIN_FILE ),
				[ 'jquery' ],
				PLUGIN_VERSION,
				true
			);

			/**
			 * WordPress.Security.NonceVerification.Recommended
			 *
             * @phpcs:disable WordPress.Security.NonceVerification.Recommended
			 */
			if ( $script === 'deactivation-confirmation' ||
				( isset( $_GET['tab'], $_GET['section'] ) && $_GET['tab'] === 'checkout' && $_GET['section'] === PLUGIN_PREFIX ) ) {
				/**
				 * Use hook wp_enqueue_script
				 *
				 * @noinspection PhpUndefinedFunctionInspection
				 */
				wp_enqueue_script( $script_name );

				/**
				 * Use function wp_localize_script
				 *
				 * @noinspection PhpUndefinedFunctionInspection
				 */
				wp_localize_script(
					$script_name,
					'widgetSettings',
					[
						'pluginUrlPrefix'  => PLUGIN_URL,
						'pluginPrefix'     => PLUGIN_PREFIX,
						'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
						'pluginTextName'   => PLUGIN_TEXT_NAME,
						'pluginName'       => PLUGIN_NAME,
						'pluginWidgetName' => PLUGIN_WIDGET_NAME,
					]
				);
			}
		}
	}

	private function get_script_name( string $script ): string {
		return implode( '_', [ PLUGIN_PREFIX, self::PREFIX, self::SCRIPT_PREFIX, $script ] );
	}

	private function get_script_path( string $script ): string {
		return self::URL_SCRIPT_PREFIX . $script . self::URL_SCRIPT_POSTFIX;
	}
}
