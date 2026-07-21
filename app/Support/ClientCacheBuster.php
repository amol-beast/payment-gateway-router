<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ClientCacheBuster
{
    public static function forgetClient(int|string $id): void
    {
        Cache::forget("client.{$id}");
    }

    public static function forgetClientPgConnection(?string $clientId): void
    {
        if (! $clientId) {
            return;
        }

        Cache::forget("clientPGConnection.{$clientId}.0");
        Cache::forget("clientPGConnection.{$clientId}.1");
    }
}
