<?php

namespace PowerBoard\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use Exception;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Enums\SettingsTabs;
use PowerBoard\Enums\WidgetSettings;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Repositories\UserTokenRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\ProcessPayment\CardProcessor;
use PowerBoard\Services\SettingsService;
use WC_Payment_Gateway;

class CardPaymentService extends WC_Payment_Gateway {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id         = 'power_board_gateway';
		$this->icon       = apply_filters( 'woocommerce_power_board_gateway_icon', '' );
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
			'woocommerce_scheduled_subscription_payment_power_board',
			[ $this, 'process_subscription_payment' ],
			10,
			2
		);

		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );

		add_action( 'wp_ajax_nopriv_get_vault_token', [ $this, 'get_vault_token' ] );
		add_action( 'wp_ajax_get_vault_token', [ $this, 'get_vault_token' ] );

		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'woocommerce_before_checkout_form' ], 10, 1 );
	}

	public function payment_scripts() {
        if ( ! is_checkout() ) {
			return '';
		}

		wp_enqueue_script( 'power-board-form', POWER_BOARD_PLUGIN_URL . 'assets/js/frontend/form.js', [], time(), true );
		wp_localize_script( 'power-board-form', 'powerBoardCardWidgetSettings', [
			'suportedCard'    => 'Visa, Mastercard, Adex',
		] );
		wp_localize_script( 'power-board-form', 'powerBoardWidgetSettings', [
			'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
		] );
		wp_enqueue_style( 'power-board-widget-css', POWER_BOARD_PLUGIN_URL . 'assets/css/frontend/widget.css', [], time() );

		wp_localize_script( 'power-board-form', 'PowerBoardAjax', [
			'url'     => admin_url( 'admin-ajax.php' ),
			'wpnonce'     => wp_create_nonce( 'create_wallet_charge' ),
			'wpnonce' => wp_create_nonce( 'get_vault_token' )
		] );
		wp_localize_script( 'power-board-form', 'powerBoardWidgetSettings', [
			'pluginUrlPrefix' => POWER_BOARD_PLUGIN_URL
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
			], $_POST ) );

			$response = $cardProcessor->run( $order );

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
					$parsed_api_error = __( 'Oops! We\'re experiencing some technical difficulties at the moment. Please try again later.', 'power-board' );
				}

				$parsed_api_error .= ' widget_error';

				throw new Exception( esc_html( $parsed_api_error ) );

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

			throw new RouteException( 'woocommerce_rest_checkout_process_payment_error', esc_html( $e->getMessage() ) );
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
			$order->update_meta_data( 'capture_amount', $order->get_total() );
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

	/**
	 * Ajax function
	 */
	public function get_vault_token(): void {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'get_vault_token' ) ) {
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

	public function woocommerce_before_checkout_form( $arg ) {
	}
}
