<?php

namespace App\Filament\Resources\PGConnections\Pages;

use App\Filament\Resources\PGConnections\PGConnectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPGConnection extends ViewRecord
{
    protected static string $resource = PGConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
