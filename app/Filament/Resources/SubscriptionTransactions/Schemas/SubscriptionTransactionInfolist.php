<?php

namespace App\Filament\Resources\SubscriptionTransactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SubscriptionTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subscription.id')
                    ->label('Subscription'),
                TextEntry::make('transaction_id'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('currency'),
                TextEntry::make('status'),
                TextEntry::make('payment_method'),
                TextEntry::make('pg_fees')
                    ->numeric(),
                TextEntry::make('pg_tax')
                    ->numeric(),
                TextEntry::make('data')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('transaction_date_time')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
