<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class OtherPaymentMethods extends AbstractEnum
{
    protected const AFTERPAY = 'AFTERPAY';
    protected const PAY_PAL = 'PAY_PAL';
    protected const ZIPPAY = 'ZIPPAY';

    public function getLabel(): string
    {
        return match ($this->name) {
            self::ZIPPAY => 'Zippay',
            self::PAY_PAL => 'PayPal',
            self::AFTERPAY => 'Afterpay v1',
        };
    }
}
