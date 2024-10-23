<?php

namespace WooPlugin\Controllers\Admin;

use WooPlugin\Enums\WalletPaymentMethods;
use WooPlugin\Helpers\ShippingHelper;
use WooPlugin\Repositories\LogRepository;
use WooPlugin\Services\SDKAdapterService;
use WooPlugin\Services\SettingsService;
use WP_REST_Request;

class WidgetController {
	public function createWalletChargeClassic() {
		$data    = [];
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'create-wallet-charge' ) ) {
			wp_send_json_error( [ 'message' => __( 'Error: Security check', PLUGIN_TEXT_DOMAIN ) ] );

			return;
		}
		$type    = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : null;
		$address = ! empty( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '{}';
		$address = str_replace( '\\', '', $address );
		$address = json_decode( str_replace( '\\', '', $address ), true );

		$cart = WC()->cart;

		$args = array(
			'limit'     => 1,
			'cart_hash' => $cart->get_cart_hash(),
		);

		$orders = wc_get_orders( $args );

		foreach ( $cart->get_cart_contents() as $item ) {
			$product         = wc_get_product( $item['product_id'] );
			$data['items'][] = [
				'id'        => $product->get_id(),
				'prices'    => [ 'price' => $product->get_price( false ) * 100 ],
				'name'      => $product->get_name( false ),
				'type'      => $product->get_type(),
				'quantity'  => $item['quantity'],
				'permalink' => $product->get_permalink(),
			];
		}
		$data['total']['total_price']    = $cart->get_total( false ) * 100 ;
		$data['total']['total_shipping'] = $cart->get_shipping_total() * 100;
		$data['total']['currency_code']  = get_woocommerce_currency();
		$data['address']                 = $address['address'];
		$data['shipping_address']        = $address['shipping_address'];

		wp_send_json_success( $this->getToken( $type, $data, $orders[0] ), 200 );
	}

	public function createWalletCharge( WP_REST_Request $request ) {
		$request = $request->get_json_params();

		return $this->getToken( $request['type'], $request, wc_get_order( $request['order_id'] ) );
	}

	private function getToken( string $type, array $data, $order ): \WP_REST_Response {
		$settings = SettingsService::getInstance();

		$loggerRepository = new LogRepository();

		$result     = [];
		$isAfterPay = false;

		switch ( $type ) {
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
			$reference = $order->ID;

			$items = [];
			foreach ( $data['items'] as $item ) {
				$image = wp_get_attachment_image_url( get_post_thumbnail_id( $item['id'] ), 'full' );

				$itemData = [
					'amount'   => round( $item['prices']['price'] / 100, 2 ),
					'name'     => $item['name'],
					'type'     => $item['type'],
					'quantity' => $item['quantity'],
					'item_uri' => $item['permalink'],
				];

				if ( ! empty( $image ) ) {
					$itemData['image_uri'] = $image;
				}

				$items[] = $itemData;
			}
			$billingAdress   = $data['address'];
			$shippingAddress = $data['shipping_address'];

			foreach ( $shippingAddress as $key => $value ) {
				if ( empty( trim( $value ) ) ) {
					$shippingAddress[ $key ] = $billingAdress[ $key ];
				}
			}

			$chargeRequest = [
				'amount'    => round( $data['total']['total_price'] / 100, 2 ),
				'currency'  => $data['total']['currency_code'],
				'reference' => (string) $reference,
				'customer'  => [
					'first_name'     => $billingAdress['first_name'],
					'last_name'      => $billingAdress['last_name'],
					'email'          => $billingAdress['email'],
					'payment_source' => [
						'gateway_id'       => $settings->getWalletGatewayId( $payment ),
						'address_line1'    => $billingAdress['address_1'],
						'address_city'     => $billingAdress['city'],
						'address_state'    => $billingAdress['state'],
						'address_country'  => $billingAdress['country'],
						'address_postcode' => $billingAdress['postcode'],
					],
				],
				'meta'      => [
					'store_name' => get_bloginfo( 'name' ),
				],
				'items'     => $items,
				'shipping'  => [
					'amount'           => round( $data['total']['total_shipping'] / 100, 2 ),
					'currency'         => $data['total']['currency_code'],
					'address_line1'    => $shippingAddress['address_1'],
					'address_city'     => $shippingAddress['city'],
					'address_state'    => $shippingAddress['state'],
					'address_country'  => $shippingAddress['country'],
					'address_postcode' => $shippingAddress['postcode'],
					'contact'          => [
						'first_name' => $shippingAddress['first_name'],
						'last_name'  => $shippingAddress['last_name'],
					],
				],
			];

			if ( ! empty( $billingAdress['phone'] ) ) {
				$chargeRequest['customer']['phone'] = $billingAdress['phone'];
			}

			if ( ! empty( $billingAdress['address_2'] ) ) {
				$chargeRequest['customer']['payment_source']['address_line2'] = $billingAdress['address_2'];
			}

			if ( ! empty( $shippingAddress['phone'] ) ) {
				$chargeRequest['shipping']['contact']['phone'] = $shippingAddress['phone'];
			}

			if ( ! empty( $shippingAddress['address_2'] ) ) {
				$chargeRequest['shipping']['address_line2'] = $shippingAddress['address_2'];
			}

			if ( ! empty( $data['shipping_rates'] ) ) {
				$shippingRates = reset( $data['shipping_rates'] );
				foreach ( $shippingRates['shipping_rates'] as $shippingRate ) {
					if ( $shippingRate['selected'] ) {
						if ( 'pickup_location' === $shippingRate['method_id'] ) {
							$location = ShippingHelper::getPickupLocationByKey( $shippingRate['rate_id'] );
							if ( false !== $location ) {
								$chargeRequest['shipping']['address_line1']    = $location['address']['address_1'];
								$chargeRequest['shipping']['address_city']     = $location['address']['city'];
								$chargeRequest['shipping']['address_state']    = $location['address']['state'];
								$chargeRequest['shipping']['address_country']  = $location['address']['country'];
								$chargeRequest['shipping']['address_postcode'] = $location['address']['postcode'];
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

			$fraudService = $settings->getWalletFraudServiceId( $payment );
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
				$chargeRequest['meta']['success_url'] = $order->get_checkout_order_received_url();
				$chargeRequest['meta']['error_url']   = add_query_arg( 'afterpay-error', 'true',
					$order->get_checkout_order_received_url() );
			}

			$result = SDKAdapterService::getInstance()
			                           ->createWalletCharge( $chargeRequest,
				                           $settings->isWalletDirectCharge( $payment ) );

			$result['county'] = $data['address']['country'] ?? '';

			if ( WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $payment->name ) {
				$result['pay_later'] = 'yes' === $settings->isPayPallSmartButtonPayLater();
			}

			if ( ! empty( $result[ $key ]['error'] ) ) {
				$operation = ucfirst( strtolower( $result[ $key ]['resource']['type'] ?? 'undefined' ) );
				$status    = $result[ $key ]['error']['message'] ?? 'empty status';
				$message   = $result[ $key ]['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

				$loggerRepository->createLogRecord( '', $operation, $status, $message, LogRepository::ERROR );
			}
		}

		return rest_ensure_response( $result );
	}
}
