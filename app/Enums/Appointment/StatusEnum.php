<?php

namespace App\Enums\Appointment;

enum StatusEnum: string
{
    case Cancelled = 'Cancelled';
    case Closed = 'Closed';
    case Completed = 'Completed';
    case Ongoing = 'Ongoing';
    case Pending = 'Pending';
    case Rejected = 'Rejected';
    case Rescheduled = 'Rescheduled';
}