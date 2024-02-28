<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

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
            self::TYPE => 'Connection to PowerBoard'
        };
    }

    public function getDescription(): string
    {
        return match ($this->name) {
            self::PUBLIC_KEY => 'Public Key from PowerBoard',
            self::SECRET_KEY => 'Secret Key from PowerBoard',
            self::ACCESS_KEY => 'Access Token from PowerBoard',
            default => ''
        };
    }

    public static function getHashed():array
    {
        return [
            self::PUBLIC_KEY()->name,
            self::SECRET_KEY()->name,
            self::ACCESS_KEY()->name,
        ];
    }
}
