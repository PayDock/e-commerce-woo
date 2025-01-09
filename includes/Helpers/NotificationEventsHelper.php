<?php

namespace PowerBoard\Helpers;

use PowerBoard\Enums\NotificationEventsEnum;

class NotificationEventsHelper {
	public static function events(): array {
		$result = [];

		foreach ( NotificationEventsEnum::cases() as $type ) {
			$result[] = strtolower( $type );
		}

		return $result;
	}
}
