<?php

namespace PowerBoard\Services\Checkout;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use PowerBoard\Abstracts\AbstractWalletPaymentService;
use PowerBoard\Enums\OrderListColumns;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Repositories\LogRepository;

class AfterpayWalletService extends AbstractWalletPaymentService {

	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::AFTERPAY();
	}

	public function get_title() {
		return trim( $this->title ) ? $this->title : 'Afterpay v2';
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

		$order->set_status( 'wc-pending' );
		$order->update_meta_data( 'power_board_charge_id', $chargeId );
		$order->update_meta_data( OrderListColumns::PAYMENT_SOURCE_TYPE()->getKey(), $this->getWalletType()->getLabel() );
		$order->save();

		$loggerRepository->createLogRecord(
			$data['data']['id'] ?? '',
			'Charge',
			'wc-pending',
			'Successful'
		);

		return [
			'result' => 'success'
		];
	}
}
