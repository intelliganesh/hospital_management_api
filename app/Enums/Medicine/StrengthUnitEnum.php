<?php

namespace App\Enums\Medicine;

enum StrengthUnitEnum: string
{
    case g = 'g';
    case l = 'l';
    case mg = 'mg';
    case ml = 'ml';
    case Other = 'Other';
}