<?php

namespace PowerBoard\Services;

use PowerBoard\API\ChargeService;
use PowerBoard\API\ConfigService;
use PowerBoard\API\NotificationService;
use PowerBoard\API\TokenService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

class SDKAdapterService {
	private ?ChargeService $charge_service      = null;
	private static ?SDKAdapterService $instance = null;

	public function __construct() {
		$settings = new WidgetConfigurationSettingService();
		$this->initialise( $settings->get_environment(), $settings->get_access_token(), $settings->get_widget_access_token() );
	}

	public function initialise( ?string $env, ?string $access_token, ?string $widget_access_token ): void {
		ConfigService::init( $env, $access_token, $widget_access_token );
	}

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function search_notifications( array $parameters = [] ): array {
		$notification_service = new NotificationService();

		return $notification_service->search( $parameters )->call();
	}

	public function create_notification( array $parameters = [] ): array {
		$notification_service = new NotificationService();

		return $notification_service->create( $parameters )->call();
	}

	public function token( array $params = [
		'gateway_id' => '',
		'type'       => '',
	]): array {
		$token_service = new TokenService();

		return $token_service->create( $params )->call_with_widget_access_token();
	}

	public function refunds( array $params ): array {
		return $this->init_charge_service()->refunds( $params )->call();
	}

	protected function init_charge_service(): ChargeService {
		if ( empty( $this->charge_service ) ) {
			$this->charge_service = new ChargeService();
		}

		return $this->charge_service;
	}
}
