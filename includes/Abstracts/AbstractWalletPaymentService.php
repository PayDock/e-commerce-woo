<?php

namespace PowerBoard\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use PowerBoard\Controllers\Admin\WidgetController;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Repositories\LogRepository;
use PowerBoard\Services\OrderService;
use PowerBoard\Services\SettingsService;

abstract class AbstractWalletPaymentService extends AbstractPaymentService {
	public function __construct() {
		$settings      = SettingsService::getInstance();
		$paymentMethod = $this->getWalletType();

		$this->id          = 'power_board_' . $paymentMethod->getId() . '_wallets_gateway';
		$this->title       = $settings->getWidgetPaymentWalletTitle( $paymentMethod );
		$this->description = $settings->getWidgetPaymentWalletDescription( $paymentMethod );

		add_action( 'wp_ajax_nopriv_create_wallet_charge', [ $this, 'create_wallet_charge' ] );
		add_action( 'wp_ajax_create_wallet_charge', [ $this, 'create_wallet_charge' ] );

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
		$order    = wc_get_order( $order_id );
		$data     = [];
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

		$wallet  = reset( $wallets );
		$isFraud = ! empty( $wallet['fraud'] ) && $wallet['fraud'];
		if ( $isFraud ) {
			$order->update_meta_data( 'power_board_fraud', [] );
			$order->save();
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

		OrderService::updateStatus( $order_id, $status );
		if ( ! in_array( $status, [ 'wc-pb-authorize' ] ) ) {
			$order->payment_complete();
			$order->update_meta_data( 'capture_amount', $order->get_total() );
			$order->update_meta_data( 'pb_directly_charged', 1 );
		}
		$order->update_meta_data( 'power_board_charge_id', $chargeId );
		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), $this->getWalletType()->getLabel() );
		$order->save();

		WC()->cart->empty_cart();

		$loggerRepository->createLogRecord(
			$data['data']['id'] ?? '',
			'Charge',
			$status,
			'Successful',
			'wc-pb-authorize' === $status ? LogRepository::DEFAULT : LogRepository::SUCCESS
		);

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function create_wallet_charge(): void {
		$wpNonce = ! empty( $_POST['data']['_wpnonce'] ) ? sanitize_text_field( $_POST['data']['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'create_wallet_charge' ) ) {
			die( 'Security check' );
		}

		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower(
			                                                     sanitize_text_field( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		                                                     ) == 'xmlhttprequest' ) {
			$widgetController = new WidgetController( );
			$createWalletChargeResult = $widgetController->createWalletCharge($_POST['data']);

			if ( $createWalletChargeResult['error'] ) {
				wp_send_json_error( [ 'error' => $createWalletChargeResult['error'] ] );
			} else {
				wp_send_json_success([
				 'token' => $createWalletChargeResult['resource']['data']['token'],
				 'county' => $createWalletChargeResult['county'],
				 'pay_later' => ! empty( $createWalletChargeResult['pay_later'] ) ? $createWalletChargeResult['pay_later'] : null,
				 ]);
			}
		} else {
			$referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : '/';
			header( 'Location: ' . $referer );
		}

		die();
	}

	public function webhook() {
	}
}
