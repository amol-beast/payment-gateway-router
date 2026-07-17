<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#Use
class Client extends Model
{
    use SoftDeletes;
    protected $casts = [
        'data' => 'array',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionTransactions(): HasMany
    {
        return $this->hasMany(SubscriptionTransaction::class);
    }

    public function connections(): HasOne
    {
        return $this->hasOne(ClientConnection::class);
    }

    public function oneTimePGConnection(): HasOne
    {
        return $this->hasOne(ClientConnection::class)
            ->where("is_recurring", "=",false);
    }
    public function customers(): HasMany
    {
        return $this->hasMany(ClientCustomer::class);
    }
}
