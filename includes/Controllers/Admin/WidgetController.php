<?php

namespace PowerBoard\Controllers\Admin;

use PowerBoard\Abstracts\AbstractWalletBlock;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Helpers\ShippingHelper;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use WP_REST_Request;

class WidgetController {
	public function createWalletCharge( WP_REST_Request $request ) {
		$settings = SettingsService::getInstance();

		$loggerRepository = new LogRepository();

		$request = $request->get_json_params();
		$result = [];
		$isAfterPay = false;

		switch ( $request['type'] ) {
			case 'afterpay':
				$isAfterPay = true;
				$payment = WalletPaymentMethods::AFTERPAY();
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
			$reference = $request['order_id'];

			$items = [];
			foreach ( $request['items'] as $item ) {
				$image = wp_get_attachment_image_url( get_post_thumbnail_id( $item['id'] ), 'full' );

				$itemData = [ 
					'amount' => round( $item['prices']['price'] / 100, 2 ),
					'name' => $item['name'],
					'type' => $item['type'],
					'quantity' => $item['quantity'],
					'item_uri' => $item['permalink']
				];

				if ( ! empty( $image ) ) {
					$itemData['image_uri'] = $image;
				}

				$items[] = $itemData;
			}

			$chargeRequest = [ 
				'amount' => round( $request['total']['total_price'] / 100, 2 ),
				'currency' => $request['total']['currency_code'],
				'reference' => (string) $reference,
				'customer' => [ 
						'first_name' => $request['address']['first_name'],
						'last_name' => $request['address']['last_name'],
						'email' => $request['address']['email'],
						'phone' => $request['address']['phone'],
						'payment_source' => [ 
								'gateway_id' => $settings->getWalletGatewayId( $payment ),
								'address_line1' => $request['address']['address_1'],
								'address_line2' => ! empty( trim( $request['address']['address_2'] ) )
									? $request['address']['address_2']
									: $request['address']['address_1'],
								'address_city' => $request['address']['city'],
								'address_state' => $request['address']['state'],
								'address_country' => $request['address']['country'],
								'address_postcode' => $request['address']['postcode']
							],
					],
				'meta' => [ 
					'store_name' => get_bloginfo( 'name' ),
				],
				'items' => $items,
				'shipping' => [ 
					'amount' => round( $request['total']['total_shipping'] / 100, 2 ),
					'currency' => $request['total']['currency_code'],
					'address_line1' => $request['shipping_address']['address_1'],
					'address_line2' => $request['shipping_address']['address_2'],
					'address_city' => $request['shipping_address']['city'],
					'address_state' => $request['shipping_address']['state'],
					'address_country' => $request['shipping_address']['country'],
					'address_postcode' => $request['shipping_address']['postcode'],
					'contact' => [ 
							'first_name' => $request['shipping_address']['first_name'],
							'last_name' => $request['shipping_address']['last_name'],
							'phone' => $request['shipping_address']['phone']
						]
				]
			];

			if ( ! empty( $request['shipping_rates'] ) ) {
				$shippingRates = reset( $request['shipping_rates'] );
				foreach ( $shippingRates['shipping_rates'] as $shippingRate ) {
					if ( $shippingRate['selected'] ) {
						if ( 'pickup_location' === $shippingRate['method_id'] ) {
							$location = ShippingHelper::getPickupLocationByKey( $shippingRate['rate_id'] );
							if ( false !== $location ) {
								$chargeRequest['shipping']['address_line1'] = $location['address']['address_1'];
								$chargeRequest['shipping']['address_city'] = $location['address']['city'];
								$chargeRequest['shipping']['address_state'] = $location['address']['state'];
								$chargeRequest['shipping']['address_country'] = $location['address']['country'];
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
					'data' => [],
				];
			}

			if ( $isAfterPay ) {
				$chargeRequest['meta']['success_url'] = wc_get_checkout_url()
					. '?afterpay_success=true&direct_charge='
					. ( $settings->isWalletDirectCharge( $payment ) ? 'true' : 'false' );
				$chargeRequest['meta']['error_url'] = wc_get_checkout_url()
					. '?afterpay_success=false&direct_charge='
					. ( $settings->isWalletDirectCharge( $payment ) ? 'true' : 'false' );
			}

			$result = SDKAdapterService::getInstance()
				->createWalletCharge( $chargeRequest, $settings->isWalletDirectCharge( $payment ) );

			$result['county'] = $request['address']['country'] ?? '';

			if ( WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $payment->name ) {
				$result['pay_later'] = 'yes' === $settings->isPayPallSmartButtonPayLater();
			}

			if ( $isAfterPay && empty( $_SESSION[ AbstractWalletBlock::AFTERPAY_SESSION_KEY ] ) ) {
				$_SESSION[ AbstractWalletBlock::AFTERPAY_SESSION_KEY ] = $result['resource']['data']['charge']['_id'];
			}

			if ( ! empty( $result[ $key ]['error'] ) ) {
				$operation = ucfirst( strtolower( $result[ $key ]['resource']['type'] ?? 'undefined' ) );
				$status = $result[ $key ]['error']['message'] ?? 'empty status';
				$message = $result[ $key ]['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

				$loggerRepository->createLogRecord( '', $operation, $status, $message, LogRepository::ERROR );
			}
		}

		return rest_ensure_response( $result );
	}
}
