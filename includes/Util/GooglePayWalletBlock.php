<?php

namespace WooPlugin\Util;

use WooPlugin\Abstracts\AbstractWalletBlock;
use WooPlugin\Enums\WalletPaymentMethods;
use WooPlugin\Services\Checkout\GooglePayWalletService;

final class GooglePayWalletBlock extends AbstractWalletBlock {
	public function getType(): WalletPaymentMethods {
		return WalletPaymentMethods::GOOGLE_PAY();
	}

	public function initialize() {
		$this->gateway = new GooglePayWalletService();
	}
}
