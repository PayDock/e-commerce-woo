<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;
use PowerBoard\Services\Settings\LogsSettingService;
use PowerBoard\Services\Settings\WebHooksSettingService;

class SettingsTabs extends AbstractEnum {
	protected const WIDGET_CONFIGURATION = PLUGIN_PREFIX;
	protected const WEBHOOKS = PLUGIN_PREFIX . '_webhooks';
	protected const LOG = PLUGIN_PREFIX . '_log';

	public static function secondary(): array {
		$allTabs = self::allCases(); // Use a custom method to simulate enum cases.

		return array_filter( $allTabs, function ($tab) {
			return self::WIDGET_CONFIGURATION !== $tab;
		} );
	}

	public static function allCases(): array {
		$RefClass = new \ReflectionClass( static::class);

		return array_map( function (string $name) {
			return static::{$name}();
		}, array_keys( $RefClass->getConstants() ) );
	}

	public function getSettingService() {
		switch ( $this->value ) {
			case self::WIDGET_CONFIGURATION:
				return new WidgetConfigurationSettingService();
			case self::WEBHOOKS:
				return new WebHooksSettingService();
			case self::LOG:
				return new LogsSettingService();
			default:
				return null;
		}
	}
}
