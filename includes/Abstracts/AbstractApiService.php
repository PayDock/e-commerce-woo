<?php declare( strict_types=1 );
namespace PowerBoard\Abstracts;

use LogicException;
use PowerBoard\API\ConfigService;
use PowerBoard\Helpers\LoggerHelper;

abstract class AbstractApiService {
	const METHOD_GET  = 'GET';
	const METHOD_POST = 'POST';

	protected string $action;
	protected ?string $request_action = null;
	protected array $parameters       = [];
	protected array $allowed_action   = [];

	public function call_with_widget_access_token(): array {
		$args = [
			'headers' => [
				'content-type' => 'application/json',
			],
		];

		if ( ! empty( ConfigService::$widget_access_token ) ) {
			$args['headers']['x-access-token'] = ConfigService::$widget_access_token;
		}

		return $this->run_call( $args );
	}

	public function call(): array {
		$args = [
			'headers' => [
				'content-type' => 'application/json',
			],
		];

		if ( ! empty( ConfigService::$access_token ) ) {
			$args['headers']['x-access-token'] = ConfigService::$access_token;
		}

		return $this->run_call( $args );
	}

	/**
	 * Uses functions (wp_json_encode, wp_parse_args, _wp_http_get_object) from WordPress
	 * Uses a function (WC) from WooCommerce
	 */
	protected function run_call( $args ): array {
		$url = ConfigService::build_api_url( $this->build_endpoint() );
		/* @noinspection PhpUndefinedFunctionInspection */
		$args['headers']['X-Power-Board-Meta'] = 'V'
												. POWER_BOARD_PLUGIN_VERSION
												. '_woocommerce_'
												. WC()->version;

		switch ( $this->allowed_action[ $this->action ] ) {
			case 'POST':
				/* @noinspection PhpUndefinedFunctionInspection */
				$args['body'] = wp_json_encode( $this->parameters, JSON_PRETTY_PRINT );
				$parsed_args  = wp_parse_args(
					$args,
					[
						'method'  => 'POST',
						'timeout' => 10,
					]
				);
				break;
			case 'DELETE':
				/* @noinspection PhpUndefinedFunctionInspection */
				$parsed_args = wp_parse_args(
					$args,
					[
						'method'  => 'DELETE',
						'timeout' => 10,
					]
				);
				break;
			default:
				/* @noinspection PhpUndefinedFunctionInspection */
				$parsed_args = wp_parse_args(
					$args,
					[
						'method'  => 'GET',
						'timeout' => 10,
					]
				);
		}
		/* @noinspection PhpUndefinedFunctionInspection */
		$request = _wp_http_get_object()->request( $url, $parsed_args );

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( is_wp_error( $request ) ) {
			return [
				'status' => 403,
				'error'  => $request,
			];
		}

		$body = json_decode( $request['body'], true );

		if ( $body === null && json_last_error() !== JSON_ERROR_NONE ) {
			return [
				'status' => 403,
				'error'  => [ 'message' => 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later. ' ],
				'body'   => $request['body'],
			];
		}

		LoggerHelper::log_api_request(
			[
				'request'  => [
					'url'     => $url,
					'method'  => $parsed_args['method'],
					'payload' => $parsed_args['body'] ?? '',
				],
				'response' => $body,
				'error'    => $body['error'],
			],
			! empty( $this->request_action ) ? $this->request_action : $url,
		);

		return $body;
	}

	/**
	 * Uses a function (esc_html) from WordPress
	 *
	 * @noinspection PhpUndefinedFunctionInspection
	 * @throws LogicException If action is not allowed
	 */
	protected function set_action( $action ): void {
		if ( empty( $this->allowed_action[ $action ] ) ) {

			/* translators: %s: Missing action name. */
			throw new LogicException( esc_html( sprintf( __( 'Not allowed action: %s', 'power-board' ), $action ) ) );
		}

		$this->action = $action;
	}
}
