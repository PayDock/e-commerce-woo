<?php

namespace Paydock\Enums;

enum OtherPaymentMethods
{
    case Afterpay;
    case PayPal;
    case Zippay;

    public function getLabel(): string
    {
        return match ($this) {
            self::Zippay => 'Zippay',
            self::PayPal => 'PayPal',
            self::Afterpay => 'Afterpay v1',
        };
    }
}
