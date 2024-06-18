<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractAPMsPaymentService;
use PowerBoard\Enums\OtherPaymentMethods;

class AfterpayAPMsPaymentServiceService extends AbstractAPMsPaymentService {

	public function  get_title(){
        return trim($this->title) ? $this->title :  'Afterpay v1';
	}
	protected function getAPMsType(): OtherPaymentMethods {
		return OtherPaymentMethods::AFTERPAY();
	}
}
