<?php

namespace WooPlugin\Util;

use WooPlugin\Abstracts\AbstractWalletBlock;
use WooPlugin\Enums\WalletPaymentMethods;
use WooPlugin\Services\Checkout\AfterpayWalletService;

final class AfterpayWalletBlock extends AbstractWalletBlock {
	public function getType(): WalletPaymentMethods {
		return WalletPaymentMethods::AFTERPAY();
	}

	public function initialize() {
		$this->gateway = new AfterpayWalletService();
	}
}
