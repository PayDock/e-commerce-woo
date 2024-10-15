<?php

namespace WooPlugin\Services\Checkout;

use WooPlugin\Abstracts\AbstractWalletPaymentService;
use WooPlugin\Enums\WalletPaymentMethods;

class ApplePayWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::APPLE_PAY();
	}

	public function  get_title(){
        return trim($this->title) ? $this->title :  'Apple Pay';
	}
}
