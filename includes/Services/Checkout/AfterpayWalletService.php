<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstracts\AbstractWalletPaymentService;
use Paydock\Enums\WalletPaymentMethods;

class AfterpayWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::AFTERPAY();
	}
}
