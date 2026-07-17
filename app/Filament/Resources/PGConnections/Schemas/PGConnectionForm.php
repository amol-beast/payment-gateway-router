<?php

namespace App\Filament\Resources\PGConnections\Schemas;

use App\Enums\ConnectionType;
use App\Models\SupportedPaymentGateway;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PGConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gateway Connection')
                    ->description('Basic details identifying this payment gateway connection.')
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->columns(4)
                    ->components([
                        TextInput::make('name')
                            ->prefixIcon(Heroicon::OutlinedTag)
                            ->columnSpan(2)
                            ->required()
                            ->maxLength(255),

                        Select::make('pg_class')
                            ->label('Payment Gateway Class')
                            ->prefixIcon(Heroicon::OutlinedBuildingLibrary)
                            ->options(fn (): array => SupportedPaymentGateway::query()
                                ->where('status', true)
                                ->pluck('name', 'pg_class')
                                ->all())
                            ->columnSpan(2)
                            ->searchable()
                            ->live()
                            ->required(),

                        Select::make('type')
                            ->prefixIcon(Heroicon::OutlinedAdjustmentsHorizontal)
                            ->options(ConnectionType::labels())
                            ->default(ConnectionType::TEST->value)
                            ->columnSpan(3)
                            ->required(),

                        Toggle::make('status')
                            ->label('Active')
                            ->onIcon(Heroicon::OutlinedCheckCircle)
                            ->offIcon(Heroicon::OutlinedXCircle)
                            ->onColor('success')
                            ->offColor('danger')
                            ->columnSpan(1)
                            ->default(true),
                    ]),

                Section::make('Attributes')
                    ->description('Credentials and settings required by the selected payment gateway.')
                    ->icon(Heroicon::OutlinedKey)
                    ->columns(2)
                    ->compact()
                    ->components(fn (Get $get): array => PGConnectionAttributeSchema::fields($get('pg_class'))),
            ]);
    }
}
