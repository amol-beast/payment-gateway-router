<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\TransactionsResource;
use App\Models\Transaction;
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
            $this->makeCheckTransactionStatusAction('checkTransactionStatus', 'Check Transaction Status', TransactionStatus::PENDING),
            $this->makeCheckTransactionStatusAction('recheckTransactionStatus', 'Recheck Transaction Status', TransactionStatus::SUCCESS),
            $this->makeCheckTransactionStatusAction('checkFailedTransactionStatus', 'Check Payment Status', TransactionStatus::FAILED),

            EditAction::make(),
        ];
    }

    protected function getTransaction(): Transaction
    {
        $record = $this->getRecord();

        if (! $record instanceof Transaction) {
            throw new \RuntimeException('Expected a Transaction record.');
        }

        return $record;
    }

    protected function makeCheckTransactionStatusAction(string $name, string $label, TransactionStatus $visibleForStatus): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('warning')
            ->visible(fn (): bool => $this->getTransaction()->status === $visibleForStatus)
            ->requiresConfirmation()
            ->action(function (TransactionService $transactionService): void {
                try {
                    $transactionService->getTransactionStatus($this->getTransaction()->getKey());

                    $this->getTransaction()->refresh();

                    Notification::make()
                        ->title('Transaction status updated')
                        ->body("Current status: {$this->getTransaction()->status->value}")
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title('Failed to check transaction status')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
