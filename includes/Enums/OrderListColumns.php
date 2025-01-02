<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class OrderListColumns extends AbstractEnum {
	public const AFTER_COLUMN = 'order_status';
	protected const PAYMENT_SOURCE_TYPE = 'PAYMENT_SOURCE_TYPE';

	public function get_label(): string {
		switch ( $this->name ) {
			case self::PAYMENT_SOURCE_TYPE:
				return 'PowerBoard Payment Type';
			default:
				return '';
		}
	}

	public function get_key(): string {
		switch ( $this->name ) {
			case self::PAYMENT_SOURCE_TYPE:
				return PLUGIN_PREFIX . '_payment_source_type';
			default:
				return '';
		}
	}
}
