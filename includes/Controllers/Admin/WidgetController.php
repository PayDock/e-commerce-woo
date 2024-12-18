<?php

namespace PowerBoard\Controllers\Admin;

use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\Settings\APIAdapterService;
use PowerBoard\Services\SettingsService;

class WidgetController {
	public function create_checkout_intent() {
		$wp_nonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wp_nonce, 'power-board-create-charge-intent' ) ) {
			wp_send_json_error( array( 'message' => __( 'Error: Security check', 'power-board' ) ) );

			return;
		}

		$request           = array();
		$settings          = SettingsService::get_instance();
		$logger_repository = new LogRepository();

		$cart = WC()->cart;

		$args = array(
			'limit'     => 1,
			'cart_hash' => $cart->get_cart_hash(),
		);

		if ( ! empty( $_POST['total'] ) ) {
			$request['total'] = $_POST['total'];
		} else {
			$request['total']['total_price']   = $cart->get_total( false ) * 100;
			$request['total']['currency_code'] = get_woocommerce_currency();
		}

		if ( ! empty( $_POST['order_id'] ) ) {
			$reference = $_POST['order_id'];
		} else {
			$orders    = wc_get_orders( $args );
			$reference = $orders[0]->ID;
		}

		$billing_address = ! empty( $_POST['address'] ) ? $_POST['address'] : array();

		$intent_request_params = array(
			'amount'        => round( $request['total']['total_price'] / 100, 2 ),
			'version'       => (int) $settings->get_checkout_template_version(),
			'currency'      => $request['total']['currency_code'],
			'reference'     => (string) $reference,
			'customer'      => array(
				'email'           => $billing_address['email'],
				'billing_address' => array(
					'first_name'       => $billing_address['first_name'],
					'last_name'        => $billing_address['last_name'],
					'address_line1'    => $billing_address['address_1'],
					'address_city'     => $billing_address['city'],
					'address_state'    => $billing_address['state'],
					'address_country'  => $billing_address['country'],
					'address_postcode' => $billing_address['postcode'],
				),
			),
			'configuration' => array(
				'template_id' => $settings->get_checkout_configuration_id(),
			),
		);

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

		if ( ! empty( $result['error'] ) ) {
			$operation = ucfirst( strtolower( $result['resource']['type'] ?? 'undefined' ) );
			$status    = $result['error']['message'] ?? 'empty status';
			$message   = $result['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

			$logger_repository->createLogRecord( '', $operation, $status, $message, LogRepository::ERROR );
		}

		wp_send_json_success( $result, 200 );
	}
}
