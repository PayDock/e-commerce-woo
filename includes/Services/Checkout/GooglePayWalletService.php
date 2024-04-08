<?php

namespace PowerBoard\Services\Checkout;

use PowerBoard\Abstracts\AbstractWalletPaymentService;
use PowerBoard\Enums\WalletPaymentMethods;

class GooglePayWalletService extends AbstractWalletPaymentService
{
    protected function getWalletType(): WalletPaymentMethods
    {
        return WalletPaymentMethods::GOOGLE_PAY();
    }
}
