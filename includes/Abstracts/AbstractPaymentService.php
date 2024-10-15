<?php

namespace WooPlugin\Abstracts;

use WooPlugin\Services\SettingsService;
use WC_Payment_Gateway;

abstract class AbstractPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->icon       = apply_filters( 'woocommerce_' . PLUGIN_PREFIX . '_gateway_icon', '' );
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

		$this->method_title       = _x( PLUGIN_TEXT . ' payment', PLUGIN_TEXT . ' payment method',
			'woocommerce-gateway-ppwer-board' );
		$this->method_description = __( 'Allows ' . PLUGIN_TEXT . ' payments.', 'woocommerce-gateway-ppwer-board' );

		$this->init_settings();
	}


	public function woocommerce_before_checkout_form( $arg ) {

	}

	public function payment_scripts() {
		if ( ! is_checkout() || ! $this->is_available() ) {
			return '';
		}

		wp_enqueue_script( PLUGIN_TEXT_DOMAIN . '-form', PLUGIN_URL . 'assets/js/frontend/form.js', [], time(),
			true );
		wp_localize_script( PLUGIN_TEXT_DOMAIN . '-form', 'pluginCardWidgetSettings', [
			'suportedCard'    => 'Visa, Mastercard, Adex',
		] );
		wp_localize_script( PLUGIN_TEXT_DOMAIN . '-form', 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT,
				'pluginPrefix' => PLUGIN_PREFIX,
		] );
		wp_enqueue_style(
			PLUGIN_TEXT_DOMAIN . '-widget-css',
			PLUGIN_URL . 'assets/css/frontend/widget.css',
			[],
			time()
		);

		wp_enqueue_script( PLUGIN_TEXT_DOMAIN . '-api', SettingsService::getInstance()->getWidgetScriptUrl(), [], time(), true );
		wp_localize_script( PLUGIN_TEXT_DOMAIN . '-api', 'widgetSettings', [
				'pluginUrlPrefix' => PLUGIN_URL,
				'pluginTextDomain' => PLUGIN_TEXT_DOMAIN,
				'pluginTextName' => PLUGIN_TEXT,
				'pluginPrefix' => PLUGIN_PREFIX,
		] );
	}
}
