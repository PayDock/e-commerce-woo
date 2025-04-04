<?php
declare( strict_types=1 );

namespace WooPlugin\API;

class ConfigService {
	public static ?string $environment  = null;
	public static ?string $access_token = null;

	public static function init( ?string $environment, ?string $access_token ): void {
		self::$environment  = $environment;
		self::$access_token = $access_token;
	}

	public static function build_api_url( ?string $endpoint = null ): string {
		if ( self::$environment === PLUGIN_PRODUCTION_ENVIRONMENT_VALUE ) {
			return PLUGIN_PRODUCTION_API_URL . $endpoint;
		} elseif ( self::$environment === PLUGIN_STAGING_ENVIRONMENT_VALUE ) {
			return PLUGIN_STAGING_API_URL . $endpoint;
		} else {
			return PLUGIN_SANDBOX_API_URL . $endpoint;
		}
	}
}
