<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;
use PowerBoard\PowerBoardPlugin;

class OrderListColumns extends AbstractEnum {
	public const AFTER_COLUMN = 'order_status';
	protected const PAYMENT_SOURCE_TYPE = 'PAYMENT_SOURCE_TYPE';

	public function getLabel(): string {
		switch ( $this->name ) {
			case self::PAYMENT_SOURCE_TYPE:
				return 'PowerBoard Payment Type';
			default:
				return '';
		}
	}

	public function getKey(): string {
		switch ( $this->name ) {
			case self::PAYMENT_SOURCE_TYPE:
				return PowerBoardPlugin::PLUGIN_PREFIX . '_payment_source_type';
			default:
				return '';
		}
	}
}
