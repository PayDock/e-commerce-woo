<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractSettingService;
use Paydock\Services\Settings\LiveConnectionSettingService;
use Paydock\Services\Settings\LogsSettingService;
use Paydock\Services\Settings\SandboxConnectionSettingService;
use Paydock\Services\Settings\WidgetSettingService;

enum SettingsTabs: string
{
    case LiveConnection = 'pay_dock';
    case SandBoxConnection = 'pay_dock_sandbox';
    case Widget = 'pay_dock_widget';
    case Log =  'pay_dock_log';

    public static function secondary(): array
    {
        return array_filter(self::cases(), fn(self $tab) => self::LiveConnection !== $tab);
    }

    public function getSettingService(): AbstractSettingService
    {
        return match ($this) {
            self::LiveConnection => new LiveConnectionSettingService(),
            self::SandBoxConnection => new SandboxConnectionSettingService(),
            self::Widget => new WidgetSettingService(),
            self::Log => new LogsSettingService(),
        };
    }
}
