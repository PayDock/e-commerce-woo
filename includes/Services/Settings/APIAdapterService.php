<?php

namespace PowerBoard\Services\Settings;

use PowerBoard\API\ChargeService;
use PowerBoard\API\ConfigService;

class APIAdapterService {
	private ?ChargeService $charge_service      = null;
	private static ?APIAdapterService $instance = null;

	public function initialise( $env, $access_token, $widget_access_token ): void {
		ConfigService::init( $env, $access_token, $widget_access_token );
	}

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function create_checkout_intent( array $params ): array {
		return $this->get_charge_service()->create_checkout_intent( $params )->call();
	}

	public function get_configuration_templates_ids( string $version ): array {
		return $this->get_charge_service()->get_configuration_templates_ids( $version )->call();
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
