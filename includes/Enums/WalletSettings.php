<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class WalletSettings extends AbstractEnum
{
    protected const ENABLE = 'ENABLE';
    protected const GATEWAY_ID = 'GATEWAY_ID';
    protected const FRAUD = 'FRAUD';
    protected const FRAUD_SERVICE_ID = 'FRAUD_SERVICE_ID';
    protected const DIRECT_CHARGE = 'DIRECT_CHARGE';

    public function getInputType(): string
    {
        return match ($this->name) {
            self::GATEWAY_ID => 'text',
            self::FRAUD,
            self::ENABLE,
            self::DIRECT_CHARGE => 'checkbox',
            self::FRAUD_SERVICE_ID => 'text',
        };
    }

    public function getLabel(): string
    {
        return ucfirst(strtolower(str_replace('_', ' ', $this->name)));
    }

    public function getDescription(): string
    {
        return match ($this->name) {
            self::DIRECT_CHARGE => 'Direct charge stands for authorization and capture in a single request',
            default => ''
        };
    }
}
