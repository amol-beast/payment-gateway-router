<?php

namespace App\Models;

use Devhammed\LaravelBrickMoney\Casts\AsCurrency;
use Devhammed\LaravelBrickMoney\Casts\AsIntegerMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionTransaction extends Model
{
    protected $table = 'subscriptions_transactions';

    protected function casts(): array
    {
        return [
            'amount' => AsIntegerMoney::of('currency'),
            'currency' => AsCurrency::class,
            'pg_fees' => AsIntegerMoney::of('currency'),
            'pg_tax' => AsIntegerMoney::of('currency'),
            'transaction_date_time' => 'datetime',
            'data' => 'array',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
