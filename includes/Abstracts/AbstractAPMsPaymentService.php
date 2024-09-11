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
			$processor = new ApmProcessor( $_POST );

			$response = $processor->run( $order );

			if ( ! empty( $response['error'] ) ) {

				$parsed_api_error = '';

				if ( ! empty( $response['error']['details'][0]['description'] ) ) {

					$parsed_api_error = $response['error']['details'][0]['description'];

					if ( ! empty( $response['error']['details'][0]['status_code_description'] ) ) {
						$parsed_api_error .= ': ' . $response['error']['details'][0]['status_code_description'];
					}

				}

				if ( empty( $parsed_api_error ) ) {
					$parsed_api_error = __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.', 'paydock' );
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
			$status = 'wc-paydock-authorize';
		} else {
			$isCompleted = 'complete' === strtolower( $status );
			$status      = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-pending';
		}
		OrderService::updateStatus( $order_id, $status );
		if ( ! in_array( $status, [ 'wc-paydock-authorize' ] ) ) {
			$order->payment_complete();
			$order->update_meta_data( 'paydock_directly_charged', 1 );
		}
		$order->update_meta_data( 'paydock_charge_id', $chargeId );
		$order->save();

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$status,
			'',
			'wc-paydock-paid' == $status ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), $this->getAPMsType()->getLabel() );
		$order->save();

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function payment_scripts() {
		return SettingsService::getInstance()->getWidgetScriptUrl();
	}

	public function webhook() {
	}
}
