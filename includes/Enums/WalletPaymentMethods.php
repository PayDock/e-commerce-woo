<?php

namespace Paydock\Enums;

enum WalletPaymentMethods
{
    case ApplePay;
    case GooglePay;
    case PayPalSmartButton;
    case Afterpay;

    public function getLabel(): string
    {
        return match ($this) {
            self::ApplePay => 'Apple Pay',
            self::GooglePay => 'Google Pay',
            self::PayPalSmartButton => 'PayPal Smart Button',
            self::Afterpay => 'Afterpay v2',
        };
    }
}
