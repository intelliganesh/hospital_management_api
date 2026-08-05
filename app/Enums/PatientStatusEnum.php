<?php

namespace App\Enums;

enum PatientStatusEnum: string
{
    case Draft = 'Draft';
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Pending = 'Pending';
    case Cancelled = 'Cancelled';
    case Approved = 'Approved';
    case Completed = 'Completed';
    case Resolved = 'Resolved';
    case Unresolved = 'Unresolved';
}