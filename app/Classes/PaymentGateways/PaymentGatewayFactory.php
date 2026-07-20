<?php

namespace App\Classes\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;

class PaymentGatewayFactory
{
    public static function create(array $connection): PaymentGatewayInterface
    {
        return match ($connection['pg_class']) {
            'ICICI' => new ICICI($connection['attributes']),
            default => throw new \Exception('Invalid payment gateway type.'),
        };
    }
    public static function createEmpty($pg_class): PaymentGatewayInterface
    {
        return match ($pg_class) {
            'ICICI' => new ICICI([]),
            default => throw new \Exception('Invalid payment gateway type.'),
        };
    }
}
