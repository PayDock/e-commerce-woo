<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

declare( strict_types=1 );

namespace WooPlugin\Util;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use WooPlugin\Services\SDKAdapterService;
use WooPlugin\Services\SettingsService;

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
	protected $name          = PLUGIN_PREFIX;
	protected string $script = 'blocks';

	/**
	 * This function is used on AbstractPaymentMethodType
	 * Uses a function (get_option) from WordPress
	 *
	 * @noinspection PhpUnused
	 */
	public function initialize(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		$this->settings = get_option( 'woocommerce_' . PLUGIN_PREFIX . '_settings', [] );
	}

	/**
	 * This function is used on AbstractPaymentMethodType
	 * Uses a method (get_setting) from AbstractPaymentMethodType
	 *
	 * @noinspection PhpUnused
	 */
	public function is_active() {
		/* @noinspection PhpUndefinedFunctionInspection */
		$payment_gateways_class = WC()->payment_gateways();
		$payment_gateways       = $payment_gateways_class->payment_gateways();

		return ! empty( $payment_gateways[ PLUGIN_PREFIX ] ) ? $payment_gateways[ PLUGIN_PREFIX ]->is_available() : false;
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
				PLUGIN_TEXT_DOMAIN . '-form-helpers',
				PLUGIN_URL . 'assets/js/helpers/form.helper.js',
				[ 'jquery' ],
				PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				PLUGIN_TEXT_DOMAIN . '-form-helpers',
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

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				PLUGIN_TEXT_DOMAIN . '-form-helpers',
				'WooPluginAjaxError',
				[
					'url'                 => admin_url( 'admin-ajax.php' ),
					'wpnonce_error'       => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-create-error-notice' ),
					'wpnonce_check_email' => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-check-email' ),
				]
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-cart-changes-helpers',
				PLUGIN_URL . 'assets/js/helpers/cart-changes.helper.js',
				[ 'jquery' ],
				PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				PLUGIN_TEXT_DOMAIN . '-cart-changes-helpers',
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

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-api',
				SettingsService::get_instance()->get_widget_script_url(),
				[],
				PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-form',
				PLUGIN_URL . 'assets/js/frontend/form.js',
				[ 'jquery' ],
				PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				PLUGIN_TEXT_DOMAIN . '-form',
				'WooPluginAjaxCheckout',
				[
					'url'                        => admin_url( 'admin-ajax.php' ),
					'wpnonce_intent'             => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-create-charge-intent' ),
					'wpnonce_update_shipping'    => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-update-shipping' ),
					'wpnonce_update_order_notes' => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-update-order-notes' ),
					'wpnonce_check_postcode'     => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-check-postcode' ),
					'wpnonce_process_payment'    => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-process-payment-result' ),
				]
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-classic-form',
				PLUGIN_URL . '/assets/js/frontend/classic-form.js',
				[ 'jquery' ],
				PLUGIN_VERSION,
				true
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_localize_script(
				PLUGIN_TEXT_DOMAIN . '-classic-form',
				'WooPluginAjaxCheckout',
				[
					'url'                        => admin_url( 'admin-ajax.php' ),
					'wpnonce_intent'             => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-create-charge-intent' ),
					'wpnonce_update_shipping'    => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-update-shipping' ),
					'wpnonce_update_order_notes' => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-update-order-notes' ),
					'wpnonce_check_postcode'     => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-check-postcode' ),
					'wpnonce_process_payment'    => wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-process-payment-result' ),
				]
			);

			/* @noinspection PhpUndefinedFunctionInspection */
			wp_enqueue_style(
				PLUGIN_TEXT_DOMAIN . '-widget-css',
				PLUGIN_URL . 'assets/css/frontend/widget.css',
				[],
				PLUGIN_VERSION
			);

			self::$is_load = true;
		}

		$script_path       = 'assets/build/js/frontend/' . $this->script . '.js';
		$script_asset_path = 'assets/build/js/frontend/' . $this->script . '.asset.php';
		/* @noinspection PhpUndefinedFunctionInspection */
		$script_url   = plugins_url( $script_path, PLUGIN_FILE );
		$script_name  = PLUGIN_PREFIX . '-' . $this->script;
		$script_asset = file_exists( $script_asset_path ) ? require $script_asset_path : [
			'dependencies' => [],
			'version'      => PLUGIN_VERSION,
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
			'title'                   => PLUGIN_TEXT_NAME,
			// Keys.
			'environment'             => $settings_service->get_environment(),
			// Master Widget Checkout.
			'checkoutTemplateVersion' => $settings_service->get_checkout_template_version(),
			'checkoutCustomisationId' => $settings_service->get_checkout_customisation_id(),
			'checkoutConfigurationId' => $settings_service->get_checkout_configuration_id(),
		];
	}
}
