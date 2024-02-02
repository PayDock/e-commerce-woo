<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;
use Paydock\Abstract\AbstractSettingService;
use Paydock\PaydockPlugin;
use Paydock\Services\Settings\LiveConnectionSettingService;
use Paydock\Services\Settings\LogsSettingService;
use Paydock\Services\Settings\SandboxConnectionSettingService;
use Paydock\Services\Settings\WebHooksSettingService;
use Paydock\Services\Settings\WidgetSettingService;

class SettingsTabs extends AbstractEnum
{
    protected const LIVE_CONNECTION = PaydockPlugin::PLUGIN_PREFIX;
    protected const SANDBOX_CONNECTION = PaydockPlugin::PLUGIN_PREFIX . '_sandbox';
    protected const WEBHOOKS = PaydockPlugin::PLUGIN_PREFIX . '_webhooks';
    protected const WIDGET = PaydockPlugin::PLUGIN_PREFIX . '_widget';
    protected const LOG = PaydockPlugin::PLUGIN_PREFIX . '_log';

    public static function secondary(): array
    {
        return array_filter(self::cases(), fn(self $tab) => self::LIVE_CONNECTION()->name !== $tab->name);
    }

    public function getSettingService(): AbstractSettingService
    {
        return match ($this->value) {
            self::LIVE_CONNECTION => new LiveConnectionSettingService(),
            self::SANDBOX_CONNECTION => new SandboxConnectionSettingService(),
            self::WEBHOOKS => new WebHooksSettingService(),
            self::WIDGET => new WidgetSettingService(),
            self::LOG => new LogsSettingService(),
        };
    }
}
