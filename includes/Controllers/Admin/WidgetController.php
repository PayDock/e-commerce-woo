<?php
declare( strict_types=1 );

namespace WooPlugin\Controllers\Admin;

use WooPlugin\Helpers\OrderHelper;
use WooPlugin\Services\Settings\APIAdapterService;
use WooPlugin\Services\SettingsService;

class WidgetController {

	/**
	 * Uses functions (sanitize_text_field, wp_verify_nonce, wp_send_json_error, __ and wp_send_json_success) from WordPress
	 * Uses functions (WC, get_woocommerce_currency and wc_get_orders) from WooCommerce
	 */
	public function create_checkout_intent(): void {
		/* @noinspection PhpUndefinedFunctionInspection */
		$wp_nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : null;

		/* @noinspection PhpUndefinedFunctionInspection */
		if ( ! wp_verify_nonce( $wp_nonce, PLUGIN_TEXT_DOMAIN . '-create-charge-intent' ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Error: Security check', PLUGIN_TEXT_DOMAIN ) ] );

			return;
		}

		$request  = [];
		$settings = SettingsService::get_instance();

		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;
		/* @noinspection PhpUndefinedFunctionInspection */
		$session = WC()->session;

		if ( is_object( $session ) && isset( $_POST['selected_shipping_id'] ) && isset( $_COOKIE[ PLUGIN_PREFIX . '_selected_shipping' ] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$selected_shipping_id = sanitize_text_field( wp_unslash( $_POST['selected_shipping_id'] ) );
			/* @noinspection PhpUndefinedFunctionInspection */
			$cookies_shipping_id = urldecode( sanitize_text_field( wp_unslash( $_COOKIE[ PLUGIN_PREFIX . '_selected_shipping' ] ) ) );
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

		/* @noinspection PhpUndefinedFunctionInspection */
		$shipping_address = isset( $_POST['shipping_address'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['shipping_address'] ) ) : $session->get( 'customer' )['shipping'];
		$billing_address  = [];

		if ( ! empty( $_POST['address'] ) && is_array( $_POST['address'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$billing_address = array_map( 'sanitize_text_field', wp_unslash( $_POST['address'] ) );
		}

		if ( empty( $billing_address['country'] ) || empty( $shipping_address['country'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$countries = WC()->countries;
			if ( ! empty( $countries ) ) {
				$allowed_countries = $countries->get_allowed_countries();

				if ( count( $allowed_countries ) === 1 ) {
					$allowed_country = key( $allowed_countries );

					if ( empty( $billing_address['country'] ) ) {
						$billing_address['country'] = $allowed_country;
					}

					if ( empty( $shipping_address['country'] ) ) {
						$shipping_address['country'] = $allowed_country;
					}
				}
			}
		}

		if ( ! empty( $_POST['order_id'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			$reference = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );
		} else {
			/* @noinspection PhpUndefinedFunctionInspection */
			$custom_order_id = (string) WC()->session->get( PLUGIN_PREFIX . '_draft_order' );

			if ( ! empty( $custom_order_id ) ) {
				$order_id = $custom_order_id;
				/* @noinspection PhpUndefinedFunctionInspection */
				$order = wc_get_order( $order_id );
                $order_status = $order->get_status();

                if ( $order_status !== 'checkout-draft' && $order_status !== 'failed' ) {
                    $order_id = $this->create_draft_order( $billing_address, $shipping_address );
                } else {
                    OrderHelper::update_order( $order, $billing_address, $shipping_address );
                }

            } else {
				$order_id = $this->create_draft_order( $billing_address, $shipping_address );
			}
			/* @noinspection PhpUndefinedFunctionInspection */
			WC()->session->set( PLUGIN_PREFIX . '_draft_order', $order_id );

			$reference = $order_id;
		}

		if ( ! $this->check_is_complete_address( $billing_address ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Incomplete billing address', PLUGIN_TEXT_DOMAIN ) ] );
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
		$api_adapter_service->initialise( $settings->get_environment(), $settings->get_access_token() );
		$result = $api_adapter_service->create_checkout_intent( $intent_request_params );

		if ( ! empty( $result['error'] ) ) {
			/* @noinspection PhpUndefinedFunctionInspection */
			wp_send_json_error( [ 'message' => __( 'Something went wrong, please refresh the page and try again.', PLUGIN_TEXT_DOMAIN ) ] );
		}

		$selected_shipping_id = $session->get( 'chosen_shipping_methods' )[0];
		/* @noinspection PhpUndefinedFunctionInspection */
		$selected_shipping = $session->get( 'shipping_for_package_0' )['rates'][ $selected_shipping_id ];
		/* @noinspection PhpUndefinedFunctionInspection */
		$identifier = '_' . wp_create_nonce( PLUGIN_TEXT_DOMAIN . '-checkout-cart' );

		$session->set(
			PLUGIN_PREFIX . '_checkout_cart' . $identifier,
			[
				'items'                => $cart->get_cart(),
				'total'                => $cart->get_total( false ),
				'shipping_total'       => $cart->get_shipping_total(),
				'selected_shipping_id' => $selected_shipping_id,
				'selected_shipping'    => $selected_shipping,
				'shipping_address'     => $shipping_address,
				'billing_address'      => $billing_address,
			]
		);

		$current_active_intent_ids = $session->get( PLUGIN_PREFIX . '_active_checkout_intent_ids' ) ?? [];
		$current_intent            = $result['resource']['data']['_id'];
		if ( !in_array( $current_intent, $current_active_intent_ids, true ) ) {
			$current_active_intent_ids[] = $current_intent;
			$session->set( PLUGIN_PREFIX . '_active_checkout_intent_ids', $current_active_intent_ids );
		}

		/* @noinspection PhpUndefinedFunctionInspection */
		wp_send_json_success(
			[
				'token'    => $result['resource']['data']['token'],
				'intentId' => $current_intent,
			],
			200
			);
	}

	protected function check_is_complete_address( $address ): bool {
		return ! empty( $address )
			&& ! empty( $address['email'] )
			&& ! empty( $address['first_name'] )
			&& ! empty( $address['last_name'] )
			&& ! empty( $address['address_1'] )
			&& ! empty( $address['city'] )
			&& ! empty( $address['state'] )
			&& ! empty( $address['country'] )
			&& ! empty( $address['postcode'] );
	}

	public static function check_intent_status( $intent_id, $charge_id, $order_id, $total_amount ): bool {
		$settings = SettingsService::get_instance();

		$intent_request_params = [ 'intent_id' => $intent_id ];
		$api_adapter_service   = APIAdapterService::get_instance();
		$api_adapter_service->initialise( $settings->get_environment(), $settings->get_access_token() );
		$result = $api_adapter_service->get_checkout_intent_by_id( $intent_request_params );

		return (float) $result['resource']['data']['amount'] === (float) $total_amount
			&& (string) $result['resource']['data']['reference'] === (string) $order_id
			&& $result['resource']['data']['status'] === 'completed'
			&& $result['resource']['data']['process_reference'] === $charge_id;
	}

	private function create_draft_order( $billing_address = null, $shipping_address = null ): string {
		/* @noinspection PhpUndefinedFunctionInspection */
		$cart = WC()->cart;
		/* @noinspection PhpUndefinedFunctionInspection */
		$order = wc_create_order(
			[
				'cart_hash' => $cart->get_cart_hash(),
			]
		);
        $order->set_status( 'checkout-draft' );

        OrderHelper::update_order( $order, $billing_address, $shipping_address );

		$order_id = $order->get_id();
		return (string) $order_id;
	}
}
