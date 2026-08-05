<?php

namespace App\Enums;

enum BedStatusEnum: string
{
    case Occupied = 'Occupied';
    case Available = 'Available';
    case UnderCleaning = 'Under Cleaning';
}