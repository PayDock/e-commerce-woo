<?php

namespace PowerBoard\Controllers\Admin;

use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Helpers\ShippingHelper;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use WP_REST_Request;

class WidgetController {
	public function createWalletCharge( WP_REST_Request $request ) {

		$request = $request->get_json_params();

		$required_fields = ['order_id', 'type', 'total', 'address', 'items'];
		foreach ( $required_fields as $field ) {
			if ( empty( $request[ $field ] ) ) {
				return new WP_Error( 'missing_data', __( 'Required data is missing: ', 'power_board' ) . $field, array( 'status' => 400 ) );
			}
		}

		$valid_types = ['afterpay', 'apple-pay', 'google-pay', 'pay-pal'];
		if ( ! in_array( $request['type'], $valid_types ) ) {
			return new WP_Error( 'invalid_type', __( 'Invalid payment type', 'power_board' ), array( 'status' => 400 ) );
		}

		if ( ! is_numeric( $request['total']['total_price'] ) || strlen( $request['total']['currency_code'] ) !== 3 ) {
			return new WP_Error( 'invalid_total', __( 'Invalid total or currency data', 'power_board' ), array( 'status' => 400 ) );
		}

		$address_fields = ['first_name', 'last_name', 'email', 'address_1', 'city', 'state', 'country', 'postcode'];
		foreach ( $address_fields as $field ) {
			if ( empty( $request['address'][ $field ] ) ) {
				return new WP_Error( 'invalid_address', __( 'Invalid address field: ', 'power_board' ) . $field, array( 'status' => 400 ) );
			}
		}

		if ( ! is_array( $request['items'] ) || empty( $request['items'] ) ) {
			return new WP_Error( 'invalid_items', __( 'Invalid items data', 'power_board' ), array( 'status' => 400 ) );
		}

		if ( isset( $request['address'] ) ) {
			$request['address'] = array_map( 'sanitize_text_field', $request['address'] );
		}

		if ( isset( $request['shipping_address'] ) ) {
			$request['shipping_address'] = array_map( 'sanitize_text_field', $request['shipping_address'] );
		}

		foreach ( $request['items'] as &$item ) {
			$item['name'] = sanitize_text_field( $item['name'] );
			$item['type'] = sanitize_text_field( $item['type'] );
			$item['permalink'] = esc_url_raw( $item['permalink'] );
		}
		unset( $item );

		$settings = SettingsService::getInstance();
		$order    = wc_get_order( $request['order_id'] );

		$loggerRepository = new LogRepository();

		$result     = [];
		$isAfterPay = false;

		switch ( $request['type'] ) {
			case 'afterpay':
				$isAfterPay = true;
				$payment    = WalletPaymentMethods::AFTERPAY();
				break;
			case 'apple-pay':
				$payment = WalletPaymentMethods::APPLE_PAY();
				break;
			case 'google-pay':
				$payment = WalletPaymentMethods::GOOGLE_PAY();
				break;
			case 'pay-pal':
				$payment = WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
				break;
		}

		$key = strtolower( $payment->name );
		if ( $settings->isWalletEnabled( $payment ) ) {
			$reference = sanitize_text_field( $request['order_id'] );

			$items = [];
			foreach ( $request['items'] as $item ) {
				$image = wp_get_attachment_image_url( get_post_thumbnail_id( $item['id'] ), 'full' );

				$itemData = [
					'amount'   => round( floatval( $item['prices']['price'] ) / 100, 2 ),
					'name'     => $item['name'],
					'type'     => $item['type'],
					'quantity' => intval( $item['quantity'] ),
					'item_uri' => esc_url_raw( $item['permalink'] ),
				];

				if ( ! empty( $image ) ) {
					$itemData['image_uri'] = esc_url_raw( $image );
				}

				$items[] = $itemData;
			}

			$billingAddress = $request['address'];
			$shippingAddress = $request['shipping_address'];

			foreach ( $shippingAddress as $key => $value ) {
				if ( empty( trim( $value ) ) ) {
					$shippingAddress[ $key ] = $billingAddress[ $key ];
				}
			}

			$chargeRequest = [
				'amount'    => round( floatval( $request['total']['total_price'] ) / 100, 2 ),
				'currency'  => sanitize_text_field( $request['total']['currency_code'] ),
				'reference' => (string) $reference,
				'customer'  => [
					'first_name'     => sanitize_text_field( $billingAddress['first_name'] ),
					'last_name'      => sanitize_text_field( $billingAddress['last_name'] ),
					'email'          => sanitize_email( $billingAddress['email'] ),
					'payment_source' => [
						'gateway_id'       => sanitize_text_field( $settings->getWalletGatewayId( $payment ) ),
						'address_line1'    => sanitize_text_field( $billingAddress['address_1'] ),
						'address_city'     => sanitize_text_field( $billingAddress['city'] ),
						'address_state'    => sanitize_text_field( $billingAddress['state'] ),
						'address_country'  => sanitize_text_field( $billingAddress['country'] ),
						'address_postcode' => sanitize_text_field( $billingAddress['postcode'] ),
					],
				],
				'meta'      => [
					'store_name' => sanitize_text_field( get_bloginfo( 'name' ) ),
				],
				'items'     => $items,
				'shipping'  => [
					'amount'           => round( floatval( $request['total']['total_shipping'] ) / 100, 2 ),
					'currency'         => sanitize_text_field( $request['total']['currency_code'] ),
					'address_line1'    => sanitize_text_field( $shippingAddress['address_1'] ),
					'address_city'     => sanitize_text_field( $shippingAddress['city'] ),
					'address_state'    => sanitize_text_field( $shippingAddress['state'] ),
					'address_country'  => sanitize_text_field( $shippingAddress['country'] ),
					'address_postcode' => sanitize_text_field( $shippingAddress['postcode'] ),
					'contact'          => [
						'first_name' => sanitize_text_field( $shippingAddress['first_name'] ),
						'last_name'  => sanitize_text_field( $shippingAddress['last_name'] ),
					],
				],
			];

			if ( ! empty( $billingAddress['phone'] ) ) {
				$chargeRequest['customer']['phone'] = sanitize_text_field( $billingAddress['phone'] );
			}

			if ( ! empty( $billingAddress['address_2'] ) ) {
				$chargeRequest['customer']['payment_source']['address_line2'] = sanitize_text_field( $billingAddress['address_2'] );
			}

			if ( ! empty( $shippingAddress['phone'] ) ) {
				$chargeRequest['shipping']['contact']['phone'] = sanitize_text_field( $shippingAddress['phone'] );
			}

			if ( ! empty( $shippingAddress['address_2'] ) ) {
				$chargeRequest['shipping']['address_line2'] = sanitize_text_field( $shippingAddress['address_2'] );
			}

			if ( ! empty( $request['shipping_rates'] ) ) {
				$shippingRates = reset( $request['shipping_rates'] );
				foreach ( $shippingRates['shipping_rates'] as $shippingRate ) {
					if ( $shippingRate['selected'] ) {
						if ( 'pickup_location' === sanitize_text_field( $shippingRate['method_id'] ) ) {
							$location = ShippingHelper::getPickupLocationByKey( sanitize_text_field( $shippingRate['rate_id'] ) );
							if ( false !== $location ) {
								$chargeRequest['shipping']['address_line1']    = sanitize_text_field( $location['address']['address_1'] );
								$chargeRequest['shipping']['address_city']     = sanitize_text_field( $location['address']['city'] );
								$chargeRequest['shipping']['address_state']    = sanitize_text_field( $location['address']['state'] );
								$chargeRequest['shipping']['address_country']  = sanitize_text_field( $location['address']['country'] );
								$chargeRequest['shipping']['address_postcode'] = sanitize_text_field( $location['address']['postcode'] );
								unset( $chargeRequest['shipping']['address_line2'] );
							}
						}
						break;
					}
				}
			}

			if ( WalletPaymentMethods::APPLE_PAY()->name === $payment->name ) {
				$chargeRequest['customer']['payment_source']['wallet_type'] = 'apple';
			}

			$fraudService = sanitize_text_field( $settings->getWalletFraudServiceId( $payment ) );
			if (
				$settings->isWalletFraud( $payment )
				&& ! empty( $fraudService )
			) {
				$chargeRequest['fraud'] = [
					'service_id' => $fraudService,
					'data'       => [],
				];
			}

			if ( $isAfterPay ) {
				$chargeRequest['meta']['success_url'] = esc_url_raw( $order->get_checkout_order_received_url() );
				$chargeRequest['meta']['error_url']   = esc_url_raw( add_query_arg( 'afterpay-error', 'true',
					$order->get_checkout_order_received_url() ) );
			}

			$result = SDKAdapterService::getInstance()
			                           ->createWalletCharge( $chargeRequest,
				                           $settings->isWalletDirectCharge( $payment ) );

			$result['country'] = sanitize_text_field( $request['address']['country'] ?? '' );

			if ( WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $payment->name ) {
				$result['pay_later'] = 'yes' === $settings->isPayPallSmartButtonPayLater();
			}

			if ( ! empty( $result[ $key ]['error'] ) ) {
				$operation = ucfirst( strtolower( sanitize_text_field( $result[ $key ]['resource']['type'] ?? 'undefined' ) ) );
				$status    = sanitize_text_field( $result[ $key ]['error']['message'] ?? 'empty status' );
				$message   = sanitize_text_field( $result[ $key ]['error']['details'][0]['gateway_specific_description'] ?? 'empty message' );

				$loggerRepository->createLogRecord( '', $operation, $status, $message, LogRepository::ERROR );
			}
		}

		return rest_ensure_response( $result );
	}
}
