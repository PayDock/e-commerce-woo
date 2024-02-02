<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class WalletPaymentMethods extends AbstractEnum
{
    protected const APPLE_PAY = 'APPLE_PAY';
    protected const GOOGLE_PAY = 'GOOGLE_PAY';
    protected const PAY_PAL_SMART_BUTTON = 'PAY_PAL_SMART_BUTTON';
    protected const AFTERPAY = 'AFTERPAY';

    public function getLabel(): string
    {
        return match ($this->name) {
            self::APPLE_PAY => 'Apple Pay',
            self::GOOGLE_PAY => 'Google Pay',
            self::PAY_PAL_SMART_BUTTON => 'PayPal Smart Button',
            self::AFTERPAY => 'Afterpay v2',
        };
    }
}
