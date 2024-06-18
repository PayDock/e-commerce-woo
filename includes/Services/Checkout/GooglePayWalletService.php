<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractWalletPaymentService;
use PowerBoard\Enums\WalletPaymentMethods;

class GooglePayWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::GOOGLE_PAY();
	}
	public function  get_title(){
        return trim($this->title) ? $this->title : 'Google Pay';
	}
}
