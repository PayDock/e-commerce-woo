<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractWalletPaymentService;
use PowerBoard\Enums\WalletPaymentMethods;

class PayPalWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
	}
	public function  get_title(){
        return trim($this->title) ? $this->title :  'PayPal';
	}
}
