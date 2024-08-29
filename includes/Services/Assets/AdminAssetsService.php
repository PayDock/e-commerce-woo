<?php

namespace PayDock\Services\Assets;

use Paydock\PaydockPlugin;

class AdminAssetsService {
	private const PREFIX = 'admin';
	private const SCRIPT_PREFIX = 'script';
	private const STYLE_PREFIX = 'style';
	private const URL_SCRIPT_PREFIX = 'assets/js/admin/';
	private const URL_SCRIPT_POSTFIX = '.js';
	private const URL_STYLE_PREFIX = 'assets/css/admin/';
	private const URL_STYLE_POSTFIX = '.css';
	private array $scripts = [];
	private array $styles = [];

	public function __construct() {
		$this->setActualScripts();
		$this->setActualStyles();

		$this->registerScripts();
		$this->loadScripts();

		$this->addStyles();
	}

	public function setActualScripts(): void {
		$section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
		$page    = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		if ( isset( $_SERVER['SCRIPT_NAME'] ) && ( $_SERVER['SCRIPT_NAME'] == '/wp-admin/plugins.php' ) ) {
			$this->scripts[] = 'deactivation-confirmation';
		} elseif ( 'wc-orders' === $page ) {
			$this->scripts[] = 'admin-helpers';
		} elseif ( $section && ( stripos( $section, 'paydock' ) !== false ) ) {
			$this->scripts = [
				'tabs',
				'connections',
				'card-select'
			];
		}
	}

	public function setActualStyles(): void {
		$section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_STRING );
		if ( $section && ( stripos( $section, 'paydock' ) !== false ) ) {
			$this->styles = [ 'card-select' ];
		}
	}

	public function registerScripts(): void {
		foreach ( $this->scripts as $script ) {
			wp_register_script(
				$this->getScriptName( $script ),
				plugins_url( $this->getScriptPath( $script ), PAYDOCK_PLUGIN_FILE ),
				[],
				PAYDOCK_PLUGIN_VERSION,
				true
			);
		}
	}

	private function getScriptName( string $script ): string {
		return implode( '_', [ PaydockPlugin::PLUGIN_PREFIX, self::PREFIX, self::SCRIPT_PREFIX, $script ] );
	}

	private function getScriptPath( string $script ): string {
		return self::URL_SCRIPT_PREFIX . $script . self::URL_SCRIPT_POSTFIX;
	}

	public function loadScripts(): void {
		foreach ( $this->scripts as $script ) {
			$scriptName = $this->getScriptName( $script );
			wp_enqueue_script( $this->getScriptName( $script ), '', [], PAYDOCK_PLUGIN_VERSION, true );
			wp_localize_script( $scriptName, 'paydockWidgetSettings', [
				'pluginUrlPrefix' => PAYDOCK_PLUGIN_URL
			] );
		}
	}

	private function addStyles(): void {
		foreach ( $this->styles as $style ) {
			wp_enqueue_style(
				$this->getStyleName( $style ),
				plugins_url( $this->getStylePath( $style ), PAYDOCK_PLUGIN_FILE ),
				[],
				PAYDOCK_PLUGIN_VERSION
			);
		}
	}

	private function getStyleName( string $script ): string {
		return implode( '_', [ PaydockPlugin::PLUGIN_PREFIX, self::PREFIX, self::STYLE_PREFIX, $script ] );
	}

	private function getStylePath( string $script ): string {
		return self::URL_STYLE_PREFIX . $script . self::URL_STYLE_POSTFIX;
	}

}
