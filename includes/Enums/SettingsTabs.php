<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;
use PowerBoard\Abstract\AbstractSettingService;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\Settings\LiveConnectionSettingService;
use PowerBoard\Services\Settings\LogsSettingService;
use PowerBoard\Services\Settings\SandboxConnectionSettingService;
use PowerBoard\Services\Settings\WebHooksSettingService;
use PowerBoard\Services\Settings\WidgetSettingService;

class SettingsTabs extends AbstractEnum
{
    protected const LIVE_CONNECTION = PowerBoardPlugin::PLUGIN_PREFIX;
    protected const SANDBOX_CONNECTION = PowerBoardPlugin::PLUGIN_PREFIX . '_sandbox';
    protected const WEBHOOKS = PowerBoardPlugin::PLUGIN_PREFIX . '_webhooks';
    protected const WIDGET = PowerBoardPlugin::PLUGIN_PREFIX . '_widget';
    protected const LOG = PowerBoardPlugin::PLUGIN_PREFIX . '_log';

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
