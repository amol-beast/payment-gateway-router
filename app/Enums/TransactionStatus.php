<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    case PROCESSING = 'processing';

}
