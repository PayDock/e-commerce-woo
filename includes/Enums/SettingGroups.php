<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class SettingGroups extends AbstractEnum {
	protected const ENVIRONMENT = 'ENVIRONMENT';
	protected const CREDENTIALS = 'CREDENTIALS';
	protected const CHECKOUT = 'CHECKOUT';

	public function get_label(): string {
		switch ( $this->name ) {
			case self::ENVIRONMENT:
				return 'Environment';
			case self::CREDENTIALS:
				return 'API Credential';
			case self::CHECKOUT:
				return 'Checkout';
			default:
				return '';
		}
	}
}
