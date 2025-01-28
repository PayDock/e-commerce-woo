<?php

namespace PowerBoard\Controllers\Admin;

use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\SettingsService;

class WidgetController {

	/**
	 * Uses functions (sanitize_text_field, wp_verify_nonce, wp_send_json_error, __ and wp_send_json_success) from WordPress
	 * Uses functions (WC, get_woocommerce_currency and wc_get_orders) from WooCommerce
	 */
	public function create_checkout_intent(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : null;

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! wp_verify_nonce( $wp_nonce, 'power-board-create-charge-intent' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Error: Security check', 'power-board' ) ] );

			return;
		}

		$request  = [];
		$settings = SettingsService::get_instance();

		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;

		$args = [
			'limit'     => 1,
			'cart_hash' => $cart->get_cart_hash(),
		];

		if ( ! empty( $_POST['total'] ) ) {
			if ( is_array( $_POST['total'] ) ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				$request['total'] = array_map( 'sanitize_text_field', wp_unslash( $_POST['total'] ) );
			} else {
				/* @noinspection PhpUndefinedFunctionInspection */
				$request['total'] = sanitize_text_field( wp_unslash( $_POST['total'] ) );
			}
		} else {
			$request['total']['total_price'] = $cart->get_total( false ) * 100;
			/* @noinspection PhpUndefinedFunctionInspection */
			$request['total']['currency_code'] = get_woocommerce_currency();
		}

		if ( ! empty( $_POST['order_id'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$reference = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );
		} else {
			/* @noinspection PhpUndefinedFunctionInspection */
			$orders = wc_get_orders( $args );

			if ( ! empty( $orders ) && is_a( $orders[0], 'WC_Order' ) ) {
				$reference = $orders[0]->get_id();
			} else {
				$reference = '';
			}
		}

		$billing_address = [];

		if ( ! empty( $_POST['address'] ) && is_array( $_POST['address'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$billing_address = array_map( 'sanitize_text_field', wp_unslash( $_POST['address'] ) );
		}

		$intent_request_params = [
			'amount'        => round( $request['total']['total_price'] / 100, 2 ),
			'version'       => (int) $settings->get_checkout_template_version(),
			'currency'      => $request['total']['currency_code'],
			'reference'     => $reference,
			'customer'      => [
				'email'           => $billing_address['email'],
				'billing_address' => [
					'first_name'       => $billing_address['first_name'],
					'last_name'        => $billing_address['last_name'],
					'address_line1'    => $billing_address['address_1'],
					'address_city'     => $billing_address['city'],
					'address_state'    => $billing_address['state'],
					'address_country'  => $billing_address['country'],
					'address_postcode' => $billing_address['postcode'],
				],
			],
			'configuration' => [
				'template_id' => $settings->get_checkout_configuration_id(),
			],
		];

		if ( empty( $reference ) ) {
			unset( $intent_request_params['reference'] );
		}

		if ( ! empty( $settings->get_checkout_customisation_id() ) ) {
			$intent_request_params['customisation']['template_id'] = $settings->get_checkout_customisation_id();
		}

		if ( ! empty( $billing_address['phone'] ) ) {
			$intent_request_params['customer']['phone'] = $billing_address['phone'];
		}

		if ( ! empty( $billing_address['address_2'] ) ) {
			$intent_request_params['customer']['billing_address']['address_line2'] = $billing_address['address_2'];
		}

		$api_adapter_service = APIAdapterService::get_instance();
		$api_adapter_service->initialise( $settings->get_environment(), $settings->get_access_token(), $settings->get_widget_access_token() );
		$result = $api_adapter_service->create_checkout_intent( $intent_request_params );

		$result['county'] = $request['address']['country'] ?? '';

		if ( ! empty( $_POST['return_cart'] ) ) {
			$result['cart'] = $cart;
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_send_json_success( $result, 200 );
	}
}
