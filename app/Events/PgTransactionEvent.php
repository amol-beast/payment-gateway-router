<?php

namespace App\Events;

use App\DTO\PaymentResponseDTO;
use App\Enums\TransactionStatus;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

class PgTransactionEvent implements ShouldQueueAfterCommit
{
    use Dispatchable;

    public function __construct(
        public readonly PaymentResponseDTO $paymentResponseDTO,
        public readonly TransactionStatus $transactionStatus,
    ) {}
}
