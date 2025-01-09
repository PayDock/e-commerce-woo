<?php

namespace PowerBoard\Helpers;

use PowerBoard\Enums\OrderListColumnsEnum;

class OrderListColumnsHelper {
	public static function get_label( string $key ): string {
		switch ( $key ) {
			case OrderListColumnsEnum::PAYMENT_SOURCE_TYPE:
				return 'PowerBoard Payment Type';
			default:
				return '';
		}
	}

	public static function get_key( string $key ): string {
		switch ( $key ) {
			case OrderListColumnsEnum::PAYMENT_SOURCE_TYPE:
				return PLUGIN_PREFIX . '_payment_source_type';
			default:
				return '';
		}
	}
}
