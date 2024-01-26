<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;

class LiveConnectionSettingService extends AbstractSettingService
{
    public function __construct()
    {
        $this->id = self::LIVE_CONNECTION_TAB;
        $this->enabled = $this->get_option('enabled');

        parent::__construct();
    }
}
