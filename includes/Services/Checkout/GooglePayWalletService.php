<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstracts\AbstractWalletPaymentService;
use Paydock\Enums\WalletPaymentMethods;

class GooglePayWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::GOOGLE_PAY();
	}
}
