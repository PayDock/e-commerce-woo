<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\SettingsTabsEnum;

class WebHooksSettingService extends AbstractSettingService {
	protected function get_id(): string {
		return SettingsTabsEnum::WEBHOOKS;
	}
}
