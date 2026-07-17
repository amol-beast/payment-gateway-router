<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use Devhammed\LaravelBrickMoney\Casts\AsCurrency;
use Devhammed\LaravelBrickMoney\Casts\AsIntegerMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => AsIntegerMoney::of('currency'),
            'transaction_amount' => AsIntegerMoney::of('currency'),
            'currency' => AsCurrency::class,
            'status' => TransactionStatus::class,
            'payment_method' => PaymentMethod::class,
            'service_tax_amount' => AsIntegerMoney::of('currency'),
            'processing_fee_amount' => AsIntegerMoney::of('currency'),
            'recover_pg_fees' => 'boolean',
            'pg_fees' => AsIntegerMoney::of('currency'),
            'total_amount' => AsIntegerMoney::of('currency'),
            'transaction_date_time' => "datetime",
            'response_data' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ClientCustomer::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
