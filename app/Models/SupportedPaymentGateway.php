<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportedPaymentGateway extends Model
{
    protected $casts = [
        'attributes' => 'array',
        'status' => 'boolean',
    ];
}
