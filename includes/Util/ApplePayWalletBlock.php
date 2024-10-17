<?php

namespace WooPlugin\Util;

use WooPlugin\Abstracts\AbstractWalletBlock;
use WooPlugin\Enums\WalletPaymentMethods;
use WooPlugin\Services\Checkout\ApplePayWalletService;

final class ApplePayWalletBlock extends AbstractWalletBlock {
	public function getType(): WalletPaymentMethods {
		return WalletPaymentMethods::APPLE_PAY();
	}

	public function initialize() {
		$this->gateway = new ApplePayWalletService();
	}
}
