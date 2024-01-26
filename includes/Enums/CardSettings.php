<?php

namespace Paydock\Enums;

enum CardSettings
{
    case Enable;
    case GatewayId;
    case DS;
    case DSServiceId;
    case Fraud;
    case FraudServiceId;
    case DirectCharge;
    case SaveCard;
    case SaveCardOption;

    case SupportedCardTypes;

    public function getInputType(): string
    {
        return match ($this) {
            self::GatewayId => 'text',
            self::DSServiceId => 'text',
            self::FraudServiceId => 'text',
            self::Enable,
            self::DirectCharge,
            self::SaveCard => 'checkbox',
            self::SupportedCardTypes => 'card_select',
            default => 'select'
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Fraud,
            self::Enable => $this->name,
            self::GatewayId => 'Gateway ID',
            self::DS => '3DS',
            self::DSServiceId => '3DS service ID',
            self::FraudServiceId => 'Fraud service ID',
            self::DirectCharge => 'Direct Charge',
            self::SaveCard => 'Save card',
            self::SaveCardOption => 'Save card option',
            self::SupportedCardTypes => 'Supported card types',
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
