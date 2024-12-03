<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractBlock;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\Checkout\CardPaymentService;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;

final class PowerBoardGatewayBlocks extends AbstractBlock {
	protected const SCRIPT = 'blocks';
	protected $name = 'power_board';
	private $gateway;

	public function initialize() {
		$this->settings = get_option( 'woocommerce_power_board_settings', [] );
		$this->gateway  = new CardPaymentService();
	}

	public function is_active() {
		return $this->gateway->is_available();
	}

	public function get_payment_method_data() {
		SDKAdapterService::getInstance();
		$settingsService = SettingsService::getInstance();
		$userTokens      = [];
		if ( is_user_logged_in() ) {
			$userTokens['tokens'] = ( new UserTokenRepository() )->getUserTokens();
		}

		if ( ! is_admin() ) {
			WC()->cart->calculate_totals();
		}

		return array_merge( $userTokens, [
			// Wordpress data
			'_wpnonce'               => wp_create_nonce( 'process_payment' ),
			'isUserLoggedIn'         => is_user_logged_in(),
			'isSandbox'              => $settingsService->isSandbox(),
			// Woocommerce data
			'amount'                 => WC()->cart->total,
			'currency'               => strtoupper( get_woocommerce_currency() ),
			// Widget
			'title'                  => $settingsService->getWidgetPaymentCardTitle(),
			'description'            => $settingsService->getWidgetPaymentCardDescription(),
			'styles'                 => $settingsService->getWidgetStyles(),
			// Tokens & keys
			'widgetToken'              => $settingsService->getWidgetAccessToken(),
			'selectedToken'          => '',
			'paymentSourceToken'     => '',
			'cvv'                    => '',
			// Card
			'cardSupportedCardTypes' => $settingsService->getCardSupportedCardTypes(),
			'gatewayId'              => $settingsService->getCardGatewayId(),
			// 3DS
			'card3DS'                => $settingsService->getCard3DS(),
			'card3DSServiceId'       => $settingsService->getCard3DSServiceId(),
			'card3DSFlow'            => $settingsService->getCardTypeExchangeOtt(),
			'charge3dsId'            => '',
			// Fraud
			'cardFraud'              => $settingsService->getCardFraud(),
			'cardFraudServiceId'     => $settingsService->getCardFraudServiceId(),
			// DirectCharge
			'cardDirectCharge'       => $settingsService->getCardDirectCharge(),
			// SaveCard
			'cardSaveCard'           => $settingsService->getCardSaveCard(),
			'cardSaveCardOption'     => $settingsService->getCardSaveCardOption(),
			'cardSaveCardChecked'    => false,
			// Other
			'supports'               => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] ),
		] );
	}
}
