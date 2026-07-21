<?php

namespace App\Filament\Resources\ClientConnections\Pages;

use App\Filament\Resources\ClientConnections\ClientConnectionsResource;
use App\Filament\Resources\ClientConnections\Concerns\ValidatesClientConnection;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditClientConnections extends EditRecord
{
    use ValidatesClientConnection;

    protected static string $resource = ClientConnectionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateClientConnection($data, (int) $this->getRecord()->getKey());

        return $data;
    }
}
