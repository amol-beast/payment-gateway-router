<?php

namespace App\Models;

use App\Enums\ConnectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientConnection extends Model
{
    use SoftDeletes;
    protected $table = 'clients_connections';

    protected $casts = [
        'pg_fees_recovery' => 'boolean',
        'is_recurring' => 'boolean',
        'status' => 'boolean',
        'type' => ConnectionType::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function pgConnection(): BelongsTo
    {
        return $this->belongsTo(PGConnection::class);
    }
}
