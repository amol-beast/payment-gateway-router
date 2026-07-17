<?php

namespace App\Filament\Resources\ClientConnections\Pages;

use App\Filament\Resources\ClientConnections\ClientConnectionsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientConnections extends ListRecords
{
    protected static string $resource = ClientConnectionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
