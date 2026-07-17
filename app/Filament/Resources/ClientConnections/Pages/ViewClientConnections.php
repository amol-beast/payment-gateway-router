<?php

namespace App\Filament\Resources\ClientConnections\Pages;

use App\Filament\Resources\ClientConnections\ClientConnectionsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClientConnections extends ViewRecord
{
    protected static string $resource = ClientConnectionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
