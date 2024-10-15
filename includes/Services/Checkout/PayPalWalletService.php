<?php

namespace WooPlugin\Services\Checkout;

use WooPlugin\Abstracts\AbstractWalletPaymentService;
use WooPlugin\Enums\WalletPaymentMethods;

class PayPalWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
	}
	public function  get_title(){
        return trim($this->title) ? $this->title :  'PayPal';
	}
}
