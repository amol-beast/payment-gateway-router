<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\TransactionsResource;
use App\Services\TransactionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTransactions extends ViewRecord
{
    protected static string $resource = TransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkTransactionStatus')
                ->label('Check Transaction Status')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->visible(fn (): bool => $this->record->status === TransactionStatus::PENDING)
                ->requiresConfirmation()
                ->action(function (TransactionService $transactionService): void {
                    try {
                        $transactionService->getTransactionStatus($this->record->id);

                        $this->record->refresh();

                        Notification::make()
                            ->title('Transaction status updated')
                            ->body("Current status: {$this->record->status->value}")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Failed to check transaction status')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            EditAction::make(),
        ];
    }
}
