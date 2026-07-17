<?php

namespace App\Filament\Resources\PGConnections\Pages;

use App\Filament\Resources\PGConnections\PGConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPGConnections extends ListRecords
{
    protected static string $resource = PGConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
