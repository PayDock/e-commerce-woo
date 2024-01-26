<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;

class SandboxConnectionSettingService extends AbstractSettingService
{
    public function __construct()
    {
        $this->id = self::SANDBOX_CONNECTION_TAB;
        $this->enabled = $this->get_option('enabled');

        parent::__construct();
    }
}
