<?php
declare( strict_types=1 );

namespace WooPlugin\Services;

use WooPlugin\API\ChargeService;
use WooPlugin\API\ConfigService;
use WooPlugin\Services\PaymentGateway\MasterWidgetPaymentService;

class SDKAdapterService {
	private ?ChargeService $charge_service      = null;
	private static ?SDKAdapterService $instance = null;

	public static function get_instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$settings = MasterWidgetPaymentService::get_instance();
		$this->initialise( $settings->get_environment(), $settings->get_access_token() );
	}

	public function initialise( ?string $env, ?string $access_token ): void {
		ConfigService::init( $env, $access_token );
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
