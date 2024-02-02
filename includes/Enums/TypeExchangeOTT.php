<?php

namespace Paydock\Enums;

use Paydock\Abstract\AbstractEnum;

class TypeExchangeOTT extends AbstractEnum
{
    protected const PERMANENT_VAULT = 'permanent vault';
    protected const SESSION_VAULT = 'session vault';
    protected const WITHOUT_VAULT = 'pre-auth with 3DS';

    public static function toArray(): array
    {
        $result = [];
        foreach (TypeExchangeOTT::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
