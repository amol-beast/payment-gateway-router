<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientsResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateClients extends CreateRecord
{
    protected static string $resource = ClientsResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] ??= auth()->id();
        $data['uuid'] ??= (string) Str::ulid();
        $data['client_id'] ??= Str::upper(Str::random(16));
        $data['client_secret'] ??= Str::random(40);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->users->each(
            fn ($user) => $user->givePermissionTo('can_view_client'),
        );
    }
}
