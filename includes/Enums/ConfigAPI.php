<?php

namespace WooPlugin\Enums;

use WooPlugin\Abstracts\AbstractEnum;

class ConfigAPI extends AbstractEnum {
	protected const PRODUCTION_API_URL = PLUGIN_PRODUCTION_API_URL;
	protected const SANDBOX_API_URL = PLUGIN_SANDBOX_API_URL;
	protected const PRODUCTION_ENVIRONMENT = PLUGIN_PRODUCTION_ENVIRONMENT;
	protected const SANDBOX_ENVIRONMENT = PLUGIN_SANDBOX_ENVIRONMENT;
}
