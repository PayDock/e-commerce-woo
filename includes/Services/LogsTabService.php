<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;

class LogsTabService extends AbstractSettingService
{
    public function __construct()
    {
        $this->id = self::LOG_TAB;
        $this->enabled = $this->get_option('enabled');

        parent::__construct();
    }
}
