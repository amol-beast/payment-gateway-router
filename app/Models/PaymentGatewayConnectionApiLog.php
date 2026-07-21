<?php

namespace App\Models;

use App\Enums\PaymentGatewayRequestType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentGatewayConnectionApiLog extends Model
{
    protected $casts = [
        'request_type' => PaymentGatewayRequestType::class,
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsTo<PGConnection, $this>
     */
    public function pgConnection(): BelongsTo
    {
        return $this->belongsTo(PGConnection::class, 'pg_connection_id');
    }

    /**
     * @return BelongsTo<Transaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
