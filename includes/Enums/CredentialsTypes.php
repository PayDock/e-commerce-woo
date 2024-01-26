<?php

namespace Paydock\Enums;

enum CredentialsTypes: string
{
    case Credentials = 'Public & Secret Keys';
    case AccessKey = 'Access Token';

    public static function toArray(): array
    {
        $result = [];

        foreach (CredentialsTypes::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
