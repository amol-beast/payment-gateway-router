<?php

namespace App\DTO;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use Carbon\Carbon;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;
use Spatie\LaravelData\Data;

class PaymentResponseDTO extends Data
{
    public function __construct(
        public readonly string $transactionDbId,
        public readonly string $siteReferenceId,
        public readonly TransactionStatus $status,
        public readonly string $transactionId,
        public readonly string $description,
        public readonly Money $amount,
        public readonly Money $pgFees,
        public readonly Money $totalAmount,
        public readonly Carbon $transactionDateTime,
        public readonly Currency $currency,
        public readonly PaymentMethod $paymentMethod,
        public readonly string $clientName,
        public readonly string $pgConnection,
        public readonly array $pgResponseRaw,
    ) {
    }
}
