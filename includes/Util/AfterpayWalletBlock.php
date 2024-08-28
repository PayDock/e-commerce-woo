<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractWalletBlock;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Services\Checkout\AfterpayWalletService;

final class AfterpayWalletBlock extends AbstractWalletBlock {
	public function getType(): WalletPaymentMethods {
		return WalletPaymentMethods::AFTERPAY();
	}

	public function initialize() {
		$this->gateway = new AfterpayWalletService();
	}
}
