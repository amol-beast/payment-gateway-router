<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Utils extends Page
{
    protected string $view = 'filament.pages.utils';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'superadmin']) ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testPayment')
                ->label('Test Payment')
                ->icon(Heroicon::OutlinedCreditCard)
                ->color('primary')
                ->url(route('testPayment')),
        ];
    }
}
