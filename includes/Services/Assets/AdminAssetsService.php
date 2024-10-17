<?php

namespace WooPlugin\Services\Assets;

use WooPlugin\WooPluginPlugin;

class AdminAssetsService {
	private const SCRIPTS = [
		'tabs',
		'connections',
		'card-select',
		'deactivation-confirmation',
		// 'admin-helpers'
	];
	private const STYLES = [
		'card-select',
	];

	private const PREFIX = 'admin';

	private const SCRIPT_PREFIX = 'script';
	private const STYLE_PREFIX = 'style';

	private const URL_SCRIPT_PREFIX = 'assets/js/admin/';
	private const URL_SCRIPT_POSTFIX = '.js';

	private const URL_STYLE_PREFIX = 'assets/css/admin/';
	private const URL_STYLE_POSTFIX = '.css';

	public function __construct() {
		$this->registerScripts();
		$this->loadScripts();
		$this->addStyles();
	}

	public function registerScripts(): void {
		foreach ( self::SCRIPTS as $script ) {
			wp_register_script(
				$this->getScriptName( $script ),
				plugins_url( $this->getScriptPath( $script ), PLUGIN_FILE ),
				[],
				PLUGIN_VERSION,
				true
			);
		}
	}

	private function getScriptName( string $script ): string {
		return implode( '_', [ WooPluginPlugin::PLUGIN_PREFIX, self::PREFIX, self::SCRIPT_PREFIX, $script ] );
	}

	private function getScriptPath( string $script ): string {
		return self::URL_SCRIPT_PREFIX . $script . self::URL_SCRIPT_POSTFIX;
	}

	public function loadScripts(): void {
		foreach ( self::SCRIPTS as $script ) {
			$scriptName = $this->getScriptName( $script );
			wp_enqueue_script( $this->getScriptName( $script ),'',[],PLUGIN_VERSION,true );
			wp_localize_script( $scriptName, 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT_NAME,
				'pluginPrefix' => PLUGIN_PREFIX,
				'pluginWidgetName' => PLUGIN_WIDGET_NAME,
				'pluginSandboxEnvironment' => PLUGIN_SANDBOX_ENVIRONMENT,
				'pluginProductionEnvironment' => PLUGIN_PRODUCTION_ENVIRONMENT,
			] );
		}
	}

	private function addStyles(): void {
		foreach ( self::STYLES as $style ) {
			wp_enqueue_style(
				$this->getStyleName( $style ),
				plugins_url( $this->getStylePath( $style ), PLUGIN_FILE ),
				[],
				PLUGIN_VERSION
			);
		}
	}

	private function getStyleName( string $script ): string {
		return implode( '_', [ WooPluginPlugin::PLUGIN_PREFIX, self::PREFIX, self::STYLE_PREFIX, $script ] );
	}

	private function getStylePath( string $script ): string {
		return self::URL_STYLE_PREFIX . $script . self::URL_STYLE_POSTFIX;
	}

}
