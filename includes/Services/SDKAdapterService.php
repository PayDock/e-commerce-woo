<?php

namespace PowerBoard\Services;

use PowerBoard\API\ChargeService;
use PowerBoard\API\ConfigService;
use PowerBoard\API\GatewayService;
use PowerBoard\API\NotificationService;
use PowerBoard\API\ServiceService;
use PowerBoard\API\TokenService;
use PowerBoard\Services\Settings\WidgetConfigurationSettingService;

class SDKAdapterService {
	private $charge_service  = null;
	private static $instance = null;

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

	public function search_gateway( array $parameters = array() ): array {
		$gateway_service = new GatewayService();

		return $gateway_service->search( $parameters )->call();
	}

	public function search_services( array $parameters = array() ): array {
		$service_service = new ServiceService();

		return $service_service->search( $parameters )->call();
	}

	public function search_notifications( array $parameters = array() ): array {
		$notification_service = new NotificationService();

		return $notification_service->search( $parameters )->call();
	}

	public function create_notification( array $parameters = array() ): array {
		$notification_service = new NotificationService();

		return $notification_service->create( $parameters )->call();
	}

	public function token( array $params = array(
		'gateway_id' => '',
		'type'       => '',
	), ?bool $use_widget_access_token = false ): array {
		$token_service = new TokenService();

		if ( $use_widget_access_token ) {
			return $token_service->create( $params )->callWithWidgetAccessToken();
		}

		return $token_service->create( $params )->call();
	}

	public function capture( array $params ): array {
		$this->init_charge_service();
		return $this->charge_service->capture( $params )->call();
	}

	public function cancel_authorised( array $params ): array {
		$this->init_charge_service();
		return $this->charge_service->cancel_authorised( $params )->call();
	}

	public function refunds( array $params ): array {
		$this->init_charge_service();

		return $this->charge_service->refunds( $params )->call();
	}

	protected function init_charge_service(): ChargeService {
		if ( empty( $this->charge_service ) ) {
			$this->charge_service = new ChargeService();
		}

		return $this->charge_service;
	}
}
