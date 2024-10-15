<?php

namespace WooPlugin\Services\Checkout;

use WooPlugin\Abstracts\AbstractAPMsPaymentService;
use WooPlugin\Enums\OtherPaymentMethods;

class ZipAPMsPaymentServiceService extends AbstractAPMsPaymentService {

	protected function getAPMsType(): OtherPaymentMethods {
		return OtherPaymentMethods::ZIPPAY();
	}
	public function  get_title(){
        return trim($this->title) ? $this->title : 'Zip';
	}
}
