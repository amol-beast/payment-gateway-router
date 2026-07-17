<?php

namespace App\Filament\Resources\ClientConnections;

use App\Filament\Resources\ClientConnections\Pages\CreateClientConnections;
use App\Filament\Resources\ClientConnections\Pages\EditClientConnections;
use App\Filament\Resources\ClientConnections\Pages\ListClientConnections;
use App\Filament\Resources\ClientConnections\Pages\ViewClientConnections;
use App\Filament\Resources\ClientConnections\Schemas\ClientConnectionsForm;
use App\Filament\Resources\ClientConnections\Schemas\ClientConnectionsInfolist;
use App\Filament\Resources\ClientConnections\Tables\ClientConnectionsTable;
use App\Models\ClientConnection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ClientConnectionsResource extends Resource
{
    protected static ?string $model = ClientConnection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return ClientConnectionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientConnectionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientConnectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientConnections::route('/'),
            'create' => CreateClientConnections::route('/create'),
            'view' => ViewClientConnections::route('/{record}'),
            'edit' => EditClientConnections::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
