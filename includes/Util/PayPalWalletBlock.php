<?php

namespace Paydock\Util;

use Paydock\Abstracts\AbstractWalletBlock;
use Paydock\Enums\WalletPaymentMethods;
use Paydock\Services\Checkout\PayPalWalletService;

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