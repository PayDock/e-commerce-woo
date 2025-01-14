<?php

namespace PowerBoard\API;

use PowerBoard\Enums\ConfigAPIEnum;

class ConfigService {
	public static ?string $environment         = null;
	public static ?string $access_token        = null;
	public static ?string $widget_access_token = null;

	public static function init( ?string $environment, ?string $access_token, ?string $widget_access_token = null ) {
		self::$environment         = $environment;
		self::$access_token        = $access_token;
		self::$widget_access_token = $widget_access_token;
	}

	public static function build_api_url( ?string $endpoint = null ): string {
		if ( self::$environment === ConfigAPIEnum::PRODUCTION_ENVIRONMENT ) {
			return ConfigAPIEnum::PRODUCTION_API_URL . $endpoint;
		} elseif ( self::$environment === ConfigAPIEnum::STAGING_ENVIRONMENT ) {
			return ConfigAPIEnum::STAGING_API_URL . $endpoint;
		} else {
			return ConfigAPIEnum::SANDBOX_API_URL . $endpoint;
		}
	}
}
