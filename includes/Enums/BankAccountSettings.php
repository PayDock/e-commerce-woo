<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class BankAccountSettings extends AbstractEnum
{
    protected const ENABLE = 'ENABLE';
    protected const GATEWAY_ID = 'GATEWAY_ID';
    protected const SAVE_CARD = 'SAVE_CARD';
    protected const SAVE_CARD_OPTION = 'SAVE_CARD_OPTION';

    public function getLabel(): string
    {
        return match ($this->name) {
            self::ENABLE => ucfirst(strtolower($this->name)),
            self::GATEWAY_ID => 'Gateway ID',
            self::SAVE_CARD => 'Save bank account',
            self::SAVE_CARD_OPTION => 'Save bank account option',
        };
    }

    public function getInputType(): string
    {
        return match ($this->name) {
            self::GATEWAY_ID => 'text',
            self::ENABLE,
            self::SAVE_CARD => 'checkbox',
            default => 'select'
        };
    }

    public function getDescription(): string
    {
        return match ($this->name) {
            self::SAVE_CARD => 'Offer your customer the option to permanently save the bank account information at '
                . 'PowerBoard for further usage',
            default => ''
        };
    }
}
