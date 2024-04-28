<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\ProcessPayment\ApmProcessor;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;

abstract class AbstractAPMsPaymentService extends AbstractPaymentService {
	/**
	 * Constructor
	 */
	public function __construct() {
		$settings = SettingsService::getInstance();
		$paymentMethod = $this->getAPMsType();

		$this->id = 'power_board_' . $paymentMethod->getId() . '_a_p_m_s_gateway';
		$this->title = $settings->getWidgetPaymentAPMTitle( $paymentMethod );
		$this->description = $settings->getWidgetPaymentAPMDescription( $paymentMethod );

		parent::__construct();
	}

	abstract protected function getAPMsType(): OtherPaymentMethods;

	public function is_available() {
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
				__( 'Error: Security check', 'power_board' )
			);
		}
		
		$order = wc_get_order( $order_id );

		$loggerRepository = new LogRepository();
		$chargeId = '';

		try {
			$processor = new ApmProcessor( $_POST );

			$response = $processor->run( $order );

			if ( ! empty( $response['error'] ) || empty( $response['resource']['data']['_id'] ) ) {
				throw new Exception( __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.', 'power_board' ) );
			}

			$chargeId = $response['resource']['data']['_id'];
		} catch (Exception $e) {
			$loggerRepository->createLogRecord(
				$chargeId ?? '',
				'Charges',
				'UnfulfilledCondition',
				$e->getMessage(),
				LogRepository::ERROR
			);
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				__( 'Error:', 'power_board' ) . ' ' . $e->getMessage()
			);
		}

		$status = ucfirst( strtolower( $response['resource']['data']['transactions'][0]['status'] ?? 'undefined' ) );
		$operation = ucfirst( strtolower( $response['resource']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		if ( $isAuthorization && 'Pending' == $status ) {
			$status = 'wc-pb-authorize';
		} else {
			$isCompleted = 'complete' === strtolower( $status );
			$status = $isCompleted ? 'wc-pb-paid' : 'wc-pb-pending';
		}
		$order->set_status( $status );
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

		return [ 
			'result' => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function payment_scripts() {
		return SettingsService::getInstance()->getWidgetScriptUrl();
	}

	public function webhook() {
	}
}
