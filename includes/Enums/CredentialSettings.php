<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class CredentialSettings extends AbstractEnum {
	protected const ACCESS_KEY = 'ACCESS_KEY';
	protected const WIDGET_KEY = 'WIDGET_KEY';

	public static function get_hashed(): array {
		return array(
			self::ACCESS_KEY()->name,
			self::WIDGET_KEY()->name,
		);
	}

	public function get_input_type(): string {
		switch ( $this->name ) {
			case self::ACCESS_KEY:
			case self::WIDGET_KEY:
				return 'password';
			default:
				return '';
		}
	}

	public function get_label(): string {
		switch ( $this->name ) {
			case self::ACCESS_KEY:
				return 'API Access Token';
			case self::WIDGET_KEY:
				return 'Widget Access Token';
			default:
				return '';
		}
	}

	public function get_description(): string {
		switch ( $this->name ) {
			case self::ACCESS_KEY:
				return 'Enter your API Access Token. This token is used to securely authenticate your payment operations. It is also used to retrieve the values for the Checkout Template ID fields shown below.';
			case self::WIDGET_KEY:
				return 'Enter your Widget access token. This token is used to render the payment methods, buttons and forms on your checkout page.';
			default:
				return '';
		}
	}
}
