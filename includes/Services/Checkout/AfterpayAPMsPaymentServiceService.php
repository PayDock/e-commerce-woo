<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstracts\AbstractAPMsPaymentService;
use Paydock\Enums\OtherPaymentMethods;

class AfterpayAPMsPaymentServiceService extends AbstractAPMsPaymentService {

	protected function getAPMsType(): OtherPaymentMethods {
		return OtherPaymentMethods::AFTERPAY();
	}
}
