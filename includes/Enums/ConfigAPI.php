<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class ConfigAPI extends AbstractEnum
{
    protected const PRODUCTION_API_URL = 'https://api.production.powerboard.commbank.com.au/v1/';
    protected const SANDBOX_API_URL = 'https://api.preproduction.powerboard.commbank.com.au/v1/';
    protected const PRODUCTION_ENVIRONMENT = 'production';
    protected const SANDBOX_ENVIRONMENT = 'sandbox';
}