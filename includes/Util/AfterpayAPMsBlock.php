<?php

namespace WooPlugin\Util;

use WooPlugin\Abstracts\AbstractAPMsBlock;
use WooPlugin\Enums\OtherPaymentMethods;
use WooPlugin\Services\Checkout\AfterpayAPMsPaymentServiceService;

class AfterpayAPMsBlock extends AbstractAPMsBlock {

	public function getType(): OtherPaymentMethods {
		return OtherPaymentMethods::AFTERPAY();
	}

	public function initialize() {
		$this->gateway = new AfterpayAPMsPaymentServiceService();
	}
}
