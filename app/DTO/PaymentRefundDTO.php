<?php

namespace App\DTO;

use App\Enums\PaymentType;
use App\Enums\TransactionType;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;
use Spatie\LaravelData\Data;

class PaymentRefundDTO extends Data
{
    public readonly Currency $currency;

    public readonly Money $amount;

    public readonly TransactionType $transactionType;

    public readonly array $customer;
    public readonly PaymentType $paymentType;

    protected ?int $pgConnectionId;
    public function __construct(
        public readonly string $clientDbId,
        public readonly string $clientId,
        string $currency,
        int|float|string $amount,
        public readonly string $site_reference_id,
        public readonly string $refundReason,
        public readonly array $requestData = [],
        int|null $pgConnectionId = null
    ) {
        $this->currency = Currency::of($currency);
        $this->amount = Money::of($amount, $this->currency);

        $this->pgConnectionId = $pgConnectionId;
    }

    public function setPgConnectionId(int $pgConnectionId):void
    {
        $this->pgConnectionId = $pgConnectionId;
    }
    public function getPgConnectionId():int|null
    {
        return $this->pgConnectionId;
    }
}
