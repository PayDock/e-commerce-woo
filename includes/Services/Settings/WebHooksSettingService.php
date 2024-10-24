<?php

namespace WooPlugin\Services\Settings;

use WooPlugin\Abstracts\AbstractSettingService;
use WooPlugin\Enums\SettingsTabs;

class WebHooksSettingService extends AbstractSettingService {
	protected function getId(): string {
		return SettingsTabs::WEBHOOKS()->value;
	}
}
