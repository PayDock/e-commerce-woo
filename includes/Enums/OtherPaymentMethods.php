<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class OtherPaymentMethods extends AbstractEnum
{
    protected const AFTERPAY = 'AFTERPAY';
    protected const ZIPPAY = 'ZIPPAY';

    public function getLabel(): string
    {
        return match ($this->name) {
            self::ZIPPAY => 'Zippay',
            self::AFTERPAY => 'Afterpay v1',
        };
    }
}
