<?php

namespace WooPlugin\Services\Checkout;

use WooPlugin\Abstracts\AbstractWalletPaymentService;
use WooPlugin\Enums\WalletPaymentMethods;

class GooglePayWalletService extends AbstractWalletPaymentService {
	protected function getWalletType(): WalletPaymentMethods {
		return WalletPaymentMethods::GOOGLE_PAY();
	}
	public function  get_title(){
        return trim($this->title) ? $this->title : 'Google Pay';
	}
}
