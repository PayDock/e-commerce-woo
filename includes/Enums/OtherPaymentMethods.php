<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

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
