<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\Settings\LiveConnectionSettingService;
use PowerBoard\Services\Settings\LogsSettingService;
use PowerBoard\Services\Settings\AdvancedSettingService;
use PowerBoard\Services\Settings\SandboxConnectionSettingService;
use PowerBoard\Services\Settings\WebHooksSettingService;
use PowerBoard\Services\Settings\WidgetSettingService;

class SettingsTabs extends AbstractEnum {
	protected const LIVE_CONNECTION = PowerBoardPlugin::PLUGIN_PREFIX;
	protected const SANDBOX_CONNECTION = PowerBoardPlugin::PLUGIN_PREFIX . '_sandbox';
	protected const WEBHOOKS = PowerBoardPlugin::PLUGIN_PREFIX . '_webhooks';
	protected const WIDGET = PowerBoardPlugin::PLUGIN_PREFIX . '_widget';
	protected const LOG = PowerBoardPlugin::PLUGIN_PREFIX . '_log';
	protected const ADVANCED = PowerBoardPlugin::PLUGIN_PREFIX . '_advanced';

	public static function secondary(): array {
		$allTabs = self::allCases(); // Use a custom method to simulate enum cases.

		return array_filter( $allTabs, function ($tab) {
			return self::LIVE_CONNECTION !== $tab;
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
			case self::LIVE_CONNECTION:
				return new LiveConnectionSettingService();
			case self::SANDBOX_CONNECTION:
				return new SandboxConnectionSettingService();
			case self::WEBHOOKS:
				return new WebHooksSettingService();
			case self::WIDGET:
				return new WidgetSettingService();
			case self::LOG:
				return new LogsSettingService();
			case self::ADVANCED:
				return new AdvancedSettingService();
			default:
				return null;
		}
	}
}
