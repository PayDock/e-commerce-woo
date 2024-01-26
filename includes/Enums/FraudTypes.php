<?php

namespace Paydock\Enums;

enum FraudTypes: string
{
    case Disable = 'Disable';
    case Standalone = 'Standalone Fraud';
    case InBuild = 'In-built Fraud';


    public static function toArray(): array
    {
        $result = [];

        foreach (FraudTypes::cases() as $type) {
            $result[$type->name] = $type->value;
        }

        return $result;
    }
}
