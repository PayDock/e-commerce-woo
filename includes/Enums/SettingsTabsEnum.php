<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class SettingsTabsEnum extends AbstractEnum {
	public const WIDGET_CONFIGURATION = PLUGIN_PREFIX;
	public const WEBHOOKS             = PLUGIN_PREFIX . '_webhooks';
	public const LOG                  = PLUGIN_PREFIX . '_log';
}
