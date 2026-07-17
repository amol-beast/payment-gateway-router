<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientsResource;
use App\Models\Client;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use RuntimeException;

class EditClients extends EditRecord
{
    protected static string $resource = ClientsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->getClientRecord()->users->each(
            fn (User $user) => $user->givePermissionTo('can_view_client'),
        );
    }

    protected function getClientRecord(): Client
    {
        if (! $this->record instanceof Client) {
            throw new RuntimeException('Expected record to be an instance of '.Client::class);
        }

        return $this->record;
    }
}
