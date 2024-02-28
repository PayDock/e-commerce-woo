<?php

namespace PowerBoard\API;

use PowerBoard\Enums\ConfigAPI;

class ConfigService
{
    public static ?string $environment = null;
    public static ?string $accessToken = null;
    public static ?string $secretKey = null;
    public static ?string $publicKey = null;

    public static function init(string $environment, string $secretKeyOrAccessToken, ?string $publicKey = null)
    {
        self::$environment = $environment;

        if (self::isAccessToken($secretKeyOrAccessToken)) {
            self::$secretKey = null;
            self::$accessToken = $secretKeyOrAccessToken;
        } else {
            self::$secretKey = $secretKeyOrAccessToken;
            self::$accessToken = null;
        }

        self::$publicKey = $publicKey;
    }

    public static function buildApiUrl(?string $endpoint = null): string
    {
        if (self::$environment === ConfigAPI::PRODUCTION_ENVIRONMENT()->value) {
            return ConfigAPI::PRODUCTION_API_URL()->value . $endpoint;
        }

        return ConfigAPI::SANDBOX_API_URL()->value . $endpoint;
    }

    public static function isAccessToken(string $token): bool
    {
        return count(explode('.', $token)) === 3;
    }
}
