<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class CredentialSettings extends AbstractEnum {
	protected const SANDBOX = 'SANDBOX';
	protected const ACCESS_KEY = 'ACCESS_KEY';
	protected const WIDGET_KEY = 'WIDGET_KEY';

	public static function getHashed(): array {
		return [
			self::ACCESS_KEY()->name,
			self::WIDGET_KEY()->name,
		];
	}

	public function getInputType(): string {
		switch ( $this->name ) {
			case self::ACCESS_KEY:
			case self::WIDGET_KEY:
				return 'password';
			case self::SANDBOX:
				return 'checkbox';
			default:
				return '';
		}
	}

	public function getLabel(): string {
		switch ( $this->name ) {
			case self::ACCESS_KEY:
				return 'API Access Token';
			case self::WIDGET_KEY:
				return 'Widget Access Token';
			case self::SANDBOX:
				return 'Sandbox';
			default:
				return '';
		}
	}

	public function getDescription(): string {
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

	public function getInputAttributes(): array {

		$attributes = [];

		if ( $this->getInputType() === 'password' ) {
			$attributes['autocomplete'] = 'off';
		}

		return $attributes;

	}

}
