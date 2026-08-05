<?php

namespace App\Enums;

enum WardStatusEnum: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case UNDER_MAINTENANCE = 'Under Maintenance';
}