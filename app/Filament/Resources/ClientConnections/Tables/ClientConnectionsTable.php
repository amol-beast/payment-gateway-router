<?php

namespace App\Filament\Resources\ClientConnections\Tables;

use App\Enums\PaymentType;
use App\Models\ClientConnection;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClientConnectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable(),

                TextColumn::make('pgConnection.name')
                    ->label('Payment Gateway Connection')
                    ->searchable(),

                IconColumn::make('is_recurring')
                    ->label('Is Recurring')
                    ->boolean(),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('testConnection')
                    ->label('Test Connection')
                    ->icon(Heroicon::OutlinedPlay)
                    ->color('success')
                    ->url(fn (ClientConnection $record): string => route('testPayment', [
                        'clientId' => $record->client->client_id,
                        'paymentType' => $record->is_recurring
                            ? PaymentType::SUBSCRIPTION->value
                            : PaymentType::ONE_TIME_PAYMENT->value,
                        'transactionType' => $record->transaction_type->value,
                    ]))
                    ->openUrlInNewTab(),
                Action::make('pgApiLogs')
                    ->label('PG API Logs')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('gray')
                    ->modalHeading(fn (ClientConnection $record): string => "PG API Logs - {$record->client->name}")
                    ->modalContent(fn (ClientConnection $record) => view('filament.modals.payment-gateway-connection-api-logs', ['clientConnection' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('7xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
