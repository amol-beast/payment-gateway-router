<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CARD = 'card';
    case UPI = 'upi';
    case NETBANKING = 'nettbanking';
    case CREDIT_CARD = 'creditcard';
    case DEBIT_CARD = 'debitcard';
    case WALLET = 'wallet';
}
