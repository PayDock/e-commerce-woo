<?php

namespace PowerBoard\Enums;

class ConfigAPIEnum {
	public const PRODUCTION_ENVIRONMENT_NAME = 'Production';
	public const SANDBOX_ENVIRONMENT_NAME    = 'Preproduction';
	public const STAGING_ENVIRONMENT_NAME    = 'Staging';
	public const PRODUCTION_API_URL          = 'https://api.powerboard.commbank.com.au/v1/';
	public const SANDBOX_API_URL             = 'https://api.preproduction.powerboard.commbank.com.au/v1/';
	public const STAGING_API_URL             = 'https://api.staging.powerboard.commbank.com.au/v1/';
	public const PRODUCTION_WIDGET_URL       = 'https://widget.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
	public const SANDBOX_WIDGET_URL          = 'https://widget.preproduction.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
	public const STAGING_WIDGET_URL          = 'https://widget.staging.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
	public const PRODUCTION_ENVIRONMENT      = 'production_cba';
	public const SANDBOX_ENVIRONMENT         = 'preproduction_cba';
	public const STAGING_ENVIRONMENT         = 'staging_cba';
}
