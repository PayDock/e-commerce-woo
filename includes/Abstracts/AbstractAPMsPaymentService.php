<?php

namespace Paydock\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use Paydock\Enums\OrderListColumns;
use Paydock\Enums\OtherPaymentMethods;
use Paydock\Repositories\LogRepository;
use Paydock\Services\OrderService;
use Paydock\Services\ProcessPayment\ApmProcessor;
use Paydock\Services\SDKAdapterService;
use Paydock\Services\SettingsService;
use Paydock\Services\Validation\ValidationHelperService;

abstract class AbstractAPMsPaymentService extends AbstractPaymentService {
	/**
	 * Constructor
	 */
	public function __construct() {
		$settings      = SettingsService::getInstance();
		$paymentMethod = $this->getAPMsType();

		$this->id          = 'paydock_' . $paymentMethod->getId() . '_a_p_m_s_gateway';
		$this->title       = $settings->getWidgetPaymentAPMTitle( $paymentMethod );
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
				esc_html( __( 'Error: Security check', 'paydock' ) )
			);
		}

		$order = wc_get_order( $order_id );

		$loggerRepository = new LogRepository();
		$chargeId         = '';

		try {
			$processor = new ApmProcessor( $this->getValidatedData() );

			$response = $processor->run( $order );

			if ( ! empty( $response['error'] ) || empty( $response['resource']['data']['_id'] ) ) {
				throw new Exception( __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.',
					'paydock' ) );
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
				esc_html( sprintf( __( 'Error: %s', 'paydock' ), $e->getMessage() ) )
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
		if ( ! in_array( $status, [ 'wc-pb-authorize' ] ) ) {
			$order->payment_complete();
			$order->update_meta_data( 'pb_directly_charged', 1 );
		}
		$order->update_meta_data( 'paydock_charge_id', $chargeId );
		$order->save();

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$status,
			'',
			'wc-pb-paid' == $status ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), $this->getAPMsType()->getLabel() );
		$order->save();

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function getValidatedData() {
		$postData = array_change_key_case( $_POST, CASE_LOWER );
		if ( ! ( new ValidationHelperService( $postData['amount'] ) )->isFloat() ) {
			wp_die( __( 'wrong "amount" format', 'paydock' ) );
		}
		if ( ! empty( sanitize_text_field( $postData['gatewayid'] ) ) &&
		     ! ( new ValidationHelperService( $postData['gatewayid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "gateway" ID', 'paydock' ) );
		}
		if ( ! empty( $postData['paymentsourcetoken'] ) &&
		     ! ( new ValidationHelperService( $postData['paymentsourcetoken'] ) )->isUUID() ) {
			wp_die( __( 'wrong "payment source token" format', 'paydock' ) );
		}
		if ( ! empty( $postData['fraudserviceid'] ) &&
		     ! ( new ValidationHelperService( $postData['fraudserviceid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "fraud service" ID', 'paydock' ) );
		}
		if ( ! empty( $postData['gatewaytype'] ) &&
		     ! ( new ValidationHelperService( $postData['gatewaytype'] ) )->isValidAPMId() ) {
			wp_die( __( 'wrong "wrong payment method" ID', 'paydock' ) );
		}


		return [
			'amount'             => ! empty ( $postData['amount'] ) ? (float) $postData['amount'] : 0,
			'gatewayid'          => sanitize_text_field( $postData['gatewayid'] ),
			'paymentsourcetoken' => $postData['paymentsourcetoken'],
			'directcharge'       => (int) $postData['directcharge'] > 0,
			'fraud'              => (int) $postData['fraud'] > 0,
			'fraudserviceid'     => sanitize_text_field( $postData['fraudserviceid'] ),
			'gatewaytype'        => sanitize_text_field( $postData['gatewaytype'] ),
		];
	}

	public function payment_scripts() {
		return SettingsService::getInstance()->getWidgetScriptUrl();
	}

	public function webhook() {
	}
}
