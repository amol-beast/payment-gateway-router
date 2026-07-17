<?php

namespace App\Enums;

enum ConnectionType: string
{
    case TEST = "TEST";
    case PRODUCTION = "PRODUCTION";

    static public function labels(): array
    {
        return [
            "TEST" => self::TEST->value,
            "PRODUCTION" => self::PRODUCTION->value,
        ];
    }
}
