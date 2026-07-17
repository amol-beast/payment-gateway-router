<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('roles.name')
                    ->label('Role'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('changeRole')
                    ->label('Role')
                    ->link()
                    ->visible(fn (User $record): bool => $record->hasRole('superadmin'))
                    ->fillForm(fn (User $record): array => [
                        'role' => $record->roles()->value('name'),
                    ])
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'User',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, User $record): void {
                        if (! $record->fresh()->hasRole('superadmin')) {
                            Notification::make()
                                ->title('Only superadmin users can have their role changed here.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->syncRoles([$data['role']]);

                        Notification::make()
                            ->title('Role updated.')
                            ->success()
                            ->send();
                    }),
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
