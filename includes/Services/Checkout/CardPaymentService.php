<?php

namespace Paydock\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use Paydock\Enums\DSTypes;
use Paydock\Enums\FraudTypes;
use Paydock\Enums\OrderListColumns;
use Paydock\Enums\SaveCardOptions;
use Paydock\Enums\SettingsTabs;
use Paydock\Enums\TypeExchangeOTT;
use Paydock\Enums\WidgetSettings;
use Paydock\Repositories\LogRepository;
use Paydock\Repositories\UserTokenRepository;
use Paydock\Services\OrderService;
use Paydock\Services\ProcessPayment\CardProcessor;
use Paydock\Services\SettingsService;
use Paydock\Services\Validation\ValidationHelperService;
use WC_Payment_Gateway;

class CardPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id         = 'paydock_gateway';
		$this->icon       = apply_filters( 'woocommerce_paydock_gateway_icon', '' );
		$this->has_fields = true;
		$this->supports   = [
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'multiple_subscriptions',
			'default_credit_card_form',
		];

		$this->method_title       = _x( 'Paydock payment', 'Paydock payment method', 'paydock' );
		$this->method_description = __( 'Allows Paydock payments.', 'paydock' );

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$service        = SettingsService::getInstance();
		$keyTitle       = $service->getOptionName( SettingsTabs::WIDGET()->value, [
			WidgetSettings::PAYMENT_CARD_TITLE()->name,
		] );
		$keyDescription = $service->getOptionName(
			SettingsTabs::WIDGET()->value,
			[ WidgetSettings::PAYMENT_CARD_DESCRIPTION()->name ]
		);

		$this->title       = get_option( $keyTitle );
		$this->description = get_option( $keyDescription );
		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action(
			'woocommerce_scheduled_subscription_payment_paydock',
			[ $this, 'process_subscription_payment' ],
			10,
			2
		);

		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );

		add_action( 'wp_ajax_paydock_get_vault_token', [ $this, 'paydock_get_vault_token' ] );

		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'woocommerce_before_checkout_form' ], 10, 1 );
	}

	public function payment_scripts() {
		if ( ! is_checkout() ) {
			return '';
		}

		wp_enqueue_script( 'paydock-form', PAYDOCK_PLUGIN_URL . 'assets/js/frontend/form.js', [], time(), true );
		wp_localize_script( 'paydock-form', 'paydockCardWidgetSettings', [
			'suportedCard' => 'Visa, Mastercard, Adex',
		] );
		wp_localize_script( 'paydock-form', 'paydockWidgetSettings', [
			'pluginUrlPrefix' => PAYDOCK_PLUGIN_URL
		] );
		wp_enqueue_style( 'paydock-widget-css', PAYDOCK_PLUGIN_URL . 'assets/css/frontend/widget.css', [], time() );

		wp_localize_script( 'paydock-form', 'PaydockAjax', [
			'url'     => admin_url( 'admin-ajax.php' ),
			'wpnonce' => wp_create_nonce( 'paydock_get_vault_token' )
		] );
		wp_localize_script( 'paydock-form', 'paydockWidgetSettings', [
			'pluginUrlPrefix' => PAYDOCK_PLUGIN_URL
		] );

		return '';
	}

	public function is_available() {
		return SettingsService::getInstance()->isEnabledPayment()
		       && SettingsService::getInstance()->isCardEnabled();
	}

	public function get_title() {
		return trim( $this->title ) ? $this->title : 'Card';
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

		$siteName    = remove_accents( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$description = sprintf(
		/* Translators: %1$s: number of orders
		*  Translators: %2$s: Site name
		*/
			__( 'Order №%1$s from %2$s.', 'paydock' ),
			$order->get_order_number(),
			$siteName
		);

		$loggerRepository = new LogRepository();
		$chargeId         = '';

		try {
			$cardProcessor = new CardProcessor( array_merge( [
				'amount'      => (float) $order->get_total(),
				'description' => $description,
			], $this->getValidatedPostData() ) );

			$response = $cardProcessor->run( $order );

			if ( ! empty( $response['error'] ) && stripos( $response['error']['message'], '3d' ) === false ) {
				throw new Exception( esc_html( __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later. <input id="widget_error" hidden type="text"/>', 'paydock' ) ) );
			}

			$chargeId = ! empty( $response['resource']['data']['_id'] ) ? $response['resource']['data']['_id'] : '';
		} catch ( Exception $e ) {
			$loggerRepository->createLogRecord(
				$chargeId ?? '',
				'Charge',
				'UnfulfilledCondition',
				$e->getMessage(),
				LogRepository::ERROR
			);
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				/* Translators: %s Error message from API. */
				esc_html( sprintf( __( 'Error: %s', 'paydock' ), $e->getMessage() ) )
			);
		}

		try {
			$cardProcessor->createCustomer();
		} catch ( Exception $e ) {
			$loggerRepository->createLogRecord(
				$chargeId ?? '',
				'Create customer',
				'UnfulfilledCondition',
				$e->getMessage(),
				LogRepository::ERROR
			);
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				/* Translators: %s Error message from API. */
				esc_html( sprintf( __( 'Error: %s', 'paydock' ), $e->getMessage() ) )
			);
		}

		$status          = ucfirst( strtolower( $response['resource']['data']['status'] ?? 'undefined' ) );
		$operation       = ucfirst( strtolower( $response['resource']['type'] ?? 'undefined' ) );
		$isAuthorization = $response['resource']['data']['authorization'] ?? 0;
		$markAsSuccess   = false;
		if (
			'Pre_authentication_pending' === $status &&
			$cardProcessor->getRunMethod() === CardProcessor::FRAUD_IN_BUILD_CHARGE_METHOD
		) {
			$status = 'wc-paydock-pending';
		} else {
			if ( $isAuthorization && in_array( $status, [ 'Pending', 'Pre_authentication_pending' ] ) ) {
				$status = 'wc-paydock-authorize';
			} else {
				$markAsSuccess = true;
				$isCompleted   = 'Complete' === $status;
				$status        = $isCompleted ? 'wc-paydock-paid' : 'wc-paydock-pending';
			}
		}

		OrderService::updateStatus( $order->get_id(), $status );
		if ( ! in_array( $status, [ 'wc-paydock-pending', 'wc-paydock-authorize' ] ) ) {
			$order->payment_complete();
			$order->update_meta_data( 'paydock_directly_charged', 1 );
		}
		$order->update_meta_data( 'paydock_charge_id', $chargeId );
		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), 'Card' );
		WC()->cart->empty_cart();
		$order->save();

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

	protected function getValidatedPostData(): array {
		$postData = array_change_key_case( $_POST, CASE_LOWER );
		if ( ! ( new ValidationHelperService( $postData['amount'] ) )->isFloat() ) {
			wp_die( __( 'wrong "amount" format', 'paydock' ) );
		}
		if ( ! empty( $postData['selectedtoken'] ) &&
		     ! ( new ValidationHelperService( $postData['selectedtoken'] ) )->isUUID() ) {
			wp_die( __( 'wrong "selected token" format', 'paydock' ) );
		}
		if ( ! empty( $postData['paymentsourcetoken'] ) &&
		     ! ( new ValidationHelperService( $postData['paymentsourcetoken'] ) )->isUUID() ) {
			wp_die( __( 'wrong "payment source token" format', 'paydock' ) );
		}
		if ( ! empty( $postData['gatewayid'] ) &&
		     ! ( new ValidationHelperService( $postData['gatewayid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "gateway" ID', 'paydock' ) );
		}
		if ( ! empty( $postData['card3dsserviceid'] ) &&
		     ! ( new ValidationHelperService( $postData['card3dsserviceid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "3ds service" ID', 'paydock' ) );
		}
		if ( ! empty( $postData['charge3dsid'] ) &&
		     ! ( new ValidationHelperService( $postData['charge3dsid'] ) )->isUUID() ) {
			wp_die( __( 'wrong "charge 3ds id" format', 'paydock' ) );
		}
		if ( ! empty( $postData['cardfraudserviceid'] ) &&
		     ! ( new ValidationHelperService( $postData['cardfraudserviceid'] ) )->isServiceId() ) {
			wp_die( __( 'wrong "fraud service" ID', 'paydock' ) );
		}

		$ds             = ! empty( $postData['card3ds'] ) ? sanitize_text_field( $postData['card3ds'] ) : '';
		$dsFlow         = ! empty( $postData['card3dsflow'] ) ? sanitize_text_field( $postData['card3dsflow'] ) : '';
		$fraud          = ! empty( $postData['cardfraud'] ) ? sanitize_text_field( $postData['cardfraud'] ) : '';
		$saveCardOption = ! empty( $postData['cardsavecardoption'] ) ? sanitize_text_field( $postData['cardsavecardoption'] ) : '';

		return [
			'amount'              => (float) $postData['amount'],
			'selectedtoken'       => sanitize_text_field( $postData['selectedtoken'] ),
			'paymentsourcetoken'  => sanitize_text_field( $postData['paymentsourcetoken'] ),
			'gatewayid'           => sanitize_text_field( $postData['gatewayid'] ),
			'card3ds'             => in_array( $ds, [
				DSTypes::IN_BUILD()->name,
				DSTypes::STANDALONE()->name,
				DSTypes::DISABLE()->name,
			] ) ? $ds : DSTypes::DISABLE()->name,
			'card3dsserviceid'    => sanitize_text_field( $postData['card3dsserviceid'] ),
			'card3dsflow'         => in_array( $dsFlow, [
				TypeExchangeOTT::PERMANENT_VAULT()->name,
				TypeExchangeOTT::SESSION_VAULT()->name,
			] ) ? $dsFlow : TypeExchangeOTT::SESSION_VAULT()->name,
			'charge3dsid'         => sanitize_text_field( $postData['charge3dsid'] ),
			'cardfraud'           => in_array( $fraud, [
				FraudTypes::DISABLE()->name,
				FraudTypes::STANDALONE()->name,
				FraudTypes::IN_BUILD()->name,
			] ) ? sanitize_text_field( $postData['cardfraud'] ) : FraudTypes::DISABLE()->name,
			'cardfraudserviceid'  => sanitize_text_field( $postData['cardfraudserviceid'] ),
			'carddirectcharge'    => in_array( sanitize_text_field( $postData['carddirectcharge'] ), [
				'1',
				'true'
			], true ) ? 'true' : 'false',
			'cardsavecard'        => in_array( sanitize_text_field( $postData['cardsavecard'] ), [
				'1',
				'true'
			], true ) ? 'true' : 'false',
			'cardsavecardoption'  => in_array( $saveCardOption, [
				SaveCardOptions::VAULT()->name,
				SaveCardOptions::WITH_GATEWAY()->name,
				SaveCardOptions::WITHOUT_GATEWAY()->name,
			] ) ? $saveCardOption : SaveCardOptions::VAULT()->name,
			'cardsavecardchecked' => in_array( sanitize_text_field( $postData['cardsavecardchecked'] ), [
				'1',
				'true'
			], true ) ? 'true' : 'false',
			'first_name'          => ! empty( $postData['first_name'] ) ? sanitize_text_field( $postData['first_name'] ) : '',
			'last_name'           => ! empty( $postData['last_name'] ) ? sanitize_text_field( $postData['last_name'] ) : '',
			'phone'               => ! empty( $postData['phone'] ) ? sanitize_text_field( $postData['phone'] ) : '',
			'email'               => ! empty( $postData['email'] ) ? sanitize_text_field( $postData['email'] ) : '',
		];
	}

	/**
	 * Ajax function
	 */
	public function paydock_get_vault_token(): void {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'paydock_get_vault_token' ) ) {
			die( 'Security check' );
		}

		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower(
			                                                     sanitize_text_field( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		                                                     ) == 'xmlhttprequest' ) {
			$cardProcessor = new CardProcessor( $this->getValidatedPostData() );
			$type          = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : null;
			try {
				switch ( $type ) {
					case 'clear-user-tokens':
						( new UserTokenRepository() )->deleteAllUserTokens();
						break;
					case 'standalone-3ds-token':
						echo esc_html( $cardProcessor->getStandalone3dsToken() );
						break;
					default:
						echo esc_html( $cardProcessor->getVaultToken() );
				}
			} catch ( Exception $e ) {
				( new LogRepository() )->createLogRecord(
					'',
					'Charges',
					'UnfulfilledCondition',
					$e->getMessage(),
					LogRepository::ERROR
				);
				throw new RouteException(
					'woocommerce_rest_checkout_process_payment_error',
					/* Translators: %s Error message from API. */
					esc_html( sprintf( __( 'Error: %s', 'paydock' ), $e->getMessage() ) )
				);
			}
		} else {
			$referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : '/';
			header( 'Location: ' . $referer );
		}

		die();
	}

	public function woocommerce_before_checkout_form( $arg ) {
	}
}
