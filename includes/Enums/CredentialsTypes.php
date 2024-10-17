<?php

namespace WooPlugin\Enums;

use WooPlugin\Abstracts\AbstractEnum;

class CredentialsTypes extends AbstractEnum {
	protected const CREDENTIALS = 'Public & Secret Keys';
	protected const ACCESS_KEY = 'Access Token';

	public static function toArray(): array {
		$result = [];

		foreach ( self::cases() as $type ) {
			$result[ $type->name ] = $type->value;
		}

		return $result;
	}
}
