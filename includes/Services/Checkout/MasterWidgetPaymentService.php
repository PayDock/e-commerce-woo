<?php
/**
 * This file uses classes from WooCommerce
 *
 * @noinspection PhpUndefinedClassInspection
 */

namespace PowerBoard\Services\Checkout;

use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\TemplateService;
use WC_Payment_Gateway;

/**
 * Some properties used comes from the extension WC_Payment_Gateway from WooCommerce
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $method_title
 * @property string method_description
 * @property bool $has_fields
 * @property array $supports
 */
class MasterWidgetPaymentService extends WC_Payment_Gateway {
	/**
	 * Uses functions (_x, add_action) from WordPress
	 * Uses a method (init_settings) from WC_Payment_Gateway
	 * Uses a property (method_description) from WC_Payment_Gateway
	 */
	public function __construct() {
		$this->id         = 'power_board_gateway';
		$this->has_fields = true;
		$this->supports   = [ 'products', 'default_credit_card_form' ];

		/* @noinspection PhpUndefinedFunctionInspection */
		$this->method_title = _x( 'PowerBoard payment', 'PowerBoard payment method', 'power-board' );

		// Load the settings.
		/* @noinspection PhpUndefinedMethodInspection */
		$this->init_settings();

		// Define user set variables.
		SettingsService::get_instance();
		$this->title       = $this->method_title;
		$this->description = $this->method_description;

		// Actions.
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_scheduled_subscription_payment_power_board', [ $this, 'process_subscription_payment' ], 10, 2 );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wp_ajax_nopriv_power_board_create_error_notice', [ $this, 'power_board_create_error_notice' ], 20 );
		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'wp_ajax_power_board_create_error_notice', [ $this, 'power_board_create_error_notice' ], 20 );

		/* @noinspection PhpUndefinedFunctionInspection */
		add_action( 'woocommerce_checkout_fields', [ $this, 'setup_phone_fields_settings' ] );
	}

	/**
	 * Uses functions (is_checkout, wp_enqueue_script, wp_localize_script, admin_url and wp_create_nonce) from WordPress
	 */
	public function payment_scripts() {
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! is_checkout() || ! $this->is_available() ) {
			return;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_enqueue_script(
			'power-board-api',
			SettingsService::get_instance()->get_widget_script_url(),
			[],
			POWER_BOARD_PLUGIN_VERSION,
			true
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_localize_script(
			'power-board-form',
			'PowerBoardAjax',
			[
				'url'           => admin_url( 'admin-ajax.php' ),
				'wpnonce'       => wp_create_nonce( 'power-board-create-charge-intent' ),
				'wpnonce_error' => wp_create_nonce( 'power-board-create-error-notice' ),
			]
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_localize_script(
			'power-board-classic-form',
			'PowerBoardAjax',
			[
				'url'           => admin_url( 'admin-ajax.php' ),
				'wpnonce'       => wp_create_nonce( 'power-board-create-charge-intent' ),
				'wpnonce_error' => wp_create_nonce( 'power-board-create-error-notice' ),
			]
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_enqueue_script(
			'power-board-form',
			POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/form.js',
			[ 'jquery' ],
			POWER_BOARD_PLUGIN_VERSION,
			true
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_enqueue_script(
			'power-board-classic-form',
			POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/classic-form.js',
			[ 'jquery' ],
			POWER_BOARD_PLUGIN_VERSION,
			true
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_enqueue_style(
			'power-board-widget',
			POWER_BOARD_PLUGIN_URL . '/assets/css/frontend/widget.css',
			[],
			POWER_BOARD_PLUGIN_VERSION,
			'all'
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_enqueue_script(
			'axios',
			'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
			[],
			POWER_BOARD_PLUGIN_VERSION,
			true
		);
	}

	public function is_available(): bool {
		return true;
	}

	public function get_title(): string {
		return trim( $this->title ) ? $this->title : $this->method_title;
	}

	/**
	 * Process the payment and return the result.
	 * Uses a function (sanitize_text_field) from WordPress
	 * Uses functions (wc_get_order, WC and get_return_url) from WooCommerce
	 * Uses method (get_return_url) from WC_Payment_Gateway
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ): array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_get_order( $order_id );
		$order->set_status( 'processing' );
		$order->payment_complete();

		/* @noinspection PhpUndefinedFunctionInspection */
		$charge_id = sanitize_text_field( $_POST['chargeid'] ?? '' );
		$order->update_meta_data( 'power_board_charge_id', $charge_id );
		/* @noinspection PhpUndefinedFunctionInspection */
		WC()->cart->empty_cart();
		$order->save();

		/* @noinspection PhpUndefinedMethodInspection */
		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	/**
	 * Uses functions (WC and get_woocommerce_currency) from WooCommerce
	 */
	public function get_settings(): array {
		$settings_service = SettingsService::get_instance();

		/* @noinspection PhpUndefinedFunctionInspection */
		return [
			// Wordpress data.
			'environment'             => $settings_service->get_environment(),
			// Woocommerce data.
			'amount'                  => WC()->cart->get_total(),
			'currency'                => strtoupper( get_woocommerce_currency() ),
			// Widget.
			'title'                   => 'PowerBoard',
			// Keys.
			'widgetToken'             => $settings_service->get_widget_access_token(),
			// Master Widget Checkout.
			'checkoutTemplateVersion' => $settings_service->get_checkout_template_version(),
			'checkoutCustomisationId' => $settings_service->get_checkout_customisation_id(),
			'checkoutConfigurationId' => $settings_service->get_checkout_configuration_id(),
		];
	}

	/**
	 * Ajax function
	 * Uses functions (sanitize_text_field, wp_verify_nonce, __ and wp_json_error) from WordPress
	 * Uses functions (wc_add_notice and wc_print_notices) from WooCommerce
	 */
	public function power_board_create_error_notice(): ?array {
		/* @noinspection PhpUndefinedFunctionInspection */
		$wp_nonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! wp_verify_nonce( $wp_nonce, 'power-board-create-error-notice' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Error: Security check', 'power-board' ) ] );

			return null;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$error_message = sanitize_text_field( $_POST['error'] ?? '' );
		if ( $error_message ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wc_add_notice( __( $error_message, 'power-board' ), 'error' );
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		$response['data'] = wc_print_notices();

		return $response;
	}

	public function setup_phone_fields_settings( $address_fields ): array {
		$address_fields['billing']['billing_phone']['required'] = false;
		$address_fields['shipping']['shipping_phone']           = [
			'label'        => 'Phone',
			'type'         => 'tel',
			'required'     => false,
			'class'        => [ 'form-row-wide' ],
			'validate'     => [ 'phone' ],
			'autocomplete' => 'tel',
			'priority'     => 95,
		];
		return $address_fields;
	}

	/**
	 * Uses functions (wp_create_nonce and wp_json_encode) from WordPress
	 */
	public function payment_fields() {
		$template = new TemplateService( $this );
		SDKAdapterService::get_instance();

		$settings = $this->get_settings();
		/* @noinspection PhpUndefinedFunctionInspection */
		$nonce = wp_create_nonce( 'power-board-create-charge-intent' );

		/* @noinspection PhpUndefinedFunctionInspection */
		$template->include_checkout_html(
			'method-form',
			[
				'description' => $this->description,
				'id'          => $this->id,
				'nonce'       => $nonce,
				'settings'    => wp_json_encode( $settings ),
			]
		);
	}
}
