<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class CardSettings extends AbstractEnum
{
    protected const ENABLE = 'ENABLE';

    protected const SUPPORTED_CARD_TYPES = 'SUPPORTED_CARD_TYPES';

    protected const GATEWAY_ID = 'GATEWAY_ID';
    protected const DS = 'DS';
    protected const DS_SERVICE_ID = 'DS_SERVICE_ID';
    protected const TYPE_EXCHANGE_OTT = 'TYPE_EXCHANGE_OTT';
    protected const FRAUD = 'FRAUD';
    protected const FRAUD_SERVICE_ID = 'FRAUD_SERVICE_ID';
    protected const DIRECT_CHARGE = 'DIRECT_CHARGE';
    protected const SAVE_CARD = 'SAVE_CARD';
    protected const SAVE_CARD_OPTION = 'SAVE_CARD_OPTION';

    public function getInputType(): string
    {
        return match ($this->name) {
            self::GATEWAY_ID => 'text',
            self::DS_SERVICE_ID => 'text',
            self::FRAUD_SERVICE_ID => 'text',
            self::ENABLE,
            self::DIRECT_CHARGE,
            self::SAVE_CARD => 'checkbox',
            self::SUPPORTED_CARD_TYPES => 'card_select',
            self::TYPE_EXCHANGE_OTT => 'select',
            default => 'select'
        };
    }

    public function getLabel(): string
    {
        return match ($this->name) {
            self::DS => '3DS',
            self::DS_SERVICE_ID => '3DS service ID',
            self::TYPE_EXCHANGE_OTT => '3DS flow',
            self::SUPPORTED_CARD_TYPES => 'Supported card schemes',
            default => ucfirst(strtolower(str_replace('_', ' ', $this->name)))
        };
    }

    public function getDescription(): string
    {
        return match ($this->name) {
            self::SAVE_CARD => 'Offer your customer to save the card permanently at Paydock for further usage',
            default => ''
        };
    }

    public function getDefault(): string
    {
        return match ($this->name) {
            self::DIRECT_CHARGE => 'yes',
            default => ''
        };
    }
}
