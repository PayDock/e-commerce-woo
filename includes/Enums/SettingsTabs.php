<?php

namespace WooPlugin\Enums;

use WooPlugin\Abstracts\AbstractEnum;
use WooPlugin\WooPluginPlugin;
use WooPlugin\Services\Settings\LiveConnectionSettingService;
use WooPlugin\Services\Settings\LogsSettingService;
use WooPlugin\Services\Settings\SandboxConnectionSettingService;
use WooPlugin\Services\Settings\WebHooksSettingService;
use WooPlugin\Services\Settings\WidgetSettingService;

class SettingsTabs extends AbstractEnum {
	protected const LIVE_CONNECTION = WooPluginPlugin::PLUGIN_PREFIX;
	protected const SANDBOX_CONNECTION = WooPluginPlugin::PLUGIN_PREFIX . '_sandbox';
	protected const WEBHOOKS = WooPluginPlugin::PLUGIN_PREFIX . '_webhooks';
	protected const WIDGET = WooPluginPlugin::PLUGIN_PREFIX . '_widget';
	protected const LOG = WooPluginPlugin::PLUGIN_PREFIX . '_log';

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
			default:
				return null;
		}
	}
}
