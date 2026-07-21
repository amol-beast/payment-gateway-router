<?php

namespace App\Filament\Resources\PGConnections\Tables;

use App\Models\PGConnection;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PGConnectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'clientConnections as active_clients_count' => fn (Builder $query) => $query->where('status', true),
            ]))
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('pg_class')
                    ->label('PG Class')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Environment')
                    ->badge(),

                TextColumn::make('active_clients_count')
                    ->label('Active Clients')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('pgApiLogs')
                    ->label('PG API Logs')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('gray')
                    ->modalHeading(fn (PGConnection $record): string => "PG API Logs - {$record->name}")
                    ->modalContent(fn (PGConnection $record) => view('filament.modals.payment-gateway-connection-api-logs', ['pgConnection' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('7xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
