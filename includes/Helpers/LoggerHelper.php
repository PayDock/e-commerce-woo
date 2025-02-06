<?php
declare( strict_types=1 );

namespace PowerBoard\Helpers;

use PowerBoard\Enums\APIActionEnum;
use PowerBoard\Enums\LoggerEnum;

class LoggerHelper {
	public static function log_api_request( array $result, string $request_action ): void {
		$has_error = ! empty( $result['error'] );
		$context   = [
			'source'   => PLUGIN_NAME,
			'request'  => $result['request'],
			'response' => self::filter_response_by_action( $result['response'], $request_action, $has_error ),
		];
		if ( $has_error ) {
			$context['backtrace'] = true;
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		wc_get_logger()->log(
			$has_error ? LoggerEnum::ERROR : LoggerEnum::INFORMATION,
			$request_action,
			JsonHelper::decode_stringified_json( $context ),
		);
	}

	public static function filter_response_by_action( $response, $request_action, $has_error ): array {
		if ( $has_error ) {
			return $response;
		}

		switch ( $request_action ) {
			case APIActionEnum::CREATE_INTENT:
				$response['resource']['data']['token'] = '********************';
				break;
			case APIActionEnum::REFUND:
				$response['resource']['data']['customer']['payment_source']['ref_token']   = '********************';
				$response['resource']['data']['customer']['payment_source']['vault_token'] = '********************';
				break;
		}

		return $response;
	}
}
