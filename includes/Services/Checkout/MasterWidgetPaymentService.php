<?php

namespace PowerBoard\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\TemplateService;
use WC_Payment_Gateway;

class MasterWidgetPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id         = 'power_board_gateway';
		$this->has_fields = true;
		$this->supports   = array(
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'multiple_subscriptions',
			'default_credit_card_form',
		);

		$this->method_title = _x( 'PowerBoard payment', 'PowerBoard payment method', 'power-board' );

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		SettingsService::get_instance();
		$this->title       = $this->method_title;
		$this->description = $this->method_description;

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action(
			'woocommerce_scheduled_subscription_payment_power_board',
			array( $this, 'process_subscription_payment' ),
			10,
			2
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

		add_action( 'wp_ajax_nopriv_power_board_create_error_notice', array( $this, 'power_board_create_error_notice' ), 20 );
		add_action( 'wp_ajax_power_board_create_error_notice', array( $this, 'power_board_create_error_notice' ), 20 );

		add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'woocommerce_before_checkout_form' ), 10, 1 );
		add_action( 'woocommerce_checkout_fields', array( $this, 'setup_phone_fields_settings' ), 10, 1 );
	}

	public function payment_scripts() {
		if ( ! is_checkout() || ! $this->is_available() ) {
			return '';
		}
		wp_enqueue_script( 'power-board-api', SettingsService::get_instance()->get_widget_script_url(), array(), time(), true );
		wp_enqueue_script( 'power-board-form', POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/form.js', array(), time(), true );
		wp_enqueue_script( 'power-board-classic-form', POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/classic-form.js', array(), time(), true );
		wp_enqueue_style( 'power-board-widget-css', POWER_BOARD_PLUGIN_URL . '/assets/css/frontend/widget.css', array(), time() );

		wp_localize_script(
			'power-board-form',
			'PowerBoardAjax',
			array(
				'url'     => admin_url( 'admin-ajax.php' ),
				'wpnonce' => wp_create_nonce( 'power-board-create-charge-intent' ),
			)
		);
		wp_localize_script(
			'power-board-classic-form',
			'PowerBoardAjax',
			array(
				'url'     => admin_url( 'admin-ajax.php' ),
				'wpnonce' => wp_create_nonce( 'power-board-create-charge-intent' ),
			)
		);
		wp_enqueue_script( 'axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', array(), time(), true );

		return '';
	}

	public function is_available() {
		return true;
	}

	public function get_title() {
		return trim( $this->title ) ? $this->title : $this->method_title;
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		$order->set_status( 'processing' );
		$order->payment_complete();
		$order->update_meta_data( 'power_board_charge_id', $_POST['chargeid'] );
		$order->update_meta_data( 'pb_directly_charged', 1 );
		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->get_key(), 'PowerBoard' );
		WC()->cart->empty_cart();
		$order->save();

		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	public function get_settings() {
		$settings_service = SettingsService::get_instance();

		return array(
			// Wordpress data.
			'environment'             => $settings_service->get_environment(),
			// Woocommerce data.
			'amount'                  => WC()->cart->total,
			'currency'                => strtoupper( get_woocommerce_currency() ),
			// Widget.
			'title'                   => 'PowerBoard',
			// Keys.
			'widgetToken'             => $settings_service->get_widget_access_token(),
			// Master Widget Checkout.
			'checkoutTemplateVersion' => $settings_service->get_checkout_template_version(),
			'checkoutCustomisationId' => $settings_service->get_checkout_customisation_id(),
			'checkoutConfigurationId' => $settings_service->get_checkout_configuration_id(),
		);
	}

	/**
	 * Ajax function
	 */
	public function power_board_create_error_notice() {
		wc_add_notice( __( $_POST['error'], 'power-board' ), 'error' );
		$response['data'] = wc_print_notices();
		return $response;
	}

	public function woocommerce_before_checkout_form( $arg ) {
	}

	function setup_phone_fields_settings( $address_fields ) {
		$address_fields['billing']['billing_phone']['required'] = false;
		$address_fields['shipping']['shipping_phone']           = array(
			'label'        => 'Phone',
			'type'         => 'tel',
			'required'     => false,
			'class'        => array( 'form-row-wide' ),
			'validate'     => array( 'phone' ),
			'autocomplete' => 'tel',
			'priority'     => 95,
		);
		return $address_fields;
	}

	public function payment_fields() {
		$template = new TemplateService( $this );
		SDKAdapterService::get_instance();

		$settings = $this->get_settings();

		$template->include_checkout_html(
			'method-form',
			array(
				'description' => $this->description,
				'id'          => $this->id,
				'settings'    => wp_json_encode( $settings ),
			)
		);
	}
}
