<?php

namespace Paydock\Enums;

enum CredentialSettings
{
    case Sandbox;
    case Type;
    case PublicKey;
    case SecretKey;
    case AccessKey;

    public function getInputType(): string
    {
        return match ($this) {
            self::PublicKey => 'password',
            self::SecretKey => 'password',
            self::AccessKey => 'password',
            self::Sandbox => 'checkbox',
            self::Type => 'select'
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PublicKey => 'Public Key',
            self::SecretKey => 'Secret Key',
            self::AccessKey => 'Access Token',
            self::Sandbox => 'Sandbox',
            self::Type => 'Connection to Paydock'
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::PublicKey => 'Public Key from Paydock partner panel.',
            self::SecretKey => 'Secret Key from Paydock partner panel.',
            self::AccessKey => 'Access Token from Paydock partner panel.',
            default => ''
        };
    }
}
