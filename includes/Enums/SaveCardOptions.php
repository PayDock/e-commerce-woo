<?php

namespace Paydock\Enums;

enum SaveCardOptions: string
{
    case Vault = 'Vault token';
    case WithGateWay = 'Customer with Gateway ID';
    case WithoutGateway = 'Customer without Gateway ID';

    public static function toArray(): array
    {
        $result = [];

        foreach (SaveCardOptions::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
