<?php

namespace Paydock\Services;

use Paydock\Abstract\AbstractSettingService;

class WidgetSettingService extends AbstractSettingService
{
    public function __construct()
    {
        $this->id = self::WIDGET_TAB;
        $this->enabled = $this->get_option('enabled');

        parent::__construct();
    }
}
