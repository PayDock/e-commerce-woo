<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use PowerBoard\Services\SettingsService;

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
				'power-board-form',
				POWER_BOARD_PLUGIN_URL . 'assets/js/frontend/form.js',
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			wp_localize_script( 'power-board-form', 'powerBoardWidgetSettings', [
				'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
			] );
			wp_enqueue_style(
				'power-board-widget-css',
				POWER_BOARD_PLUGIN_URL . 'assets/css/frontend/widget.css',
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			wp_enqueue_script(
				'power-board-api',
				SettingsService::get_instance()->get_widget_script_url(),
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);
			wp_localize_script( 'power-board-api', 'powerBoardWidgetSettings', [
				'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
			] );

			self::$isLoad = true;
		}

		$scriptPath      = 'assets/build/js/frontend/' . $this->script . '.js';
		$scriptAssetPath = 'assets/build/js/frontend/' . $this->script . '.asset.php';
		$scriptUrl       = plugins_url( $scriptPath, POWER_BOARD_PLUGIN_FILE );
		$scriptName      = PLUGIN_PREFIX  . '-' . $this->script;

		$scriptAsset = file_exists( $scriptAssetPath ) ? require( $scriptAssetPath ) : [
			'dependencies' => [],
			'version'      => POWER_BOARD_PLUGIN_VERSION,
		];
		wp_register_script( $scriptName, $scriptUrl, $scriptAsset['dependencies'], $scriptAsset['version'], true );
		wp_localize_script( $scriptName, 'powerBoardWidgetSettings', [
			'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
		] );
		wp_localize_script( 'power-board-api', 'powerBoardWidgetSettings', [
			'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
		] );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $scriptName );
		}

		return [ $scriptName ];
	}
}
