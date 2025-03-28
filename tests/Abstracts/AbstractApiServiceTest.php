<?php

use PHPUnit\Framework\TestCase;
use PowerBoard\Abstracts\AbstractApiService;

class AbstractApiServiceTest extends TestCase {

	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_call_with_token_and_successful_response() {
		$service = new class extends AbstractApiService {
			public function __construct() {
				$this->action = 'test';
				$this->allowed_action = [ 'test' => 'GET' ];
			}

			protected function build_endpoint(): string {
				return '/some/endpoint';
			}
		};

		\WP_Mock::userFunction('pb_get_access_token', [
			'return' => 'abc123'
		]);

		\WP_Mock::userFunction('pb_build_api_url', [
			'args' => ['/some/endpoint'],
			'return' => 'https://api.example.com/some/endpoint',
		]);

		\WP_Mock::userFunction('WC', [
			'return' => (object)[ 'version' => '8.9.0' ]
		]);

		\WP_Mock::userFunction('wp_parse_args', [
			'return' => [
				'headers' => [
					'content-type' => 'application/json',
					'x-access-token' => 'abc123',
					'X-Power-Board-Meta' => 'V1.0.0_woocommerce_8.9.0'
				],
				'method'  => 'GET',
				'timeout' => 10,
			]
		]);

		\WP_Mock::userFunction('wp_remote_request', [
			'return' => [ 'body' => '{"result": "ok"}' ]
		]);

		\WP_Mock::userFunction('wp_remote_retrieve_body', [
			'return' => '{"result": "ok"}'
		]);

		// \WP_Mock::passthruFunction('PowerBoard\Helpers\LoggerHelper::log_api_request');

		$response = $service->call();

		$this->assertIsArray($response);
		$this->assertEquals('ok', $response['result']);
	}

	public function test_call_with_wp_error() {
		$service = new class extends AbstractApiService {
			public function __construct() {
				$this->action = 'error_test';
				$this->allowed_action = [ 'error_test' => 'GET' ];
			}

			protected function build_endpoint(): string {
				return '/error/endpoint';
			}
		};

		\WP_Mock::userFunction('pb_get_access_token', [
			'return' => 'abc123'
		]);

		\WP_Mock::userFunction('pb_build_api_url', [
			'args' => ['/error/endpoint'],
			'return' => 'https://api.example.com/error/endpoint',
		]);

		\WP_Mock::userFunction('WC', [
			'return' => (object)[ 'version' => '8.9.0' ]
		]);

		\WP_Mock::userFunction('wp_parse_args', [
			'return' => [
				'headers' => [],
				'method'  => 'GET',
				'timeout' => 10,
			]
		]);

		$wp_error = new \WP_Error('fail', 'Request failed');
		\WP_Mock::userFunction('wp_remote_request', [
			'return' => $wp_error
		]);

		// \WP_Mock::passthruFunction('PowerBoard\Helpers\LoggerHelper::log_api_request');

		$response = $service->call();

		$this->assertArrayHasKey('status', $response);
		$this->assertEquals(403, $response['status']);
		$this->assertEquals($wp_error, $response['error']);
	}

	public function test_call_with_invalid_json_response() {
		$service = new class extends AbstractApiService {
			public function __construct() {
				$this->action = 'bad_json';
				$this->allowed_action = [ 'bad_json' => 'GET' ];
				$this->parameters = ['reference' => 'ref_123'];
			}

			protected function build_endpoint(): string {
				return '/bad/json';
			}
		};

		\WP_Mock::userFunction('pb_get_access_token', [
			'return' => 'abc123'
		]);

		\WP_Mock::userFunction('pb_build_api_url', [
			'args' => ['/bad/json'],
			'return' => 'https://api.example.com/bad/json',
		]);

		\WP_Mock::userFunction('WC', [
			'return' => (object)[ 'version' => '8.9.0' ]
		]);

		\WP_Mock::userFunction('wp_parse_args', [
			'return' => [
				'headers' => [],
				'method'  => 'GET',
				'timeout' => 10,
			]
		]);

		\WP_Mock::userFunction('wp_remote_request', [
			'return' => [ 'body' => 'this is not JSON' ]
		]);

		\WP_Mock::userFunction('wp_remote_retrieve_body', [
			'return' => 'this is not JSON'
		]);

		// \WP_Mock::passthruFunction('PowerBoard\Helpers\LoggerHelper::log_api_request');

		$response = $service->call();

		$this->assertEquals(403, $response['status']);
		$this->assertArrayHasKey('error', $response);
		$this->assertArrayHasKey('body', $response);
	}
}
