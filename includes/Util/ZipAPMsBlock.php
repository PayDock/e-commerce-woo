<?php

namespace WooPlugin\Util;

use WooPlugin\Abstracts\AbstractAPMsBlock;
use WooPlugin\Enums\OtherPaymentMethods;
use WooPlugin\Services\Checkout\ZipAPMsPaymentServiceService;

class ZipAPMsBlock extends AbstractAPMsBlock {

	public function getType(): OtherPaymentMethods {
		return OtherPaymentMethods::ZIPPAY();
	}

	public function initialize() {
		$this->gateway = new ZipAPMsPaymentServiceService();
	}
}
