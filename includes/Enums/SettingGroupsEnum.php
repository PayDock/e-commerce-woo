<?php
declare( strict_types=1 );

namespace WooPlugin\Enums;

use WooPlugin\Abstracts\AbstractEnum;

class SettingGroupsEnum extends AbstractEnum {
	public const ENVIRONMENT = 'ENVIRONMENT';
	public const CREDENTIALS = 'CREDENTIALS';
	public const CHECKOUT    = 'CHECKOUT';
}
