<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractAPMsBlock;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Services\Checkout\AfterpayAPMsPaymentServiceService;

class AfterpayAPMsBlock extends AbstractAPMsBlock {

	public function getType(): OtherPaymentMethods {
		return OtherPaymentMethods::AFTERPAY();
	}

	public function initialize() {
		$this->gateway = new AfterpayAPMsPaymentServiceService();
	}
}
