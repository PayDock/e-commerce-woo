<?php

namespace Paydock\Enums;

enum BankAccountSettings
{
    case Enable;
    case GatewayId;
    case SaveCard;
    case SaveCardOption;

    public function getLabel(): string
    {
        return match ($this) {
            self::Enable => $this->name,
            self::GatewayId => 'Gateway ID',
            self::SaveCard => 'Save bank account',
            self::SaveCardOption => 'Save bank account option',
        };
    }

    public function getInputType(): string
    {
        return match ($this) {
            self::GatewayId => 'text',
            self::Enable,
            self::SaveCard => 'checkbox',
            default => 'select'
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::SaveCard => 'Offer your customer to save the bank account information permanently at Paydock for'
                . ' further usage',
            default => ''
        };
    }
}
