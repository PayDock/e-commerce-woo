<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class SettingGroups extends AbstractEnum
{
    protected const CREDENTIALS = 'CREDENTIALS';
    protected const CARD = 'CARD';
    protected const BANK_ACCOUNT = 'BANK_ACCOUNT';
    protected const WALLETS = 'WALLETS';
    protected const A_P_M_S = 'A_P_M_S';

    public function getLabel(): string
    {
        return match ($this->name) {
            self::CARD => 'Cards',
            self::WALLETS => 'Wallets:',
            self::A_P_M_S => 'APMs:',
            self::BANK_ACCOUNT => 'Bank account:',
            self::CREDENTIALS => 'API Credential:',
        };
    }
}
