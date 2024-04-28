<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\PowerBoardPlugin;
use PowerBoard\Services\SettingsService;

abstract class AbstractWalletBlock extends AbstractBlock {
	public const AFTERPAY_SESSION_KEY = PowerBoardPlugin::PLUGIN_PREFIX . '_afterpay_payment_session_token';

	protected $gateway;

	public function __construct() {
		$walletTypeId = $this->getType()->getId();

		$this->name = 'power_board_' . $walletTypeId . '_wallet_block';
		$this->script = $walletTypeId . '-wallet';

		parent::__construct();
	}

	abstract public function getType(): WalletPaymentMethods;

	public function get_payment_method_data(): array {
		$settings = SettingsService::getInstance();
		$payment = $this->getType();

		$result = [
			'_wpnonce' => wp_create_nonce( 'process_payment' ),
			'title' => $settings->getWidgetPaymentWalletTitle( $payment ),
			'description' => $settings->getWidgetPaymentWalletDescription( $payment ),
			'publicKey' => $settings->getPublicKey(),
			'isSandbox' => $settings->isSandbox(),
			'styles' => $settings->getWidgetStyles(),
		];

		if (
			( WalletPaymentMethods::AFTERPAY()->name === $payment->name )
			&& ! empty( $_SESSION[ self::AFTERPAY_SESSION_KEY ] )
		) {
			$result['afterpayChargeId'] = sanitize_text_field( $_SESSION[ self::AFTERPAY_SESSION_KEY ] );
		}

		$result['wallets'][ strtolower( $payment->name ) ] = [ 
			'gatewayId' => $settings->getWalletGatewayId( $payment ),
			'fraud' => $settings->isWalletFraud( $payment ),
			'fraudServiceId' => $settings->getWalletFraudServiceId( $payment ),
			'directCharge' => $settings->getWalletFraudServiceId( $payment ),
		];

		if ( WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $payment->name ) {
			$result[ strtolower( $payment->name ) ]['payLater'] = $settings->isPayPallSmartButtonPayLater();
		}


		return $result;
	}

	public function is_active() {
		return $this->gateway->is_available();
	}
}
