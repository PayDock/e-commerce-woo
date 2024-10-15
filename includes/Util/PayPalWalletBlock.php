<?php

namespace WooPlugin\Util;

use WooPlugin\Abstracts\AbstractWalletBlock;
use WooPlugin\Enums\WalletPaymentMethods;
use WooPlugin\Services\Checkout\PayPalWalletService;

final class PayPalWalletBlock extends AbstractWalletBlock {
	public function getType(): WalletPaymentMethods {
		return WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
	}

	public function initialize() {
		$this->gateway = new PayPalWalletService();
	}
}
