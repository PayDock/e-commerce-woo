<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\SettingsService;

abstract class AbstractWalletPaymentService extends AbstractPaymentService {
	public function __construct() {
		$settings = SettingsService::getInstance();
		$paymentMethod = $this->getWalletType();

		$this->id = 'power_board_' . $paymentMethod->getId() . '_wallets_gateway';
		$this->title = $settings->getWidgetPaymentWalletTitle( $paymentMethod );
		$this->description = $settings->getWidgetPaymentWalletDescription( $paymentMethod );

		parent::__construct();
	}

	abstract protected function getWalletType(): WalletPaymentMethods;

	public function is_available() {
		return SettingsService::getInstance()->isEnabledPayment()
			&& SettingsService::getInstance()->isWalletEnabled( $this->getWalletType() );
	}

	public function payment_scripts() {
		return SettingsService::getInstance()->getWidgetScriptUrl();
	}

	public function process_payment( $order_id, $retry = true, $force_customer = false ) {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'process_payment' ) ) {
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				__( 'Error: Security check', 'power_board' )
			);
		}
		
		if ( ! empty( $_GET['afterpay_success'] ) && ( 'false' == $_GET['afterpay_success'] ) ) {
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				__( 'Error:', 'power_board' ) . ' ' . __( 'Afterpay returned a failure status.' ),
				'power_board'
			);
		}

		$order = wc_get_order( $order_id );
		$data = [];
		$chargeId = null;
		if ( ! empty( $_POST['payment_response'] ) ) {
			$data = json_decode( sanitize_text_field( $_POST['payment_response'] ), true );
		}

		if ( ( json_last_error() === JSON_ERROR_NONE ) && ! empty( $_POST['payment_response'] ) ) {
			$chargeId = $data['data']['id'];
		}

		$wallets = [];
		if ( ! empty( $_POST['wallets'] ) ) {
			$wallets = json_decode( sanitize_text_field( $_POST['wallets'] ), true );
			if ( null === $wallets ) {
				$wallets = [];
			}
		}

		$wallet = reset( $wallets );
		$isFraud = ! empty( $wallet['fraud'] ) && $wallet['fraud'];
		if ( $isFraud ) {
			update_option( 'power_board_fraud_' . (string) $order->get_id(), [] );
		}

		$loggerRepository = new LogRepository();
		if ( 'inreview' === $data['data']['status'] ) {
			$status = 'wc-pb-requested';
		} elseif (
			( 'pending' === $data['data']['status'] )
			|| ( ! empty( $_GET['direct_charge'] ) && ( 'true' == $_GET['direct_charge'] ) )
		) {
			$status = 'wc-pb-authorize';
		} else {
			$status = 'wc-pb-paid';
		}

		$order->set_status( $status );
		$order->payment_complete();
		$order->save();

		unset( $_SESSION[ AbstractWalletBlock::AFTERPAY_SESSION_KEY ] );

		update_post_meta( $order_id, 'power_board_charge_id', $chargeId );

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$data['data']['id'] ?? '',
			'Charge',
			$status,
			'Successful',
			'wc-pb-authorize' === $status ? LogRepository::DEFAULT : LogRepository::SUCCESS
		);

		return [ 
			'result' => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function webhook() {
	}
}
