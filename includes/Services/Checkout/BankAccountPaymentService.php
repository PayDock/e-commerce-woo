<?php

namespace Paydock\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use Paydock\Abstracts\AbstractPaymentService;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\OrderListColumns;
use Paydock\Enums\SaveCardOptions;
use Paydock\Enums\TypeExchangeOTT;
use Paydock\Exceptions\LoggedException;
use Paydock\Repositories\LogRepository;
use Paydock\Services\OrderService;
use Paydock\Services\ProcessPayment\BankAccountProcessor;
use Paydock\Services\SettingsService;
use Paydock\Services\Validation\ValidationHelperService;

class BankAccountPaymentService extends AbstractPaymentService {
	public function __construct() {
		$settings = SettingsService::getInstance();

		$this->id          = 'paydock_bank_account_gateway';
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
				esc_html( __( 'Error: Security check', 'paydock' ) )
			);
		}

		$order = wc_get_order( $order_id );

		$loggerRepository = new LogRepository();
		$chargeId         = '';

		try {
			$processor = new BankAccountProcessor( $order, $this->getValidatedPostData() );

			$response = $processor->run( $order_id );
			$chargeId = ! empty( $response['resource']['data']['_id'] ) ? $response['resource']['data']['_id'] : '';
		} catch ( LoggedException $exception ) {

			$operation = ucfirst( strtolower( $exception->response['resource']['type'] ?? 'undefined' ) );
			$status    = $exception->response['error']['message'] ?? 'empty status';
			$message   = $exception->response['error']['details'][0]['gateway_specific_description'] ?? 'empty message';

			$loggerRepository->createLogRecord( $chargeId, $operation, $status, $message, LogRepository::ERROR );
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				/* Translators: %s Exception message. */
				esc_html( sprintf( __( 'Error: %s', 'paydock' ), $exception->getMessage() ) )
			);
		} catch ( Exception $exception ) {
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				/* Translators: %s Exception message. */
				esc_html( sprintf( __( 'Error: %s', 'paydock' ), $exception->getMessage() ) )
			);
		}

		$status          = ucfirst( strtolower( $response['resource']['data']['transactions'][0]['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $response['resource']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		$markAsSuccess   = false;
		if ( $isAuthorization && 'Pending' == $status ) {
			$status = 'wc-paydock-authorize';
		} else {
			$markAsSuccess = true;
			$isCompleted   = 'Complete' === $status;
			$status        = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-requested';
		}

		OrderService::updateStatus( $order->get_id(), $status );
		$order->payment_complete();
		$order->update_meta_data( 'pb_directly_charged', 1 );
		$order->update_meta_data( 'paydock_charge_id', $chargeId );
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

	protected function getValidatedPostData(): array {
		if ( ! ( new ValidationHelperService( $_POST['amount'] ) )->isFloat() ) {
			wp_die( __( 'wrong "amount" format', 'paydock' ) );
		}
		if ( ! empty( $_POST['selectedtoken'] ) && ! ( new ValidationHelperService( $_POST['selectedtoken'] ) )->isUUID() ) {
			wp_die( __( 'wrong "selected token" format', 'paydock' ) );
		}
		if ( ! empty( $_POST['paymentsourcetoken'] ) && ! ( new ValidationHelperService( $_POST['paymentsourcetoken'] ) )->isUUID() ) {
			wp_die( __( 'wrong "payment source token" format', 'paydock' ) );
		}
		if ( ! empty( $_POST['gatewayid'] ) && ! ( new ValidationHelperService( $_POST['gatewayid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "gateway" ID', 'paydock' ) );
		}
		if ( ! empty( $_POST['card3dsserviceid'] ) && ! ( new ValidationHelperService( $_POST['gatewcard3dsserviceidayid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "3ds service" ID', 'paydock' ) );
		}
		if ( ! empty( $_POST['charge3dsid'] ) && ! ( new ValidationHelperService( $_POST['charge3dsid'] ) )->isUUID() ) {
			wp_die( __( 'wrong "charge 3ds id" format', 'paydock' ) );
		}
		if ( ! empty( $_POST['cardfraudserviceid'] ) && ! ( new ValidationHelperService( $_POST['cardfraudserviceid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "fraud service" ID', 'paydock' ) );
		}

		$ds             = sanitize_text_field( $_POST['card3ds'] );
		$dsFlow         = sanitize_text_field( $_POST['card3dsflow'] );
		$fraud          = sanitize_text_field( $_POST['card3dsfraud'] );
		$saveCardOption = sanitize_text_field( $_POST['savecardoptions'] );

		return [
			'amount'              => (float) $_POST['amount'],
			'selectedtoken'       => sanitize_text_field( $_POST['selectedtoken'] ),
			'paymentsourcetoken'  => sanitize_text_field( $_POST['paymentsourcetoken'] ),
			'gatewayid'           => sanitize_text_field( $_POST['gatewayid'] ),
			'card3ds'             => in_array( $ds, [
				DSTypes::IN_BUILD()->name,
				DSTypes::STANDALONE()->name,
				DSTypes::DISABLE()->name,
			] ) ? $ds : DSTypes::DISABLE()->name,
			'card3dsserviceid'    => sanitize_text_field( $_POST['card3dsserviceid'] ),
			'card3dsflow'         => in_array( $dsFlow, [
				TypeExchangeOTT::PERMANENT_VAULT()->name,
				TypeExchangeOTT::SESSION_VAULT()->name,
			] ) ? $dsFlow : TypeExchangeOTT::SESSION_VAULT()->name,
			'charge3dsid'         => sanitize_text_field( $_POST['charge3dsid'] ),
			'cardfraud'           => in_array( $fraud, [
				FraudTypes::DISABLE()->name,
				FraudTypes::STANDALONE()->name,
				FraudTypes::IN_BUILD()->name,
			] ),
			'cardfraudserviceid'  => sanitize_text_field( $_POST['cardfraudserviceid'] ),
			'carddirectcharge'    => (int) $_POST['cardfraudcharge'] > 0,
			'cardsavecard'        => (int) $_POST['cardfraudcharge'] > 0,
			'cardsavecardoption'  => in_array( $saveCardOption, [
				SaveCardOptions::VAULT()->name,
				SaveCardOptions::WITH_GATEWAY()->name,
				SaveCardOptions::WITHOUT_GATEWAY()->name,
			] ) ? $saveCardOption : SaveCardOptions::VAULT()->name,
			'cardsavecardchecked' => (int) $_POST['cardfraudcharge'] > 0,
		];
	}
}
