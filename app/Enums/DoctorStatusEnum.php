<?php

namespace App\Enums;

enum DoctorStatusEnum: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case OnLeave = 'On Leave';
}