<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class NotificationEvents extends AbstractEnum {
	protected const TRANSACTION_SUCCESS = 'Transaction Success';
	protected const TRANSACTION_FAILURE = 'Transaction Failure';
	protected const REFUND_SUCCESS = 'Refund Successful';

	public static function events(): array {
		$result = [];

		foreach ( self::cases() as $type ) {
			$result[] = strtolower( $type->name );
		}

		return $result;
	}

	public static function toArray(): array {
		$result = [];

		foreach ( self::cases() as $type ) {
			$result[ $type->name ] = $type->value;
		}

		return $result;
	}
}
