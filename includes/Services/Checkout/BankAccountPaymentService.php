<?php

namespace WooPlugin\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use WooPlugin\Abstracts\AbstractPaymentService;
use WooPlugin\Enums\OrderListColumns;
use WooPlugin\Exceptions\LoggedException;
use WooPlugin\Repositories\LogRepository;
use WooPlugin\Services\OrderService;
use WooPlugin\Services\ProcessPayment\BankAccountProcessor;
use WooPlugin\Services\SettingsService;

class BankAccountPaymentService extends AbstractPaymentService {
	public function __construct() {
		$settings = SettingsService::getInstance();

		$this->id          = PLUGIN_PREFIX . '_bank_account_gateway';
		$this->title       = $settings->getWidgetPaymentBankAccountTitle();
		$this->description = $settings->getWidgetPaymentBankAccountDescription();

		parent::__construct();
	}

	public function is_available() {
		return SettingsService::getInstance()->isEnabledPayment()
		       && SettingsService::getInstance()->isBankAccountEnabled();
	}

	public function payment_scripts() {
		return SettingsService::getInstance()->getWidgetScriptUrl();
	}

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
			$processor = new BankAccountProcessor( $order, $_POST );

			$response = $processor->run( $order_id );
			$chargeId = ! empty( $response['resource']['data']['_id'] ) ? $response['resource']['data']['_id'] : '';
		} catch ( LoggedException $exception ) {

			$operation = ucfirst( strtolower( $exception->response['resource']['type'] ?? 'undefined' ) );
			$status    = $exception->response['error']['message'] ?? 'empty status';
			$message   = $exception->response['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

			$loggerRepository->createLogRecord( $chargeId, $operation, $status, $message, LogRepository::ERROR );

			throw new RouteException( 'woocommerce_rest_checkout_process_payment_error', esc_html( $exception->getMessage() ) );
		} catch ( Exception $exception ) {
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				/* Translators: %s Exception message. */
				esc_html( sprintf( __( 'Error: %s', PLUGIN_TEXT_DOMAIN ), $exception->getMessage() ) )
			);
		}

		$status          = ucfirst( strtolower( $response['resource']['data']['transactions'][0]['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $response['resource']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		$markAsSuccess   = false;
		if ( $isAuthorization && 'Pending' == $status ) {
			$status = 'wc-pb-authorize';
		} else {
			$markAsSuccess = true;
			$isCompleted   = 'Complete' === $status;
			$status        = $isCompleted ? 'wc-pb-paid' : 'wc-pb-requested';
		}

		OrderService::updateStatus( $order->get_id(), $status );
		$order->payment_complete();
		$order->update_meta_data( 'pb_directly_charged', 1 );
		$order->update_meta_data( PLUGIN_PREFIX . '_charge_id', $chargeId );
		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), 'Bank' );
		$order->save();

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$chargeId,
			$operation,
			$status,
			'',
			$markAsSuccess ? LogRepository::SUCCESS : LogRepository::DEFAULT
		);

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function webhook() {

	}

	public function get_payment_method_data(): array
    {
        return [];
    }

}
