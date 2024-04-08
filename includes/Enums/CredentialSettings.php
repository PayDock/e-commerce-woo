<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstracts\AbstractEnum;

class CredentialSettings extends AbstractEnum
{
    protected const SANDBOX = 'SANDBOX';
    protected const TYPE = 'TYPE';
    protected const PUBLIC_KEY = 'PUBLIC_KEY';
    protected const SECRET_KEY = 'SECRET_KEY';
    protected const ACCESS_KEY = 'ACCESS_KEY';
    protected const WIDGET_KEY = 'WIDGET_KEY';

    public static function getHashed(): array
    {
        return [
            self::PUBLIC_KEY()->name,
            self::SECRET_KEY()->name,
            self::ACCESS_KEY()->name,
            self::WIDGET_KEY()->name,
        ];
    }

    public function getInputType(): string
    {
        switch ($this->name) {
            case self::PUBLIC_KEY:
            case self::SECRET_KEY:
            case self::ACCESS_KEY:
            case self::WIDGET_KEY:
                return 'password';
            case self::SANDBOX:
                return 'checkbox';
            case self::TYPE:
                return 'select';
            default:
                return '';
        }
    }

    public function getLabel(): string
    {
        switch ($this->name) {
            case self::PUBLIC_KEY:
                return 'Public Key';
            case self::SECRET_KEY:
                return 'Secret Key';
            case self::ACCESS_KEY:
                return 'API Access Token';
            case self::WIDGET_KEY:
                return 'Widget Access Token';
            case self::SANDBOX:
                return 'Sandbox';
            case self::TYPE:
                return 'Connection to PowerBoard';
            default:
                return '';
        }
    }

    public function getDescription(): string
    {
        switch ($this->name) {
            case self::PUBLIC_KEY:
                return 'Enter the API public key for the live environment.
                        This key is used for authentication to ensure secure communication
                        with the Payment Gateway.';
            case self::SECRET_KEY:
                return 'Enter the API secret key for the live environment.
                        This key is used for authentication to ensure secure communication
                        with the Payment Gateway.';
            case self::ACCESS_KEY:
                return 'Enter the API access token for authentication.
                        This token is used to authorize transactions.
                        It needs to be filled in only if API Public Key & API Secret Key is not entered.';
            case self::WIDGET_KEY:
                return 'Enter the widget access token for authentication.
                        This token is used to authorize widget transactions.
                        It needs to be filled in only if API Public Key & API Secret Key is not entered.';
            default:
                return '';
        }
    }
}
