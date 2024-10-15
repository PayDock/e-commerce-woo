<?php

namespace WooPlugin\Services\Checkout;

use WooPlugin\Abstracts\AbstractAPMsPaymentService;
use WooPlugin\Enums\OtherPaymentMethods;

class AfterpayAPMsPaymentServiceService extends AbstractAPMsPaymentService {

	public function  get_title(){
        return trim($this->title) ? $this->title :  'Afterpay v1';
	}
	protected function getAPMsType(): OtherPaymentMethods {
		return OtherPaymentMethods::AFTERPAY();
	}
}
