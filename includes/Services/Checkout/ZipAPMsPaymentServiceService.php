<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractAPMsPaymentService;
use PowerBoard\Enums\OtherPaymentMethods;

class ZipAPMsPaymentServiceService extends AbstractAPMsPaymentService {

	protected function getAPMsType(): OtherPaymentMethods {
		return OtherPaymentMethods::ZIPPAY();
	}
	public function  get_title(){
        return trim($this->title) ? $this->title : 'Zip';
	}
}
