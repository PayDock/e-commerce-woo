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
				return 'Enter the API Access Token for authentication. This key is used for authentication to ensure'
						. ' secure communication with the payment gateway.';
			case self::WIDGET_KEY:
				return 'Enter the Widget Access Token for authentication. This key is used for authentication to ensure'
						. ' secure communication with the payment gateway.';
			default:
				return '';
		}
	}
}
