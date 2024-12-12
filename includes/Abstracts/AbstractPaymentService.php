<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Services\SettingsService;
use WC_Payment_Gateway;

abstract class AbstractPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->icon       = apply_filters( 'woocommerce_power_board_gateway_icon', '' );
		$this->has_fields = true;
		$this->supports   = [
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'multiple_subscriptions',
			'default_credit_card_form',
		];

		$this->method_title       = _x( 'PowerBoard payment', 'PowerBoard payment method',
			'woocommerce-gateway-ppwer-board' );
		$this->method_description = __( 'Allows PowerBoard payments.', 'woocommerce-gateway-ppwer-board' );

		$this->init_settings();
	}


	public function woocommerce_before_checkout_form( $arg ) {

	}

	public function payment_scripts() {
		if ( ! is_checkout() || ! $this->is_available() ) {
			return '';
		}

		wp_enqueue_script( 'power-board-form', POWER_BOARD_PLUGIN_URL . 'assets/js/frontend/form.js', [], time(),
			true );
		wp_localize_script( 'power-board-form', 'powerBoardCardWidgetSettings', [
			'suportedCard'    => 'Visa, Mastercard, Adex',
		] );
		wp_localize_script( 'power-board-form', 'powerBoardWidgetSettings', [
			'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
		] );
		wp_localize_script( 'power-board-form', 'PowerBoardAjax', [
    			'url'         => admin_url( 'admin-ajax.php' ),
    			'wpnonce'     => wp_create_nonce( 'create_wallet_charge' ),
    		] );
		wp_enqueue_style(
			'power-board-widget-css',
			POWER_BOARD_PLUGIN_URL . 'assets/css/frontend/widget.css',
			[],
			time()
		);

		wp_enqueue_script( 'power-board-api', SettingsService::getInstance()->getWidgetScriptUrl(), [], time(), true );
		wp_localize_script( 'power-board-api', 'powerBoardWidgetSettings', [
			'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
		] );
	}
}
