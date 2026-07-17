<?php

namespace App\Filament\Resources\SubscriptionTransactions\Pages;

use App\Filament\Resources\SubscriptionTransactions\SubscriptionTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionTransaction extends EditRecord
{
    protected static string $resource = SubscriptionTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
