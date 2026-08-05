<?php

namespace App\Enums\Payment;

enum PaymentStatusEnum: string
{
    case Pending = 'Pending';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
}