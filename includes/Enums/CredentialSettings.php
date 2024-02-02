<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class CredentialSettings extends AbstractEnum
{
    protected const SANDBOX = 'SANDBOX';
    protected const TYPE = 'TYPE';
    protected const PUBLIC_KEY = 'PUBLIC_KEY';
    protected const SECRET_KEY = 'SECRET_KEY';
    protected const ACCESS_KEY = 'ACCESS_KEY';

    public function getInputType(): string
    {
        return match ($this->name) {
            self::PUBLIC_KEY => 'password',
            self::SECRET_KEY => 'password',
            self::ACCESS_KEY => 'password',
            self::SANDBOX => 'checkbox',
            self::TYPE => 'select'
        };
    }

    public function getLabel(): string
    {
        return match ($this->name) {
            self::PUBLIC_KEY => 'Public Key',
            self::SECRET_KEY => 'Secret Key',
            self::ACCESS_KEY => 'Access Token',
            self::SANDBOX => 'Sandbox',
            self::TYPE => 'Connection to Paydock'
        };
    }

    public function getDescription(): string
    {
        return match ($this->name) {
            self::PUBLIC_KEY => 'Public Key from Paydock/Powerboard',
            self::SECRET_KEY => 'Secret Key from Paydock/Powerboard',
            self::SANDBOX => 'Access Token from Paydock/Powerboard',
            default => ''
        };
    }
}
