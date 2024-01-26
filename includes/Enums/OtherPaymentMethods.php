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
            self::Afterpay => 'Afterpay v1',
            self::Zippay => 'Secret Key',
            self::PayPal => 'Access Token',
        };
    }

}
