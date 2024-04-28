<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractWalletPaymentService;
use PowerBoard\Enums\WalletPaymentMethods;

class ApplePayWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::APPLE_PAY();
	}
}
