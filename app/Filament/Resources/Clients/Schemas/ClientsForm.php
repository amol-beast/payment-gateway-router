<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\User;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ClientsForm
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
                        TextInput::make('name')
                            ->prefixIcon(Heroicon::OutlinedTag)
                            ->columnSpan(3)
                            ->required()
                            ->maxLength(255),

                        Toggle::make('status')
                            ->label('Active')
                            ->onIcon(Heroicon::OutlinedCheckCircle)
                            ->offIcon(Heroicon::OutlinedXCircle)
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1)
                            ->default(true),
                    ]),

                Section::make('User Permissions')
                    ->description('Selected users are granted the "can_view_client" permission.')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->components([
                        Select::make('users')
                            ->hiddenLabel()
                            ->relationship(
                                name: 'users',
                                titleAttribute: 'name',
                                modifyQueryUsing: static fn (Builder $query): Builder => static::usersWithUserRole($query),
                            )
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('URLs')
                    ->description('Where clients are sent during and after authentication.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->columns(4)
                    ->components([
                        TextInput::make('website')
                            ->prefixIcon(Heroicon::OutlinedGlobeAlt)
                            ->url()
                            ->columnSpan(4)
                            ->required()
                            ->maxLength(255),

                        TextInput::make('redirect_uri')
                            ->label('Redirect URI')
                            ->prefixIcon(Heroicon::OutlinedArrowUturnLeft)
                            ->url()
                            ->columnSpan(3)
                            ->required()
                            ->maxLength(255),

                        Select::make('redirect_uri_separator')
                            ->label('Query Separator')
                            ->options([
                                '?' => '?',
                                '&' => '&',
                            ])
                            ->default('?')
                            ->columnSpan(1)
                            ->required(),

                        TextInput::make('webhook_uri')
                            ->label('Webhook URI')
                            ->prefixIcon(Heroicon::OutlinedBolt)
                            ->url()
                            ->columnSpan(4)
                            ->maxLength(255),
                    ]),

                Section::make('Additional Data')
                    ->description('Optional, freeform metadata stored alongside this client.')
                    ->icon(Heroicon::OutlinedRectangleStack)
                    ->collapsible()
                    ->collapsed()
                    ->components([
                        KeyValue::make('data')
                            ->hiddenLabel()
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->addable()
                            ->deletable()
                            ->reorderable(),
                    ]),
            ]);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    protected static function usersWithUserRole(Builder $query): Builder
    {
        return $query->role('user');
    }
}
