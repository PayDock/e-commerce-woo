<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractWalletBlock;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Services\Checkout\GooglePayWalletService;

final class GooglePayWalletBlock extends AbstractWalletBlock {
	public function getType(): WalletPaymentMethods {
		return WalletPaymentMethods::GOOGLE_PAY();
	}

	public function initialize() {
		$this->gateway = new GooglePayWalletService();
	}
}
