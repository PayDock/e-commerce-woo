<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class TypeExchangeOTT extends AbstractEnum
{
    protected const PERMANENT_VAULT = 'With vault';
    protected const SESSION_VAULT = 'With OTT';
//    protected const WITHOUT_VAULT = 'pre-auth with 3DS';

    public static function toArray(): array
    {
        $result = [];
        foreach (TypeExchangeOTT::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
