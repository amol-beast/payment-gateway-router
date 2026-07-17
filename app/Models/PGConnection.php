<?php

namespace App\Models;

use App\Enums\ConnectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PGConnection extends Model
{
    protected $table = 'pg_connections';
    protected $casts = [
        'attributes' => 'array',
        'status' => 'boolean',
        'type' => ConnectionType::class,
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function clientConnections(): HasMany
    {
        return $this->hasMany(ClientConnection::class, 'pg_connection_id');
    }

}
