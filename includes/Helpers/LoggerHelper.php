<?php
declare( strict_types=1 );

namespace PowerBoard\Helpers;

use PowerBoard\Enums\APIActionEnum;
use PowerBoard\Enums\LoggerEnum;
use PowerBoard\Helpers\JsonHelper;

class LoggerHelper {
	public static function log_api_request( array $result, string $request_action ): void {
		$has_error = ! empty( $result['error'] );
		$context   = [
			'source'   => POWER_BOARD_PLUGIN_NAME,
			'request'  => $result['request'] ?? null,
			'response' => self::filter_response_by_action(
				$result['response'] ?? [],
				$request_action,
				$has_error
			),
		];

		if ( $has_error ) {
			$context['backtrace'] = true;
		}

		$message = $request_action;

		if ( ! empty( $result['order_id'] ) ) {
			$message             = '[order_id: ' . $result['order_id'] . '] ' . $message;
			$context['order_id'] = $result['order_id'];
		}

		$log_level = $has_error ? LoggerEnum::ERROR : LoggerEnum::INFORMATION;

		/* @noinspection PhpUndefinedFunctionInspection */
		wc_get_logger()->log(
			$log_level,
			$message,
			JsonHelper::decode_stringified_json( $context )
		);
	}

	public static function filter_response_by_action( $response, string $request_action, bool $has_error ): array {
		if ( $has_error ) {
			return (array) $response;
		}

		switch ( $request_action ) {
			case APIActionEnum::CREATE_INTENT:
				if ( ! empty( $response['resource']['data']['token'] ) ) {
					$response['resource']['data']['token'] = '********************';
				}
				break;
			case APIActionEnum::REFUND:
				if ( ! empty( $response['resource']['data']['customer']['payment_source']['ref_token'] ) ) {
					$response['resource']['data']['customer']['payment_source']['ref_token'] = '********************';
				}
				if ( ! empty( $response['resource']['data']['customer']['payment_source']['vault_token'] ) ) {
					$response['resource']['data']['customer']['payment_source']['vault_token'] = '********************';
				}
				break;
		}

		return (array) $response;
	}

	public static function log_callback_event( string $title, array $data = [], string $level = 'info' ): void {
		$context = array_merge(
			[
				'source' => POWER_BOARD_PLUGIN_NAME,
			],
			$data
		);

		if ( isset( $data['order_id'] ) ) {
			$title = '[order_id: ' . $data['order_id'] . '] ' . $title;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		wc_get_logger()->log(
			$level,
			$title,
			JsonHelper::decode_stringified_json( $context )
		);
	}
}
