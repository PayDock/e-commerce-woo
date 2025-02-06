<?php
declare( strict_types=1 );

namespace PowerBoard\Helpers;

class JsonHelper {
	public static function decode_stringified_json( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			foreach ( $data as &$value ) {
				$value = self::decode_stringified_json( $value );
			}
		} elseif ( is_string( $data ) ) {
			if ( json_decode( $data ) !== null && $data !== json_last_error_msg() ) {
				return json_decode( $data, true );
			} else {
				$data = str_replace( '\\', '\\\\', $data );
				$data = str_replace( '"', '\'', $data );
			}
		}

		return $data;
	}
}
