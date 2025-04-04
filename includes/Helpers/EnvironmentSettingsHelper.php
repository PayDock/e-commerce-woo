<?php
declare( strict_types=1 );

namespace WooPlugin\Helpers;

use WooPlugin\Enums\EnvironmentSettingsEnum;

class EnvironmentSettingsHelper {
	public static function get_input_type( string $key ): string {
		switch ( $key ) {
			case EnvironmentSettingsEnum::ENVIRONMENT:
			default:
				return 'select';
		}
	}

	public static function get_label( string $key ): string {
		switch ( $key ) {
			case EnvironmentSettingsEnum::ENVIRONMENT:
				return 'Environment';
			default:
				return ucfirst( strtolower( str_replace( '_', ' ', $key ) ) );
		}
	}

	public static function get_options_for_ui( string $key ): array {
		switch ( $key ) {
			case EnvironmentSettingsEnum::ENVIRONMENT:
				return self::get_environments_list_for_ui();
			default:
				return [];
		}
	}

	public static function get_environments_list_for_ui(): array {
		$available_environments   = [ ''=> 'Select an environment' ];
		$show_staging_environment = !empty( PLUGIN_STAGING_API_URL );

		if ( $show_staging_environment ) {
			$available_environments = $available_environments + [ PLUGIN_STAGING_ENVIRONMENT_VALUE => PLUGIN_STAGING_ENVIRONMENT_NAME ];
		}

		return $available_environments + [
			PLUGIN_SANDBOX_ENVIRONMENT_VALUE    => PLUGIN_SANDBOX_ENVIRONMENT_NAME,
			PLUGIN_PRODUCTION_ENVIRONMENT_VALUE => PLUGIN_PRODUCTION_ENVIRONMENT_NAME,
		];
	}

	public static function get_default( string $key ): string {
		switch ( $key ) {
			case EnvironmentSettingsEnum::ENVIRONMENT:
			default:
				$result = '';
		}

		return $result;
	}
}
