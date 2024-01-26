<?php

namespace Paydock\Enums;

enum SettingGroups
{
    case Credentials;
    case Card;
    case BankAccount;
    case Wallets;
    case APMs;

    public function getLabel(): string
    {
        return match ($this) {
            self::Card => 'Cards',
            self::Wallets, self::APMs => $this->name . ':',
            self::BankAccount => 'Bank account:',
            self::Credentials => 'API Credential:',
        };
    }
}
