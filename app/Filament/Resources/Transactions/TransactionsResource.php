<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\CreateTransactions;
use App\Filament\Resources\Transactions\Pages\EditTransactions;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\ViewTransactions;
use App\Filament\Resources\Transactions\Schemas\TransactionsForm;
use App\Filament\Resources\Transactions\Schemas\TransactionsInfolist;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Transactions';

    protected static ?string $navigationLabel = 'One time Transaction';

    public static function form(Schema $schema): Schema
    {
        return TransactionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
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
            'index' => ListTransactions::route('/'),
            'create' => CreateTransactions::route('/create'),
            'view' => ViewTransactions::route('/{record}'),
            'edit' => EditTransactions::route('/{record}/edit'),
        ];
    }
}
