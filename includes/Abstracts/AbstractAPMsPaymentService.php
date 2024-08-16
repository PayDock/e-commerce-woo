<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Repositories\UserCustomerRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\ProcessPayment\ApmProcessor;
use PowerBoard\Services\SettingsService;

abstract class AbstractAPMsPaymentService extends AbstractPaymentService {
	/**
	 * Constructor
	 */
	public function __construct() {
		$settings      = SettingsService::getInstance();
		$paymentMethod = $this->getAPMsType();
		$id            = $paymentMethod->getId();

		$this->id    = 'power_board_' . $id . '_wallets_gateway';
		$this->title = $settings->getWidgetPaymentAPMTitle( $paymentMethod );

		$this->id          = 'power_board_' . $paymentMethod->getId() . '_a_p_m_s_gateway';
		$this->description = $settings->getWidgetPaymentAPMDescription( $paymentMethod );

		parent::__construct();
	}

	abstract protected function getAPMsType(): OtherPaymentMethods;

	public function is_available() {
		$paymentMethod = $this->getAPMsType();

		if ( is_checkout() ) {
			$this->title = '<img src="/wp-content/plugins/power-board/assets/images/icons/' .
			               $paymentMethod->getId() .
			               '.png" height="25" class="power-board-payment-method-label-icon ' .
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
				esc_html( __( 'Error: Security check', 'power-board' ) )
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

			if ( ! empty( $response['error'] ) || empty( $response['resource']['data']['_id'] ) ) {
				throw new Exception( __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.',
					'power-board' ) );
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
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				/* translators: %s: Error message */
				esc_html( sprintf( __( 'Error: %s', 'power-board' ), $e->getMessage() ) )
			);
		}

		$status          = ucfirst( strtolower( $response['resource']['data']['transactions'][0]['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $response['resource']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		if ( $isAuthorization && 'Pending' == $status ) {
			$status = 'wc-pb-authorize';
		} else {
			$isCompleted = 'complete' === strtolower( $status );
			$status      = $isCompleted ? 'wc-pb-paid' : 'wc-pb-pending';
		}
		OrderService::updateStatus( $order_id, $status );
		$order->payment_complete();
		$order->save();

		update_post_meta( $order->get_id(), 'power_board_charge_id', $chargeId );

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$status,
			'',
			'wc-pb-paid' == $status ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		add_post_meta(
			$order->get_id(),
			OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(),
			$this->getAPMsType()->getLabel()
		);

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
