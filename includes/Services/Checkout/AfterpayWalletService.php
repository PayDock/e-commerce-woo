<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractWalletPaymentService;
use PowerBoard\Enums\WalletPaymentMethods;

class AfterpayWalletService extends AbstractWalletPaymentService
{
    protected function getWalletType(): WalletPaymentMethods
    {
        return WalletPaymentMethods::AFTERPAY();
    }
}
