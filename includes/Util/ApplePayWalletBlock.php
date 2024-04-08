<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractWalletBlock;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Services\Checkout\ApplePayWalletService;

final class ApplePayWalletBlock extends AbstractWalletBlock
{
    public function getType(): WalletPaymentMethods
    {
        return WalletPaymentMethods::APPLE_PAY();
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $this->gateway = new ApplePayWalletService();
    }
}
