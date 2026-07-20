<?php

namespace App\Listeners;

use App\Events\PgTransactionEvent;
use App\Mail\TransactionStatusMail;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PgTransactionEventListener implements ShouldQueue
{
    public function handle(PgTransactionEvent $event): void
    {
        $transaction = Transaction::with('client.user')->find($event->paymentResponseDTO->transactionDbId);

        if (! $transaction || ! $transaction->client) {
            return;
        }

        $client = $transaction->client;

        if ($client->webhook_uri) {
            $this->sendWebhook($client, $event);
        }

        if (config('services.pg_transaction_notifications.email_enabled') && $client->user?->email) {
            $this->sendEmail($client->user->email, $event);
        }
    }

    protected function sendWebhook($client, PgTransactionEvent $event): void
    {
        $payload = [
            'transaction_id' => $event->paymentResponseDTO->transactionId,
            'site_reference_id' => $event->paymentResponseDTO->siteReferenceId,
            'status' => $event->transactionStatus->value,
            'amount' => (string) $event->paymentResponseDTO->amount->getAmount(),
            'total_amount' => (string) $event->paymentResponseDTO->totalAmount->getAmount(),
            'currency' => (string) $event->paymentResponseDTO->currency,
            'payment_method' => $event->paymentResponseDTO->paymentMethod->value,
            'transaction_date_time' => $event->paymentResponseDTO->transactionDateTime->toIso8601String(),
        ];

        $signature = hash_hmac('sha256', json_encode($payload), $client->client_secret);

        try {
            Http::withHeaders([
                'X-Client-Id' => $client->client_id,
                'X-Signature' => $signature,
            ])->post($client->webhook_uri, $payload)->throw();
        } catch (\Throwable $e) {
            Log::warning('PG transaction webhook delivery failed', [
                'client_id' => $client->client_id,
                'webhook_uri' => $client->webhook_uri,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sendEmail(string $email, PgTransactionEvent $event): void
    {
        try {
            Mail::to($email)->send(new TransactionStatusMail($event->paymentResponseDTO, $event->transactionStatus));
        } catch (\Throwable $e) {
            Log::warning('PG transaction status email delivery failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
