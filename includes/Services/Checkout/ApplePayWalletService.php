<?php

namespace Paydock\Services\Checkout;

use Paydock\Abstracts\AbstractWalletPaymentService;
use Paydock\Enums\WalletPaymentMethods;

class ApplePayWalletService extends AbstractWalletPaymentService
{
    protected function getWalletType(): WalletPaymentMethods
    {
        return WalletPaymentMethods::APPLE_PAY();
    }
}
