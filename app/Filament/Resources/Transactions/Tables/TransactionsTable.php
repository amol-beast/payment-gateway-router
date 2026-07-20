<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('site_reference_id')
                    ->label('Site Reference ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (TransactionStatus $state): string => match ($state) {
                        TransactionStatus::SUCCESS => 'success',
                        TransactionStatus::FAILED => 'danger',
                        TransactionStatus::PENDING, TransactionStatus::PROCESSING => 'warning',
                        TransactionStatus::CANCELLED => 'gray',
                        TransactionStatus::REFUNDED => 'info',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->state(fn (Transaction $record): string => $record->amount['amount']->format(true))
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->state(fn (Transaction $record): string => $record->total_amount['total_amount']->format(true))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
