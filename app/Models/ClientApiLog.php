<?php

namespace App\Models;

use App\Enums\ClientApiLogResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientApiLog extends Model
{
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'result' => ClientApiLogResult::class,
        'datetime' => 'datetime',
    ];

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
