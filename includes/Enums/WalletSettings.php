<?php

namespace Paydock\Enums;

enum WalletSettings
{
    case Enable;
    case GatewayId;
    case Fraud;
    case FraudServiceId;
    case DirectCharge;

    public function getInputType(): string
    {
        return match ($this) {
            self::GatewayId => 'text',
            self::Fraud,
            self::Enable,
            self::DirectCharge => 'checkbox',
            self::FraudServiceId => 'text',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Fraud,
            self::Enable => $this->name,
            self::GatewayId => 'Gateway ID',
            self::FraudServiceId => 'Fraud service ID',
            self::DirectCharge => 'Direct Charge',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::DirectCharge => 'Direct charge stands for authorization and capture in a single request',
            default => ''
        };
    }
}
