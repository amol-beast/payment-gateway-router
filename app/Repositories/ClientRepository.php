<?php

namespace App\Repositories;
use App\Models\Client;
use Illuminate\Support\Facades\Cache;
class ClientRepository
{
    public function getClient($clientId)
    {
        return Cache::remember("client.{$clientId}", 300, function () use ($clientId) {
            return Client::where("id", $clientId)->first();
        });
    }
}
