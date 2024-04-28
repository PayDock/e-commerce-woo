<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstracts\AbstractWalletPaymentService;
use Paydock\Enums\WalletPaymentMethods;

class PayPalWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
	}
}
