<?php
declare( strict_types=1 );

namespace PowerBoard\Controllers\Admin;

use PowerBoard\Helpers\OrderHelper;
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
		/* @noinspection PhpUndefinedFunctionInspection */
		$session = WC()->session;

		if ( is_object( $session ) && isset( $_POST['selected_shipping_id'] ) && isset( $_COOKIE['power_board_selected_shipping'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$selected_shipping_id = sanitize_text_field( wp_unslash( $_POST['selected_shipping_id'] ) );
			/* @noinspection PhpUndefinedFunctionInspection */
			$cookies_shipping_id = urldecode( sanitize_text_field( wp_unslash( $_COOKIE['power_board_selected_shipping'] ) ) );
			/* @noinspection PhpUndefinedFunctionInspection */
			$current_shipping_id = $session->get( 'chosen_shipping_methods' )[0];
			if ( $selected_shipping_id === $cookies_shipping_id && $selected_shipping_id !== $current_shipping_id ) {
				if ( isset( $_POST['total'] ) ) {
					if ( is_array( $_POST['total'] ) ) {
						/* @noinspection PhpUndefinedFunctionInspection */
						$cart_total = array_map( 'sanitize_text_field', wp_unslash( $_POST['total'] ) );
					} else {
						/* @noinspection PhpUndefinedFunctionInspection */
						$cart_total = sanitize_text_field( wp_unslash( $_POST['total'] ) );
					}
					$request['total'] = $cart_total;
				} else {
					return;
				}
			}
		}

		if ( is_object( $cart ) ) {
			if ( empty( $cart_total ) ) {
				$cart->calculate_totals();
				$cart_total = $cart->get_total( false );

				if ( ! empty( $cart_total ) ) {
					/* @noinspection PhpUndefinedFunctionInspection */
					$request['total']['total_price'] = $cart_total * 100;
					/* @noinspection PhpUndefinedFunctionInspection */
					$request['total']['currency_code'] = get_woocommerce_currency();
				}
			}
		} elseif ( isset( $_POST['total'] ) ) {
			if ( is_array( $_POST['total'] ) ) {
				/* @noinspection PhpUndefinedFunctionInspection */
				$request['total'] = array_map( 'sanitize_text_field', wp_unslash( $_POST['total'] ) );
			} else {
				/* @noinspection PhpUndefinedFunctionInspection */
				$request['total'] = sanitize_text_field( wp_unslash( $_POST['total'] ) );
			}
		} else {
			return;
		}

		if ( ! empty( $_POST['order_id'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$reference = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );
		} else {
			/* @noinspection PhpUndefinedFunctionInspection */
			$custom_order_id = (string) WC()->session->get( 'power_board_draft_order' );

			if ( ! empty( $custom_order_id ) ) {
				$order_id = $custom_order_id;
				/* @noinspection PhpUndefinedFunctionInspection */
				$order = wc_get_order( $order_id );
				OrderHelper::update_order( $order );
			} else {
				$order_id = $this->create_draft_order();
			}
			/* @noinspection PhpUndefinedFunctionInspection */
			WC()->session->set( 'power_board_draft_order', $order_id );

			$reference = $order_id;
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
			'reference'     => (string) $reference,
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
		$api_adapter_service->initialise( $settings->get_environment(), $settings->get_access_token() );
		$result = $api_adapter_service->create_checkout_intent( $intent_request_params );

		$result['county'] = $request['address']['country'] ?? '';

		/* @noinspection PhpUndefinedFunctionInspection */
		$session              = WC()->session;
		$selected_shipping_id = $session->get( 'chosen_shipping_methods' )[0];
		$shipping_address     = $session->get( 'customer' );
		$selected_shipping    = $session->get( 'shipping_for_package_0' )['rates'][ $selected_shipping_id ];
		/* @noinspection PhpUndefinedFunctionInspection */
		$identifier = is_user_logged_in() ? ( '_' . wp_create_nonce( 'power-board-checkout-cart' ) ) : '';

		$session->set(
			'power_board_checkout_cart' . $identifier,
			[
				'items'                => $cart->get_cart(),
				'total'                => $cart->get_total( false ),
				'shipping_total'       => $cart->get_shipping_total(),
				'selected_shipping_id' => $selected_shipping_id,
				'selected_shipping'    => $selected_shipping,
				'shipping_address'     => $shipping_address,
			]
		);

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_send_json_success( $result, 200 );
	}

	private function create_draft_order(): string {
		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_create_order(
			[
				'status'    => 'checkout-draft',
				'cart_hash' => $cart->get_cart_hash(),
			]
		);
		OrderHelper::update_order( $order );

		$order_id = $order->get_id();
		return (string) $order_id;
	}
}
