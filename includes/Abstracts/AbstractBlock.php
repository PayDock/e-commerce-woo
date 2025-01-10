<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use PowerBoard\Services\SettingsService;

abstract class AbstractBlock extends AbstractPaymentMethodType {
	private static $is_load = false;
	protected $script;

	public function __construct() {
		if ( defined( static::class . '::SCRIPT' ) ) {
			$this->script = static::SCRIPT;
		}
	}

	public function is_active() {
		return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
	}

	public function get_payment_method_script_handles() {
		if ( ! self::$is_load && is_checkout() ) {
			wp_enqueue_script(
				'power-board-form',
				POWER_BOARD_PLUGIN_URL . 'assets/js/frontend/form.js',
				[],
				POWER_BOARD_PLUGIN_VERSION,
				true
			);

			wp_localize_script(
				'power-board-form',
				'powerBoardWidgetSettings',
				[
					'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL,
				]
			);
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
			wp_localize_script(
				'power-board-api',
				'powerBoardWidgetSettings',
				[
					'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL,
				]
			);

			self::$is_load = true;
		}

		$script_path       = 'assets/build/js/frontend/' . $this->script . '.js';
		$script_asset_path = 'assets/build/js/frontend/' . $this->script . '.asset.php';
		$script_url        = plugins_url( $script_path, POWER_BOARD_PLUGIN_FILE );
		$script_name       = PLUGIN_PREFIX . '-' . $this->script;
		$script_asset      = file_exists( $script_asset_path ) ? require $script_asset_path : [
			'dependencies' => [],
			'version'      => POWER_BOARD_PLUGIN_VERSION,
		];

		wp_register_script( $script_name, $script_url, $script_asset['dependencies'], $script_asset['version'], true );
		wp_localize_script(
			$script_name,
			'powerBoardWidgetSettings',
			[
				'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL,
			]
		);
		wp_localize_script(
			'power-board-api',
			'powerBoardWidgetSettings',
			[
				'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL,
			]
		);
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( $script_name );
		}

		return [ $script_name ];
	}
}
