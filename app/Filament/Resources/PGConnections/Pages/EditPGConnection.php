<?php

namespace App\Filament\Resources\PGConnections\Pages;

use App\Filament\Resources\PGConnections\Concerns\ValidatesGatewayAttributes;
use App\Filament\Resources\PGConnections\PGConnectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPGConnection extends EditRecord
{
    use ValidatesGatewayAttributes;

    protected static string $resource = PGConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->validateGatewayAttributes($data);

        return $data;
    }
}
