<?php

namespace Paydock\Services\Settings;

use Paydock\Abstract\AbstractSettingService;
use Paydock\Enums\SettingsTabs;

class WidgetSettingService extends AbstractSettingService
{
    protected function getId(): string
    {
        return SettingsTabs::Widget->value;
    }
}
