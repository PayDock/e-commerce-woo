<?php
declare( strict_types=1 );

namespace WooPlugin\Helpers;

use WooPlugin\Enums\SettingGroupsEnum;

class SettingGroupsHelper {
	public static function get_label( string $key ): string {
		switch ( $key ) {
			case SettingGroupsEnum::ENVIRONMENT:
				return 'Environment';
			case SettingGroupsEnum::CREDENTIALS:
				return 'API Credentials';
			case SettingGroupsEnum::CHECKOUT:
				return 'Checkout';
			default:
				return '';
		}
	}
}
