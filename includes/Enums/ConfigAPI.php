<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class ConfigAPI extends AbstractEnum {
	protected const PRODUCTION_API_URL = 'https://api.powerboard.commbank.com.au/v1/';
	protected const SANDBOX_API_URL = 'https://api.preproduction.powerboard.commbank.com.au/v1/';
	protected const PRODUCTION_ENVIRONMENT = 'production_cba';
	protected const SANDBOX_ENVIRONMENT = 'preproduction_cba';
}
