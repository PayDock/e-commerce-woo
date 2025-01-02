<?php

namespace PowerBoard\API;

use PowerBoard\Enums\ConfigAPI;

class ConfigService {
	public static $environment = null;
	public static $access_token = null;
	public static $widget_access_token = null;

	public static function init( ?string $environment, ?string $accessToken, ?string $widgetAccessToken = null ) {
		self::$environment = $environment;
		self::$access_token = $accessToken;
		self::$widget_access_token = $widgetAccessToken;
	}

	public static function build_api_url( ?string $endpoint = null ): string {
  		if ( self::$environment === ConfigAPI::PRODUCTION_ENVIRONMENT()->value  ) {
  			return ConfigAPI::PRODUCTION_API_URL()->value . $endpoint;
  		} else if ( self::$environment === ConfigAPI::STAGING_ENVIRONMENT()->value ) {
  			return ConfigAPI::STAGING_API_URL()->value . $endpoint;
  		} else {
  			return ConfigAPI::SANDBOX_API_URL()->value . $endpoint;
  		}
  	}
}
