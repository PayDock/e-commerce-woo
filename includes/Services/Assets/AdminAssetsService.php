<?php

namespace PowerBoard\Services\Assets;

class AdminAssetsService {
	private const SCRIPTS = [
		'hide-settings',
		'handle-settings-select-option',
		'deactivation-confirmation',
	];

	private const PREFIX = 'admin';

	private const SCRIPT_PREFIX = 'script';

	private const URL_SCRIPT_PREFIX  = 'assets/js/admin/';
	private const URL_SCRIPT_POSTFIX = '.js';

	public function __construct() {
		$this->register_scripts();
		$this->load_scripts();
	}

	public function register_scripts(): void {
		foreach ( self::SCRIPTS as $script ) {
			wp_register_script(
				$this->get_script_name( $script ),
				plugins_url( $this->get_script_path( $script ), POWER_BOARD_PLUGIN_FILE ),
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);
		}
	}

	private function get_script_name( string $script ): string {
		return implode( '_', [ PLUGIN_PREFIX, self::PREFIX, self::SCRIPT_PREFIX, $script ] );
	}

	private function get_script_path( string $script ): string {
		return self::URL_SCRIPT_PREFIX . $script . self::URL_SCRIPT_POSTFIX;
	}

	public function load_scripts(): void {
		foreach ( self::SCRIPTS as $script ) {
			$script_name = $this->get_script_name( $script );
			wp_enqueue_script( $this->get_script_name( $script ), '', [], POWER_BOARD_PLUGIN_VERSION, true );
			wp_localize_script(
				$script_name,
				'powerBoardWidgetSettings',
				[
					'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL,
				]
			);
		}
	}
}
