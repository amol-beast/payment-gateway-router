<?php

namespace App\Filament\Resources\ClientConnections\Pages;

use App\Filament\Resources\ClientConnections\ClientConnectionsResource;
use App\Filament\Resources\ClientConnections\Concerns\ValidatesClientConnection;
use Filament\Resources\Pages\CreateRecord;

class CreateClientConnections extends CreateRecord
{
    use ValidatesClientConnection;

    protected static string $resource = ClientConnectionsResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateClientConnection($data);

        return $data;
    }
}
