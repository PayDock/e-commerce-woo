<?php

namespace PowerBoard\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\ProcessPayment\CardProcessor;
use PowerBoard\Services\SDKAdapterService;
use PowerBoard\Services\SettingsService;
use PowerBoard\Services\TemplateService;
use WC_Payment_Gateway;

class CardPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id         = 'power_board_gateway';
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

		$this->method_title       = _x( 'PowerBoard payment', 'PowerBoard payment method', 'power-board' );
		$this->method_description = __( 'Allows PowerBoard payments.', 'power-board' );

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$service = SettingsService::getInstance();
		$this->title       = SettingsService::getInstance()->getWidgetPaymentCardTitle();
		$this->description = $service->getWidgetPaymentCardDescription();

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action(
			'woocommerce_scheduled_subscription_payment_power_board',
			[ $this, 'process_subscription_payment' ],
			10,
			2
		);

		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );

		add_action( 'wp_ajax_power_board_get_vault_token', [ $this, 'power_board_get_vault_token' ] );
		add_action( 'wp_ajax_power_board_create_error_notice', [ $this, 'power_board_create_error_notice' ], 20 );

		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'woocommerce_before_checkout_form' ], 10, 1 );
	}

	public function payment_scripts() {
        if ( ! is_checkout() ) {
			return '';
		}

		wp_enqueue_script( 'power-board-form', POWER_BOARD_PLUGIN_URL . 'assets/js/frontend/form.js', [], time(), true );
		wp_enqueue_script( 'power-board-classic-form', POWER_BOARD_PLUGIN_URL . '/assets/js/frontend/classic-form.js', [], time(), true );
		wp_localize_script( 'power-board-form', 'powerBoardCardWidgetSettings', [
			'suportedCard'    => 'Visa, Mastercard, Adex',
		] );
		wp_enqueue_style( 'power-board-widget-css', POWER_BOARD_PLUGIN_URL . 'assets/css/frontend/widget.css', [], time() );

		wp_localize_script( 'power-board-form', 'PowerBoardAjax', [
			'url'         => admin_url( 'admin-ajax.php' ),
			'wpnonce'     => wp_create_nonce( 'power-board-create-wallet-charge' ),
			'wpnonce_3ds' => wp_create_nonce( 'power_board_get_vault_token' ),
		] );
		wp_enqueue_script( 'axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', [], time(), true );

		return '';
	}

	public function is_available() {
		if ( is_checkout() && ! is_order_received_page() ) {
			$this->title = '<img src="/wp-content/plugins/power-board/assets/images/icons/card.png"
								  height="25"
								  class="power-board-payment-method-label-icon card">
							 <span class="power-board-payment-method-label-title card">' .
			               SettingsService::getInstance()->getWidgetPaymentCardTitle() .
			               '</span> <img class="power-board-payment-method-label-icon card logo"
							  src="' . POWER_BOARD_PLUGIN_URL . 'assets/images/logo.png"
		                     class="power-board-payment-method-label-logo"
							 height="36">';
		}

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
				esc_html( __( 'Error: Security check', 'power-board' ) )
			);
		}

		$order = wc_get_order( $order_id );

		$siteName    = remove_accents( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
		$description = sprintf(
		/* Translators: %1$s: number of orders
		*  Translators: %2$s: Site name
		*/
			__( 'Order №%1$s from %2$s.', 'power-board' ),
			$order->get_order_number(),
			$siteName
		);

		$loggerRepository = new LogRepository();
		$chargeId         = '';

		try {
			$cardProcessor = new CardProcessor( array_merge( [
				'amount'      => (float) $order->get_total(),
				'description' => $description,
			], $this->getSettings(), $_POST ) );


			$response = $cardProcessor->run( $order );

			if ( ! empty( $response['error'] ) && stripos( $response['error']['message'], '3d' ) === false) {
				throw new Exception( esc_html( __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later. <input id="widget_error" hidden type="text"/>', 'power-board' ) ) );
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
				esc_html( sprintf( __( 'Error: %s', 'power-board' ), $e->getMessage() ) )
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
				esc_html( sprintf( __( 'Error: %s', 'power-board' ), $e->getMessage() ) )
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
			$status = 'wc-pb-pending';
		} else {
			if ( $isAuthorization && in_array( $status, [ 'Pending', 'Pre_authentication_pending' ] ) ) {
				$status = 'wc-pb-authorize';
			} else {
				$markAsSuccess = true;
				$isCompleted   = 'Complete' === $status;
				$status        = $isCompleted ? 'wc-pb-paid' : 'wc-pb-pending';
			}
		}

		OrderService::updateStatus( $order->get_id(), $status );
		if ( ! in_array( $status, [ 'wc-pb-pending', 'wc-pb-authorize' ] ) ) {
			$order->payment_complete();
			$order->update_meta_data( 'pb_directly_charged', 1 );
		}

		$order->update_meta_data( 'power_board_charge_id', $chargeId );
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

	public function getSettings() {
		$settingsService = SettingsService::getInstance();
		$userTokens      = [];
		if ( is_user_logged_in() ) {
			$userTokens = ( new UserTokenRepository() )->getUserTokens();
		}

		return [
			'tokens'                 => $userTokens,
			// Wordpress data
			'_wpnonce'               => wp_create_nonce( 'process_payment' ),
			'isUserLoggedIn'         => is_user_logged_in(),
			'isSandbox'              => $settingsService->isSandbox(),
			// Woocommerce data
			'amount'                 => WC()->cart->total,
			'currency'               => strtoupper( get_woocommerce_currency() ),
			// Widget
			'title'                  => $settingsService->getWidgetPaymentCardTitle(),
			'description'            => $settingsService->getWidgetPaymentCardDescription(),
			'styles'                 => $settingsService->getWidgetStyles(),
			// Tokens & keys
			'publicKey'              => $settingsService->getPublicKey(),
			'selectedToken'          => '',
			'paymentSourceToken'     => '',
			'cvv'                    => '',
			// Card
			'cardSupportedCardTypes' => $settingsService->getCardSupportedCardTypes(),
			'gatewayId'              => $settingsService->getCardGatewayId(),
			// 3DS
			'card3DS'                => $settingsService->getCard3DS(),
			'card3DSServiceId'       => $settingsService->getCard3DSServiceId(),
			'card3DSFlow'            => $settingsService->getCardTypeExchangeOtt(),
			'charge3dsId'            => '',
			// Fraud
			'cardFraud'              => $settingsService->getCardFraud(),
			'cardFraudServiceId'     => $settingsService->getCardFraudServiceId(),
			// DirectCharge
			'cardDirectCharge'       => $settingsService->getCardDirectCharge(),
			// SaveCard
			'cardSaveCard'           => $settingsService->getCardSaveCard(),
			'cardSaveCardOption'     => $settingsService->getCardSaveCardOption(),
			'cardSaveCardChecked'    => false,
			// Other
			'supports'               => array_filter( $this->supports, [ $this, 'supports' ] ),
		];
	}


	/**
	 * Ajax function
	 */
	public function power_board_get_vault_token(): void {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'power_board_get_vault_token' ) &&
			 ! wp_verify_nonce( $wpNonce, 'power-board-create-wallet-charge' ) ) {
			die( 'Security check' );
		}

		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower(
			                                                     sanitize_text_field( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		                                                     ) == 'xmlhttprequest' ) {
			$cardProcessor = new CardProcessor( $_POST );
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
					esc_html( sprintf( __( 'Error: %s', 'power-board' ), $e->getMessage() ) )
				);
			}
		} else {
			$referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : '/';
			header( 'Location: ' . $referer );
		}

		die();
	}

	/**
	 * Ajax function
	 */
	public function power_board_create_error_notice() {
		wc_add_notice( __( $_POST['error'], 'power-board' ), 'error' );
		$response['data'] = wc_print_notices();
		return $response;
	}

	public function woocommerce_before_checkout_form( $arg ) {
	}

	public function payment_fields() {
		$template = new TemplateService ( $this );
		SDKAdapterService::getInstance();

		$settings = $this->getSettings();

		$template->includeCheckoutHtml( 'method-form', [
			'description'      => $this->description,
			'id'               => $this->id,
			'card3DSFlow'      => $settings['card3DSFlow'],
			'isSaveCardEnable' => $settings['cardSaveCard'],
			'nonce'            => wp_create_nonce( 'process_payment' ),
			'isUserLoggedIn'   => is_user_logged_in(),
			'tokens'           => $settings['tokens'],
			'settings'         => wp_json_encode( $settings )
		] );
	}

}
