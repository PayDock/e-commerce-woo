<?php

namespace PowerBoard\Abstracts;

use PowerBoard\Services\SettingsService;
use PowerBoard\Services\TemplateService;
use WC_Payment_Gateway;

abstract class AbstractPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
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

		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
	}

	public function woocommerce_before_checkout_form( $arg ) {

	}

	public function payment_scripts() {
		if ( ! is_checkout() || ! $this->is_available() ) {
			return '';
		}
		wp_enqueue_script( 'power-board-api', SettingsService::getInstance()->getWidgetScriptUrl(), [], time(), true );
		wp_enqueue_script( 'axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', [], time(), true );
		wp_enqueue_script( 'power-board-form', POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/form.js', [], time(), true );
		wp_enqueue_script( 'power-board-classic-form', POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/classic-form.js', [], time(), true );
		wp_localize_script( 'power-board-form', 'powerBoardCardWidgetSettings', [
			'suportedCard' => 'Visa, Mastercard, Adex',
		] );
		wp_enqueue_style( 'power-board-widget-css', POWER_BOARD_PLUGIN_URL . '/assets/css/frontend/widget.css', [], time() );

		wp_localize_script( 'power-board-form', 'PowerBoardAjax', [
			'url'         => admin_url( 'admin-ajax.php' ),
			'wpnonce'     => wp_create_nonce( 'power-board-create-wallet-charge' ),
			'wpnonce_3ds' => wp_create_nonce( 'power_board_get_vault_token' ),
			'wpnonce_error' => wp_create_nonce( 'power_board_create_error_notice' ),
		] );
		wp_localize_script( 'power-board-classic-form', 'PowerBoardAjax', [
			'url'         => admin_url( 'admin-ajax.php' ),
			'wpnonce'     => wp_create_nonce( 'power-board-create-wallet-charge' ),
			'wpnonce_3ds' => wp_create_nonce( 'power_board_get_vault_token' ),
			'wpnonce_error' => wp_create_nonce( 'power_board_create_error_notice' ),
		] );
		wp_enqueue_script( 'axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', [], time(), true );
	}

	public function payment_fields() {
		$cart_hash = WC()->cart->get_cart_hash();

		$template = new TemplateService ( $this );
		$template->includeCheckoutHtml( 'method-form', [
			'description' => $this->description,
			'id'          => $this->id,
			'settings'    => wp_json_encode( $this->get_payment_method_data() ),
			'cart_hash'   => $cart_hash,
			'nonce'       => wp_create_nonce( 'process_payment' ),
		] );
	}

	abstract public function get_payment_method_data(): array;
}
