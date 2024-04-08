<?php

namespace PowerBoard\Util;

use PowerBoard\Abstracts\AbstractWalletBlock;
use PowerBoard\Enums\WalletPaymentMethods;
use PowerBoard\Services\Checkout\PayPalWalletService;

final class PayPalWalletBlock extends AbstractWalletBlock
{
    public function getType(): WalletPaymentMethods
    {
        return WalletPaymentMethods::PAY_PAL_SMART_BUTTON();
    }

    /**
     * @inheritDoc
     */
    public function initialize()
    {
        $this->gateway = new PayPalWalletService();
    }
}
