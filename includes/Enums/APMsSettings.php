<?php

namespace Paydock\Enums;

enum APMsSettings
{
    case Enable;
    case GatewayId;
    case Fraud;
    case FraudServiceId;
    case DirectCharge;
    case SaveCard;
    case SaveCardOption;

    public function getInputType(): string
    {
        return match ($this) {
            self::GatewayId => 'text',
            self::Enable,
            self::Fraud => 'checkbox',
            self::FraudServiceId => 'text',
            self::DirectCharge,
            self::SaveCard => 'checkbox',
            self::SaveCardOption => 'select',

        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Fraud,
            self::Enable => $this->name,
            self::GatewayId => 'Gateway ID',
            self::DirectCharge => 'Direct Charge',
            self::FraudServiceId => 'Fraud service ID',
            self::SaveCard => 'Save card',
            self::SaveCardOption => 'Save card option',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::DirectCharge => 'Direct charge stands for authorization and capture in a single request',
            self::SaveCard => 'Offer your customer to save the card permanently at Paydock for further usage',

            default => ''
        };
    }
}
