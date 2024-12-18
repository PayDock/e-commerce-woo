<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class ConfigAPI extends AbstractEnum {
	protected const PRODUCTION_ENVIRONMENT_NAME = 'Production';
	protected const SANDBOX_ENVIRONMENT_NAME = 'Preproduction';
	protected const STAGING_ENVIRONMENT_NAME = 'Staging';

	protected const PRODUCTION_API_URL = 'https://api.powerboard.commbank.com.au/v1/';
	protected const SANDBOX_API_URL = 'https://api.preproduction.powerboard.commbank.com.au/v1/';
	protected const STAGING_API_URL = 'https://api.staging.powerboard.commbank.com.au/v1/';
	protected const PRODUCTION_WIDGET_URL = 'https://widget.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
    protected const SANDBOX_WIDGET_URL = 'https://widget.preproduction.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
    protected const STAGING_WIDGET_URL = 'https://widget.staging.powerboard.commbank.com.au/sdk/{version}/widget.umd.js';
	protected const PRODUCTION_ENVIRONMENT = 'production_cba';
	protected const SANDBOX_ENVIRONMENT = 'preproduction_cba';
	protected const STAGING_ENVIRONMENT = 'staging_cba';
}
