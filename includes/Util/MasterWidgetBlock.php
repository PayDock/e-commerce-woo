<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

declare( strict_types=1 );

namespace PowerBoard\Util;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use PowerBoard\Services\Checkout\MasterWidgetPaymentService;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;

/**
 * Settings property used comes from the extension AbstractPaymentMethodType from WooCommerce
 *
 * @property array $settings
 */
final class MasterWidgetBlock extends AbstractPaymentMethodType {
	private static bool $is_load = false;
	/**
	 * Variable from AbstractPaymentMethodType
	 *
	 * @var string $name
	 * @noinspection PhpMissingFieldTypeInspection
	 * @noinspection PhpUnused
	 */
	protected $name          = 'power_board';
	protected string $script = 'blocks';
	protected MasterWidgetPaymentService $gateway;

	/**
	 * This function is used on AbstractPaymentMethodType
	 * Uses a function (get_option) from WordPress
	 *
	 * @noinspection PhpUnused
	 */
	public function initialize(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		$this->settings = get_option( 'woocommerce_power_board_settings', [] );
		$this->gateway  = new MasterWidgetPaymentService();
	}

	/**
	 * This function is used on AbstractPaymentMethodType
	 * Uses a method (get_setting) from AbstractPaymentMethodType
	 *
	 * @noinspection PhpUnused
	 */
	public function is_active() {
		/* @noinspection PhpUndefinedMethodInspection */
		return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * This function is used on AbstractPaymentMethodType
	 * Uses functions (is_checkout, wp_enqueue_script, wp_localize_script, admin_url, plugin_url, wp_set_script_translations, wp_create_nonce and is_admin) from WordPress
	 * Uses functions (WC and get_woocommerce_currency) from WooCommerce
	 *
	 * @noinspection PhpUnused
	 */
	public function get_payment_method_script_handles(): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! self::$is_load && is_checkout() ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				'power-board-form-helpers',
				POWER_BOARD_PLUGIN_URL . 'assets/js/helpers/form.helper.js',
				[ 'jquery' ],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				'power-board-cart-changes-helpers',
				POWER_BOARD_PLUGIN_URL . 'assets/js/helpers/cart-changes.helper.js',
				[ 'jquery' ],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				'power-board-form-helpers',
				'PowerBoardAjaxError',
				[
					'url'           => admin_url( 'admin-ajax.php' ),
					'wpnonce_error' => wp_create_nonce( 'power-board-create-error-notice' ),
				]
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				'power-board-api',
				SettingsService::get_instance()->get_widget_script_url(),
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				'power-board-form',
				POWER_BOARD_PLUGIN_URL . 'assets/js/frontend/form.js',
				[ 'jquery' ],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				'power-board-form',
				'PowerBoardAjax',
				[
					'url'           => admin_url( 'admin-ajax.php' ),
					'wpnonce'       => wp_create_nonce( 'power-board-create-charge-intent' ),
					'wpnonce_error' => wp_create_nonce( 'power-board-create-error-notice' ),
				]
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				'power-board-classic-form',
				POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/classic-form.js',
				[ 'jquery' ],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				'power-board-classic-form',
				'PowerBoardAjax',
				[
					'url'           => admin_url( 'admin-ajax.php' ),
					'wpnonce'       => wp_create_nonce( 'power-board-create-charge-intent' ),
					'wpnonce_error' => wp_create_nonce( 'power-board-create-error-notice' ),
				]
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				'axios',
				'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_style(
				'power-board-widget-css',
				POWER_BOARD_PLUGIN_URL . 'assets/css/frontend/widget.css',
				[],
				POWER_BOARD_PLUGIN_VERSION
			);

			self::$is_load = true;
		}

		$script_path       = 'assets/build/js/frontend/' . $this->script . '.js';
		$script_asset_path = 'assets/build/js/frontend/' . $this->script . '.asset.php';
		/* @noinspection PhpUndefinedFunctionInspection */
		$script_url   = plugins_url( $script_path, POWER_BOARD_PLUGIN_FILE );
		$script_name  = POWER_BOARD_PLUGIN_PREFIX . '-' . $this->script;
		$script_asset = file_exists( $script_asset_path ) ? require $script_asset_path : [
			'dependencies' => [],
			'version'      => POWER_BOARD_PLUGIN_VERSION,
		];

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_register_script(
			$script_name,
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_localize_script(
			$script_name,
			'powerBoardWidgetSettings',
			[
				'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL,
			]
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_set_script_translations( $script_name );
		}

		return [ $script_name ];
	}

	/**
	 * This function is used on AbstractPaymentMethodType
	 * Uses a function (is_admin) from WordPress
	 * Uses functions (WC, get_woocommerce_currency) from WooCommerce
	 *
	 * @noinspection PhpUnused
	 */
	public function get_payment_method_data(): array {
		SDKAdapterService::get_instance();
		$settings_service = SettingsService::get_instance();

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! is_admin() ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			WC()->cart->calculate_totals();
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$total = ! is_admin() ? WC()->cart->get_total() : 0;

		/* @noinspection PhpUndefinedFunctionInspection */
		return [
			// Woocommerce data.
			'amount'                  => $total,
			'currency'                => strtoupper( get_woocommerce_currency() ),
			// Widget.
			'title'                   => 'PowerBoard',
			// Keys.
			'environment'             => $settings_service->get_environment(),
			// Master Widget Checkout.
			'checkoutTemplateVersion' => $settings_service->get_checkout_template_version(),
			'checkoutCustomisationId' => $settings_service->get_checkout_customisation_id(),
			'checkoutConfigurationId' => $settings_service->get_checkout_configuration_id(),
		];
	}
}
