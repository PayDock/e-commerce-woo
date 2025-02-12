<?php
declare( strict_types=1 );

namespace PowerBoard\Helpers;

class SettingsHelper {
	public static function get_option_name( string $id, array $fragments ): string {
		return implode( '_', array_merge( [ $id ], $fragments ) );
	}
}
