<?php

namespace App\Filament\Resources\SubscriptionTransactions\Pages;

use App\Filament\Resources\SubscriptionTransactions\SubscriptionTransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptionTransaction extends ViewRecord
{
    protected static string $resource = SubscriptionTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
