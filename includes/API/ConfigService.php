<?php

namespace PowerBoard\API;

use PowerBoard\Enums\ConfigAPI;

class ConfigService {
	public static $environment = null;
	public static $accessToken = null;
	public static $widgetAccessToken = null;
	public static $secretKey = null;
	public static $publicKey = null;

	public static function init( string $environment, string $secretKeyOrAccessToken, ?string $publicKeyOrWidgetAccessToken = null ) {
		self::$environment = $environment;

		if ( self::isAccessToken( $secretKeyOrAccessToken ) ) {
			self::$secretKey = null;
			self::$publicKey = null;
			self::$accessToken = $secretKeyOrAccessToken;
			self::$widgetAccessToken = $publicKeyOrWidgetAccessToken;
		} else {
			self::$secretKey = $secretKeyOrAccessToken;
		  self::$publicKey = $publicKeyOrWidgetAccessToken;
			self::$accessToken = null;
			self::$widgetAccessToken = null;
		}

	}

	public static function isAccessToken( string $token ): bool {
		return count( explode( '.', $token ) ) === 3;
	}

	public static function buildApiUrl( ?string $endpoint = null ): string {
		if ( self::$environment === ConfigAPI::PRODUCTION_ENVIRONMENT()->value  ) {
			return ConfigAPI::PRODUCTION_API_URL()->value . $endpoint;
		} else if ( self::$environment === ConfigAPI::STAGING_ENVIRONMENT()->value ) {
			return ConfigAPI::STAGING_API_URL()->value . $endpoint;
		} else {
			return ConfigAPI::SANDBOX_API_URL()->value . $endpoint;
		}
	}
}
