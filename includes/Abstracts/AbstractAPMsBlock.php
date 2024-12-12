<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Repositories\UserCustomerRepository;
use PowerBoard\Services\SettingsService;

abstract class AbstractAPMsBlock extends AbstractBlock {
	protected $gateway;

	public function __construct() {
		$aPMsTypeId = $this->getType()->getId();

		$this->name   = 'power_board_' . $aPMsTypeId . '_a_p_m_s_block';
		$this->script = $aPMsTypeId . '-a-p-m-s';

		parent::__construct();
	}

	abstract public function getType(): OtherPaymentMethods;

	public function get_payment_method_data(): array {
		$settingsService = SettingsService::getInstance();
		$payment         = $this->getType();

		$userCustomers = [];
		if ( is_user_logged_in() ) {
			$userCustomers = [
				'customers' => ( new UserCustomerRepository() )->getUserCustomers(),
			];
		}

		if ( ! is_admin() ) {
			WC()->cart->calculate_totals();
		}

		return array_merge( $userCustomers, [
			// Wordpress data
			'isUserLoggedIn'     => is_user_logged_in(),
			'isSandbox'          => $settingsService->isSandbox(),
			// Woocommerce data
			'amount'             => WC()->cart->total,
			'currency'           => strtoupper( get_woocommerce_currency() ),
			// Widget
			'title'              => $settingsService->getWidgetPaymentAPMTitle( $payment ),
			'description'        => $settingsService->getWidgetPaymentAPMDescription( $payment ),
			'styles'             => $settingsService->getWidgetStyles(),
			// Apms
			'enable'             => $settingsService->isAPMsEnabled( $payment ),
			'gatewayId'          => $settingsService->getAPMsGatewayId( $payment ),
			// Tokens & keys
			'widgetToken'          => $settingsService->getWidgetAccessToken(),
			'paymentSourceToken' => '',
			// SaveCard
			'saveCard'           => $settingsService->isAPMsSaveCard( $payment ),
			// DirectCharge
			'directCharge'       => $settingsService->isAPMsDirectCharge( $payment ),
			// Fraud
			'fraud'              => $settingsService->isAPMsFraud( $payment ),
			'fraudServiceId'     => $settingsService->getAPMsFraudServiceId( $payment ),
			// Other
			'supports'           => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] ),
			'pickupLocations'    => get_option( 'pickup_location_pickup_locations' ),
			'total_limitation'   => $settingsService->getWidgetPaymentAPMsMinMax( $payment ),
		] );
	}

	public function is_active() {
		return $this->gateway->is_available();
	}
}
