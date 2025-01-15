<?php

namespace PowerBoard\Helpers;

use PowerBoard\Enums\LoggerEnum;

class LoggerHelper {
	public static function log_request(array $result, string $message ): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		wc_get_logger()->log(
			$result['error'] ? LoggerEnum::ERROR : LoggerEnum::INFO,
			$message,
			self::decode_stringified_json(
				[
					'request'  => $result['request'],
					'response' => $result['response'],
				]
			),
		);
	}

	public static function decode_stringified_json( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			foreach ( $data as &$value ) {
				$value = self::decode_stringified_json( $value );
			}
		} elseif ( is_string( $data ) ) {
			if ( json_decode( $data ) !== null && $data !== json_last_error_msg() ) {
				return json_decode( $data, true );
			}
		}

		return $data;
	}
}
