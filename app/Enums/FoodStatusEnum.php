<?php

namespace App\Enums;

enum FoodStatusEnum: string
{
    case Available = 'Available';
    case Unavailable = 'Unavailable';
    case Discontinued = 'Discontinued';
}