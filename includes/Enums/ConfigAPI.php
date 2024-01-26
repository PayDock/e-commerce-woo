<?php

namespace PayDock\Enums;

enum ConfigAPI: string
{
    case ProductionApiUrl = 'https://api.paydock.com/v1/';
    case SandboxApiUrl = 'https://api-sandbox.paydock.com/v1/';
    case ProductionEnvironment = 'production';
    case SandboxEnvironment = 'sandbox';
}