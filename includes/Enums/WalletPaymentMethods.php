<?php

namespace Paydock\Enums;

enum WalletPaymentMethods
{
    case ApplePay;
    case GooglePay;
    case PayPalSmartButton;

    public function getLabel(): string
    {
        return match ($this) {
            self::ApplePay => 'Apple Pay',
            self::GooglePay => 'Google Pay',
            self::PayPalSmartButton => 'PayPal Smart Button',
        };
    }
}
