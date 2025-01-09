<?php

namespace PowerBoard\Helpers;

use PowerBoard\Enums\SettingsTabsEnum;
use PowerBoard\Services\Settings\LogsSettingService;
use PowerBoard\Services\Settings\WebHooksSettingService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

class SettingsTabsHelper {
	public static function get_setting_service( string $key ) {
		switch ( $key ) {
			case SettingsTabsEnum::WIDGET_CONFIGURATION:
				return new WidgetConfigurationSettingService();
			case SettingsTabsEnum::WEBHOOKS:
				return new WebHooksSettingService();
			case SettingsTabsEnum::LOG:
				return new LogsSettingService();
			default:
				return null;
		}
	}
}
