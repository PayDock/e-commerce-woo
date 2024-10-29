<?php

namespace WooPlugin\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use WooPlugin\Enums\OrderListColumns;
use WooPlugin\Enums\OtherPaymentMethods;
use WooPlugin\Repositories\LogRepository;
use WooPlugin\Repositories\UserCustomerRepository;
use WooPlugin\Services\OrderService;
use WooPlugin\Services\ProcessPayment\ApmProcessor;
use WooPlugin\Services\SettingsService;

abstract class AbstractAPMsPaymentService extends AbstractPaymentService {
	/**
	 * Constructor
	 */
	public function __construct() {
		$settings      = SettingsService::getInstance();
		$paymentMethod = $this->getAPMsType();
		$id            = $paymentMethod->getId();

		$this->id    = 'plugin_' . $id . '_wallets_gateway';
		$this->title = $settings->getWidgetPaymentAPMTitle( $paymentMethod );

		$this->id          = 'plugin_' . $paymentMethod->getId() . '_a_p_m_s_gateway';
		$this->description = $settings->getWidgetPaymentAPMDescription( $paymentMethod );

		parent::__construct();
	}

	abstract protected function getAPMsType(): OtherPaymentMethods;

	public function is_available() {
		$paymentMethod = $this->getAPMsType();

		if ( is_checkout() ) {
			$this->title = '<img src="/wp-content/plugins/' . PLUGIN_TEXT_DOMAIN . '/assets/images/icons/' .
			               $paymentMethod->getId() .
			               '.png" height="25" class="plugin-payment-method-label-icon ' .
			               $paymentMethod->getId() .
			               '">' .
			               SettingsService::getInstance()->getWidgetPaymentAPMTitle( $paymentMethod );
		}

		$minMax = SettingsService::getInstance()->getWidgetPaymentAPMsMinMax( $paymentMethod );

		if ( is_checkout() && WC()->cart && ( $minMax['min'] > 0 ) && ( WC()->cart->total < $minMax['min'] ) ) {
			return false;
		}

		if ( is_checkout() && WC()->cart && ! empty( $minMax['max'] ) && ( WC()->cart->total > $minMax['max'] ) ) {
			return false;
		}

		return SettingsService::getInstance()->isEnabledPayment()
		       && SettingsService::getInstance()->isAPMsEnabled( $this->getAPMsType() );
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id, $retry = true, $force_customer = false ) {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'process_payment' ) ) {
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				esc_html( __( 'Error: Security check', PLUGIN_TEXT_DOMAIN ) )
			);
		}

		$order = wc_get_order( $order_id );

		$loggerRepository = new LogRepository();
		$chargeId         = '';

		try {
			$processor = new ApmProcessor(
				$_POST,
				$this->getAPMsType()
			);

			$response = $processor->run( $order );

			if ( ! empty( $response['error'] ) ) {

				$parsed_api_error = '';

				if ( ! empty( $response['error']['details'][0]['description'] ) ) {

					$parsed_api_error = $response['error']['details'][0]['description'];

					if ( ! empty( $response['error']['details'][0]['status_code_description'] ) ) {
						$parsed_api_error .= ': ' . $response['error']['details'][0]['status_code_description'];
					}

				} elseif ( ! empty( $response['error']['message'] ) ) {
					$parsed_api_error = $response['error']['message'];
				}

				if ( empty( $parsed_api_error ) ) {
					$parsed_api_error = __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.', PLUGIN_TEXT_DOMAIN );
				}

				throw new Exception( esc_html( $parsed_api_error ) );

			}

			$chargeId = $response['resource']['data']['_id'];
		} catch ( Exception $e ) {
			$loggerRepository->createLogRecord(
				$chargeId ?? '',
				'Charges',
				'UnfulfilledCondition',
				$e->getMessage(),
				LogRepository::ERROR
			);

			throw new RouteException( 'woocommerce_rest_checkout_process_payment_error', esc_html( $e->getMessage() ) );
		}

		$status          = ucfirst( strtolower( $response['resource']['data']['transactions'][0]['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $response['resource']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		if ( $isAuthorization && 'Pending' == $status ) {
			$status = 'on-hold';
		} else {
			$isCompleted = 'complete' === strtolower( $status );
			$status      = $isCompleted ? 'processing' : 'pending';
		}
		OrderService::updateStatus( $order_id, $status );
		if ( ! in_array( $status, [ 'on-hold' ] ) ) {
			$order->payment_complete();
			$order->update_meta_data( 'pb_directly_charged', 1 );
		}
		$order->update_meta_data( PLUGIN_PREFIX . '_charge_id', $chargeId );
		$order->save();

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$status,
			'',
			'processing' == $status ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), $this->getAPMsType()->getLabel() );
		$order->save();

		$order->set_payment_method_title( SettingsService::getInstance()->getWidgetPaymentAPMTitle( $this->getAPMsType() ) );
		$order->save();

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function webhook() {
	}

	public function get_payment_method_data(): array {
		$settingsService = SettingsService::getInstance();
		$payment         = $this->getAPMsType();

		$userCustomers = [];
		if ( is_user_logged_in() ) {
			$userCustomers = [
				'customers' => ( new UserCustomerRepository() )->getUserCustomers(),
			];
		}
		$items = [];
		foreach ( WC()->cart->get_cart_contents() as $item ) {
			$product = wc_get_product( $item['product_id'] );
			$items[] = [
				'name'      => $product->get_name( false ),
				'amount'    => $product->get_price( false ),
				'quantity'  => $item['quantity'],
				'reference' => $product->get_permalink(),
			];
		}

		return array_merge( $userCustomers, [
			// Wordpress data
			'_wpnonce'           => wp_create_nonce( 'process_payment' ),
			'isUserLoggedIn'     => is_user_logged_in(),
			'isSandbox'          => $settingsService->isSandbox(),
			// Woocommerce data
			'amount'             => WC()->cart->total,
			'items'              => $items,
			'currency'           => strtoupper( get_woocommerce_currency() ),
			// Widget
			'title'              => $settingsService->getWidgetPaymentAPMTitle( $payment ),
			'description'        => $settingsService->getWidgetPaymentAPMDescription( $payment ),
			'styles'             => $settingsService->getWidgetStyles(),
			// Apms
			'enable'             => $settingsService->isAPMsEnabled( $payment ),
			'gatewayId'          => $settingsService->getAPMsGatewayId( $payment ),
			// Tokens & keys
			'publicKey'          => $settingsService->getPublicKey(),
			'paymentSourceToken' => '',
			// SaveCard
			'saveCard'           => $settingsService->isAPMsSaveCard( $payment ),
			// DirectCharge
			'directCharge'       => $settingsService->isAPMsDirectCharge( $payment ),
			// Fraud
			'fraud'              => $settingsService->isAPMsFraud( $payment ),
			'fraudServiceId'     => $settingsService->getAPMsFraudServiceId( $payment ),
			// Other
			'supports'           => array_filter( $this->supports, [ $this, 'supports' ] ),
			'pickupLocations'    => get_option( 'pickup_location_pickup_locations' ),
			'total_limitation'   => $settingsService->getWidgetPaymentAPMsMinMax( $payment ),
		] );
	}
}
