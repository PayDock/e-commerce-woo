<?php
declare( strict_types=1 );

namespace WooPlugin\Services\Settings;

use WooPlugin\API\ChargeService;
use WooPlugin\API\ConfigService;
use WooPlugin\Helpers\JsonHelper;

class APIAdapterService {
	private ?ChargeService $charge_service      = null;
	private static ?APIAdapterService $instance = null;

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function initialise( $env, $access_token ): void {
		ConfigService::init( $env, $access_token );
	}

	public function create_checkout_intent( array $params ): array {
		return $this->get_charge_service()->create_checkout_intent( $params )->call();
	}

	public function get_checkout_intent_by_id( array $params ): array {
		return $this->get_charge_service()->get_checkout_intent_by_id( $params )->call();
	}

	public function get_plugin_configuration_by_version(): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$body = JsonHelper::decode_stringified_json( wp_remote_get( PLUGIN_VERSIONS_JSON_URL )['body'] );

		$result = [];
		foreach ( $body['woocommerce'][ PLUGIN_VERSION ] as $key => $value ) {
			if ( $key === 'env' ) {
				foreach ( $value as $env_value ) {
					$result['environment_url'][ $env_value['name'] ] = $env_value['client_sdk_url'];
				}
			} elseif ( $key === 'checkout_version' ) {
				$result['checkout_versions'] = array_combine( $value, $value );
			}
		}

		return $result;
	}

	public function get_configuration_templates_ids( string $version ): array {
		return $this->get_charge_service()->get_configuration_templates_ids( $version )->call();
	}

	public function get_configuration_templates_for_validation(): array {
		return $this->get_charge_service()->get_configuration_templates_for_validation()->call();
	}

	public function get_customisation_templates_ids( string $version ): array {
		return $this->get_charge_service()->get_customisation_templates_ids( $version )->call();
	}

	protected function get_charge_service(): ChargeService {
		if ( empty( $this->charge_service ) ) {
			$this->charge_service = new ChargeService();
		}

		return $this->charge_service;
	}
}
