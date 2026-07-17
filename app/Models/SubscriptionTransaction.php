<?php

namespace App\Models;

use Devhammed\LaravelBrickMoney\Casts\AsCurrency;
use Devhammed\LaravelBrickMoney\Casts\AsIntegerMoney;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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

    /**
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * @return HasOneThrough<Client, Subscription, $this>
     */
    public function client(): HasOneThrough
    {
        return $this->hasOneThrough(
            Client::class,
            Subscription::class,
            'id',
            'id',
            'subscription_id',
            'client_id',
        );
    }
}
