<?php

namespace PowerBoard\API;

use PowerBoard\Enums\ConfigAPI;

class ConfigService {
	public static $environment = null;
	public static $accessToken = null;
	public static $widgetAccessToken = null;

	public static function init( string $environment, string $accessToken, ?string $widgetAccessToken = null ) {
		self::$environment = $environment;
		self::$accessToken = $accessToken;
		self::$widgetAccessToken = $widgetAccessToken;
	}

	public static function buildApiUrl( ?string $endpoint = null ): string {
		if ( ConfigAPI::PRODUCTION_ENVIRONMENT()->value === self::$environment ) {
			return ConfigAPI::PRODUCTION_API_URL()->value . $endpoint;
		}

		return ConfigAPI::SANDBOX_API_URL()->value . $endpoint;
	}
}
