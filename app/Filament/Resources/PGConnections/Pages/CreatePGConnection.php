<?php

namespace App\Filament\Resources\PGConnections\Pages;

use App\Filament\Resources\PGConnections\Concerns\ValidatesGatewayAttributes;
use App\Filament\Resources\PGConnections\PGConnectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePGConnection extends CreateRecord
{
    use ValidatesGatewayAttributes;

    protected static string $resource = PGConnectionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validateGatewayAttributes($data);

        return $data;
    }
}
