<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractAPMsBlock;
use PowerBoard\Enums\OtherPaymentMethods;
use PowerBoard\Services\Checkout\ZipAPMsPaymentServiceService;

class ZipAPMsBlock extends AbstractAPMsBlock {

	public function getType(): OtherPaymentMethods {
		return OtherPaymentMethods::ZIPPAY();
	}

	public function initialize() {
		$this->gateway = new ZipAPMsPaymentServiceService();
	}
}
