<?php

namespace WooPlugin\Abstracts;

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;
use WooPlugin\Enums\OrderListColumns;
use WooPlugin\Enums\WalletPaymentMethods;
use WooPlugin\Repositories\LogRepository;
use WooPlugin\Services\OrderService;
use WooPlugin\Services\SettingsService;

abstract class AbstractWalletPaymentService extends AbstractPaymentService {
	public function __construct() {
		$settings      = SettingsService::getInstance();
		$paymentMethod = $this->getWalletType();
		$id            = $paymentMethod->getId();

		$this->id          ='plugin_' . $paymentMethod->getId() . '_wallets_gateway';
		$this->title       = $settings->getWidgetPaymentWalletTitle( $paymentMethod );
		$this->description = $settings->getWidgetPaymentWalletDescription( $paymentMethod );

		parent::__construct();
	}

	abstract protected function getWalletType(): WalletPaymentMethods;

	public function is_available() {
		if ( is_checkout() ) {
			$paymentMethod = $this->getWalletType();
			$this->title   = '<img src="/wp-content/plugins/' . PLUGIN_TEXT_DOMAIN . '/assets/images/icons/' .
			                 $paymentMethod->getId() .
			                 '.png" height="25" class="plugin-payment-method-label-icon ' .
			                 $paymentMethod->getId() .
			                 '">' .
			                 SettingsService::getInstance()->getWidgetPaymentWalletTitle( $paymentMethod );
		}

		return SettingsService::getInstance()->isEnabledPayment()
		       && SettingsService::getInstance()->isWalletEnabled( $this->getWalletType() );
	}

	public function process_payment( $order_id, $retry = true, $force_customer = false ) {
		$wpNonce = ! empty( $_POST['_wpnonce'] ) ? sanitize_text_field( $_POST['_wpnonce'] ) : null;
		if ( ! wp_verify_nonce( $wpNonce, 'process_payment' ) ) {
			throw new RouteException(
				'woocommerce_rest_checkout_process_payment_error',
				esc_html( __( 'Error: Security check', PLUGIN_TEXT_DOMAIN ) )
			);
		}

		$order    = wc_get_order( $order_id );
		$data     = [];
		$jsonData = '';
		$chargeId = null;
		if ( ! empty( $_POST['payment_response'] ) ) {
			$jsonData = $_POST['payment_response'];
		} elseif ( ! empty( $_POST['payment_source'] ) && is_array( $_POST['payment_source'] ) ) {
			$jsonData = array_filter( $_POST['payment_source'] );
			$jsonData = reset( $jsonData );
			$jsonData = str_replace( '\\"', '"', $jsonData );
		}

		if ( ! empty( $jsonData ) ) {
			$data = json_decode( sanitize_text_field( $jsonData ), true );
		}

		if ( ( json_last_error() === JSON_ERROR_NONE ) && ! empty( $data ) ) {
			$chargeId = $data['data']['id'] ?? $data['data']['data']['id'];
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
			update_option( PLUGIN_PREFIX . '_fraud_' . (string) $order->get_id(), [] );
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
			$order->update_meta_data( 'pb_directly_charged', 1 );
		}
		$order->update_meta_data( PLUGIN_PREFIX . '_charge_id', $chargeId );
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

		$order->set_payment_method_title( SettingsService::getInstance()->getWidgetPaymentWalletTitle( $this->getWalletType() ) );
		$order->save();

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	public function webhook() {
	}

	public function get_payment_method_data(): array {
		$settings = SettingsService::getInstance();
		$payment  = $this->getWalletType();

		$result = [
			'title'       => $settings->getWidgetPaymentWalletTitle( $payment ),
			'description' => $settings->getWidgetPaymentWalletDescription( $payment ),
			'publicKey'   => $settings->getPublicKey(),
			'isSandbox'   => $settings->isSandbox(),
			'styles'      => $settings->getWidgetStyles(),
		];

		$result['wallets'][ strtolower( $payment->name ) ] = [
			'gatewayId'      => $settings->getWalletGatewayId( $payment ),
			'fraud'          => $settings->isWalletFraud( $payment ),
			'fraudServiceId' => $settings->getWalletFraudServiceId( $payment ),
			'directCharge'   => $settings->getWalletFraudServiceId( $payment ),
		];

		if ( WalletPaymentMethods::PAY_PAL_SMART_BUTTON()->name === $payment->name ) {
			$result[ strtolower( $payment->name ) ]['payLater'] = $settings->isPayPallSmartButtonPayLater();
		}

		return $result;
	}

}
