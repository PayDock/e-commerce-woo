<?php

namespace PowerBoard\API;

use PowerBoard\Enums\ConfigAPIEnum;

class ConfigService {
	public static $environment         = null;
	public static $access_token        = null;
	public static $widget_access_token = null;

	public static function init( ?string $environment, ?string $accessToken, ?string $widgetAccessToken = null ) {
		self::$environment         = $environment;
		self::$access_token        = $accessToken;
		self::$widget_access_token = $widgetAccessToken;
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
