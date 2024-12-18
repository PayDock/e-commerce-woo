<?php

namespace PowerBoard\Services\Assets;

class AdminAssetsService {
	private const SCRIPTS = [
		'tabs',
		'connections',
		'deactivation-confirmation',
		// 'admin-helpers'
	];

	private const PREFIX = 'admin';

	private const SCRIPT_PREFIX = 'script';
	private const STYLE_PREFIX = 'style';

	private const URL_SCRIPT_PREFIX = 'assets/js/admin/';
	private const URL_SCRIPT_POSTFIX = '.js';

	public function __construct() {
		$this->registerScripts();
		$this->loadScripts();
	}

	public function registerScripts(): void {
		foreach ( self::SCRIPTS as $script ) {
			wp_register_script(
				$this->getScriptName( $script ),
				plugins_url( $this->getScriptPath( $script ), POWER_BOARD_PLUGIN_FILE ),
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);
		}
	}

	private function getScriptName( string $script ): string {
		return implode( '_', [ PLUGIN_PREFIX, self::PREFIX, self::SCRIPT_PREFIX, $script ] );
	}

	private function getScriptPath( string $script ): string {
		return self::URL_SCRIPT_PREFIX . $script . self::URL_SCRIPT_POSTFIX;
	}

	public function loadScripts(): void {
		foreach ( self::SCRIPTS as $script ) {
			$scriptName = $this->getScriptName( $script );
			wp_enqueue_script( $this->getScriptName( $script ),'',[],POWER_BOARD_PLUGIN_VERSION,true );
			wp_localize_script( $scriptName, 'powerBoardWidgetSettings', [
				'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
			] );
		}
	}
}
