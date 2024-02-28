<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class APMsSettings extends AbstractEnum
{
    protected const ENABLE = 'ENABLE';
    protected const GATEWAY_ID = 'GATEWAY_ID';
    protected const FRAUD = 'FRAUD';
    protected const FRAUD_SERVICE_ID = 'FRAUD_SERVICE_ID';
    protected const DIRECT_CHARGE = 'DIRECT_CHARGE';
    protected const SAVE_CARD = 'SAVE_CARD';
    protected const SAVE_CARD_OPTION = 'SAVE_CARD_OPTION';

    public function getInputType(): string
    {
        return match ($this->name) {
            self::GATEWAY_ID => 'text',
            self::ENABLE,
            self::FRAUD => 'checkbox',
            self::FRAUD_SERVICE_ID => 'text',
            self::DIRECT_CHARGE,
            self::SAVE_CARD => 'checkbox',
            self::SAVE_CARD_OPTION => 'select',
        };
    }

    public function getLabel(): string
    {
        return match ($this->name) {
            self::FRAUD,
            self::ENABLE => ucfirst(strtolower($this->name)),
            self::GATEWAY_ID => 'Gateway ID',
            self::DIRECT_CHARGE => 'Direct Charge',
            self::FRAUD_SERVICE_ID => 'Fraud service ID',
            self::SAVE_CARD => 'Save card',
            self::SAVE_CARD_OPTION => 'Save card option',
        };
    }

    public function getDescription(): string
    {
        return match ($this->name) {
            self::DIRECT_CHARGE => 'Direct charge stands for authorization and capture in a single request',
            self::SAVE_CARD => 'Offer your customer the option to save the card information permanently at PowerBoard for '
                . 'further usage',

            default => ''
        };
    }
}
