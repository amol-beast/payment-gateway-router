<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('site_reference_id')
                            ->label('Site Reference ID')
                            ->copyable(),
                        TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->placeholder('-')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (TransactionStatus $state): string => match ($state) {
                                TransactionStatus::SUCCESS => 'success',
                                TransactionStatus::FAILED => 'danger',
                                TransactionStatus::PENDING, TransactionStatus::PROCESSING => 'warning',
                                TransactionStatus::CANCELLED => 'gray',
                                TransactionStatus::REFUNDED => 'info',
                            }),
                        TextEntry::make('payment_method')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('transaction_date_time')
                            ->label('Transaction Date')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),

                Section::make('Client & Customer')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('client.name')
                            ->label('Client'),
                        TextEntry::make('pgConnection.name')
                            ->label('PG Connection')
                            ->placeholder('-'),
                        TextEntry::make('customer.name')
                            ->label('Customer')
                            ->placeholder('-'),
                        TextEntry::make('customer.email')
                            ->label('Customer Email')
                            ->placeholder('-'),
                        TextEntry::make('customer.mobile')
                            ->label('Customer Mobile')
                            ->placeholder('-'),
                    ]),

                Section::make('Amounts')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('amount')
                            ->state(fn (Transaction $record): string => $record->amount['amount']->format(true)),
                        TextEntry::make('transaction_amount')
                            ->label('Transaction Amount')
                            ->state(fn (Transaction $record): string => $record->transaction_amount['transaction_amount']->format(true)),
                        TextEntry::make('pg_fees')
                            ->label('PG Fees')
                            ->state(fn (Transaction $record): string => $record->pg_fees['pg_fees']->format(true)),
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->state(fn (Transaction $record): string => $record->total_amount['total_amount']->format(true)),
                    ]),

                Section::make('Raw Payloads')
                    ->columns(1)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('request_data')
                            ->label('Request Data')
                            ->placeholder('-')
                            ->state(function (Transaction $record): ?string {
                                $json = $record->request_data ? json_encode($record->request_data, JSON_PRETTY_PRINT) : null;

                                return $json === false ? null : $json;
                            })
                            ->extraAttributes(['class' => 'font-mono text-xs whitespace-pre-wrap'])
                            ->columnSpanFull(),
                        TextEntry::make('response_data')
                            ->label('Response Data')
                            ->placeholder('-')
                            ->state(function (Transaction $record): ?string {
                                $json = $record->response_data ? json_encode($record->response_data, JSON_PRETTY_PRINT) : null;

                                return $json === false ? null : $json;
                            })
                            ->extraAttributes(['class' => 'font-mono text-xs whitespace-pre-wrap'])
                            ->columnSpanFull(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
