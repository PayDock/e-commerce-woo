<?php

namespace WooPlugin\Enums;

use WooPlugin\Abstracts\AbstractEnum;
use WooPlugin\WooPluginPlugin;

class OrderListColumns extends AbstractEnum {
	public const AFTER_COLUMN = 'order_status';
	protected const PAYMENT_SOURCE_TYPE = 'PAYMENT_SOURCE_TYPE';

	public function getLabel(): string {
		switch ( $this->name ) {
			case self::PAYMENT_SOURCE_TYPE:
				return PLUGIN_TEXT_NAME . ' Payment Type';
			default:
				return '';
		}
	}

	public function getKey(): string {
		switch ( $this->name ) {
			case self::PAYMENT_SOURCE_TYPE:
				return WooPluginPlugin::PLUGIN_PREFIX . '_payment_source_type';
			default:
				return '';
		}
	}
}
