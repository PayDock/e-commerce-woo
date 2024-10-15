<?php

namespace WooPlugin\Abstracts;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use WooPlugin\WooPluginPlugin;
use WooPlugin\Services\SettingsService;

abstract class AbstractBlock extends AbstractPaymentMethodType {
	private static $isLoad = false;
	protected $script;

	public function __construct() {
		if ( defined( static::class . '::SCRIPT' ) ) {
			$this->script = static::SCRIPT;
		}
	}

	public function is_active() {
		return $this->gateway->is_available();
	}

	public function get_payment_method_script_handles() {
		if ( ! self::$isLoad && is_checkout() ) {
			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-form',
				PLUGIN_URL . 'assets/js/frontend/form.js',
				[],
				PLUGIN_VERSION,
				true
			);

			wp_localize_script( PLUGIN_TEXT_DOMAIN . '-form', 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT_NAME,
				'pluginPrefix' => PLUGIN_PREFIX,
			] );
			wp_enqueue_style(
				PLUGIN_TEXT_DOMAIN . '-widget-css',
				PLUGIN_URL . 'assets/css/frontend/widget.css',
				[],
				PLUGIN_VERSION,
				true
			);

			wp_enqueue_script(
				PLUGIN_TEXT_DOMAIN . '-api',
				SettingsService::getInstance()->getWidgetScriptUrl(),
				[],
				PLUGIN_VERSION,
				true
			);
			wp_localize_script( PLUGIN_TEXT_DOMAIN . '-api', 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT_NAME,
				'pluginPrefix' => PLUGIN_PREFIX,
			] );

			self::$isLoad = true;
		}

		$scriptPath      = 'assets/build/js/frontend/' . $this->script . '.js';
		$scriptAssetPath = 'assets/build/js/frontend/' . $this->script . '.asset.php';
		$scriptUrl       = plugins_url( $scriptPath, PLUGIN_FILE );
		$scriptName      = WooPluginPlugin::PLUGIN_PREFIX . '-' . $this->script;

		$scriptAsset = file_exists( $scriptAssetPath ) ? require( $scriptAssetPath ) : [
			'dependencies' => [],
			'version'      => PLUGIN_VERSION,
		];
		wp_register_script( $scriptName, $scriptUrl, $scriptAsset['dependencies'], $scriptAsset['version'], true );
		wp_localize_script( $scriptName, 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT_NAME,
				'pluginPrefix' => PLUGIN_PREFIX,
		] );
		wp_localize_script( PLUGIN_TEXT_DOMAIN . '-api', 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT_NAME,
				'pluginPrefix' => PLUGIN_PREFIX,
		] );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $scriptName );
		}

		return [ $scriptName ];
	}
}
