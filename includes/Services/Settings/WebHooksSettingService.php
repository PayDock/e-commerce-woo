<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\Abstracts\AbstractSettingService;
use PowerBoard\Enums\SettingsTabs;

class WebHooksSettingService extends AbstractSettingService {
	protected function getId(): string {
		return SettingsTabs::WEBHOOKS()->value;
	}
}
