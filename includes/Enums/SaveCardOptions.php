<?php

namespace PowerBoard\Enums;

use PowerBoard\Abstract\AbstractEnum;

class SaveCardOptions extends AbstractEnum
{
    protected const VAULT = 'Vault token';
    protected const WITH_GATEWAY = 'Customer with Gateway ID';
    protected const WITHOUT_GATEWAY = 'Customer without Gateway ID';

    public static function toArray(): array
    {
        $result = [];

        foreach (SaveCardOptions::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
