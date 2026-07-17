<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Details')
                    ->description('The basics identifying this client.')
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->columns(4)
                    ->components([
                        TextEntry::make('name')
                            ->columnSpan(3),

                        IconEntry::make('status')
                            ->label('Active')
                            ->boolean()
                            ->columnSpan(1),
                    ]),

                Section::make('User Permissions')
                    ->description('Users granted the "can_view_client" permission for this client.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->components([
                        TextEntry::make('users.name')
                            ->hiddenLabel()
                            ->badge()
                            ->placeholder('No users assigned.'),
                    ]),

                Section::make('URLs')
                    ->description('Where clients are sent during and after authentication.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->columns(4)
                    ->components([
                        TextEntry::make('website')
                            ->url(fn (Client $record): string => $record->website)
                            ->openUrlInNewTab()
                            ->columnSpan(4),

                        TextEntry::make('redirect_uri')
                            ->label('Redirect URI')
                            ->columnSpan(3),

                        TextEntry::make('redirect_uri_separator')
                            ->label('Query Separator')
                            ->columnSpan(1),

                        TextEntry::make('webhook_uri')
                            ->label('Webhook URI')
                            ->placeholder('—')
                            ->columnSpan(4),
                    ]),

                Section::make('API Credentials')
                    ->description('Used by this client to authenticate its API requests.')
                    ->icon(Heroicon::OutlinedKey)
                    ->columns(2)
                    ->components([
                        TextEntry::make('client_id')
                            ->label('Client ID')
                            ->copyable(),

                        TextEntry::make('client_secret')
                            ->label('Client Secret')
                            ->formatStateUsing(fn (): string => str_repeat('•', 32))
                            ->copyable()
                            ->copyableState(fn (Client $record): string => $record->client_secret)
                            ->copyMessage('Client secret copied.')
                            ->hintAction(
                                Action::make('revealClientSecret')
                                    ->label('Reveal')
                                    ->icon(Heroicon::OutlinedEye)
                                    ->color('gray')
                                    ->modalHeading('Client Secret')
                                    ->modalDescription(fn (Client $record): string => $record->client_secret)
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Close'),
                            ),
                    ]),

                Section::make('Additional Data')
                    ->description('Optional, freeform metadata stored alongside this client.')
                    ->icon(Heroicon::OutlinedRectangleStack)
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        KeyValueEntry::make('data')
                            ->hiddenLabel(),
                    ]),
            ]);
    }
}
