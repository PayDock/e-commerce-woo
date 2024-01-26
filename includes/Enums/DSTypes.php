<?php

namespace Paydock\Enums;

enum DSTypes: string
{
    case Disable = 'Disable';
    case Standalone = 'Standalone 3DS';
    case InBuild = 'In-built 3DS';


    public static function toArray(): array
    {
        $result = [];

        foreach (DSTypes::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
