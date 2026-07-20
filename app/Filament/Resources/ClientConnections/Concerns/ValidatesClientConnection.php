<?php

namespace App\Filament\Resources\ClientConnections\Concerns;

use App\Enums\TransactionType;
use App\Models\ClientConnection;
use App\Models\PGConnection;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

trait ValidatesClientConnection
{
    /**
     * Guard the record from being persisted with an environment that
     * doesn't match its gateway connection, an invalid transaction type,
     * or with more active connections than the client is allowed to have.
     *
     * @param  array<string, mixed>  $data
     */
    protected function validateClientConnection(array $data, ?int $ignoreRecordId = null): void
    {
        $pgConnection = PGConnection::find($data['pg_connection_id'] ?? null);

        if ($pgConnection && $pgConnection->type->value !== $data['type']) {
            $this->failClientConnectionValidation(
                "The connection's environment ({$data['type']}) must match the payment gateway's environment ({$pgConnection->type->value})."
            );
        }

        if (TransactionType::tryFrom($data['transaction_type'] ?? '') === null) {
            $this->failClientConnectionValidation(
                'The selected transaction type is invalid.'
            );
        }

        if (! ($data['status'] ?? false)) {
            return;
        }

        $activeConnectionsCount = ClientConnection::query()
            ->where('client_id', $data['client_id'] ?? null)
            ->where('status', true)
            ->when($ignoreRecordId, fn ($query) => $query->whereKeyNot($ignoreRecordId))
            ->when($data['is_recurring'], fn ($query) => $query->where('is_recurring', $data['is_recurring']))
            ->count();

        $maxActiveConnections = 1;

        if (($activeConnectionsCount + 1) > $maxActiveConnections) {
            $this->failClientConnectionValidation(
                'This client already has an active connection. Only one active connection is allowed unless it is recurring.'
            );
        }
    }

    protected function failClientConnectionValidation(string $message): void
    {
        Notification::make()
            ->title('Invalid client connection')
            ->body($message)
            ->danger()
            ->send();

        throw new Halt;
    }
}
