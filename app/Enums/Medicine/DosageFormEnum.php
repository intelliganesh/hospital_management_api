<?php

namespace App\Enums\Medicine;

enum DosageFormEnum: string
{
    case Other = "Other";
    case Syrup = 'Syrup';
    case Drops = 'Drops';
    case Liquid = 'Liquid';
    case Tablet = 'Tablet';
    case Capsule = 'Capsule';
    case Injection = 'Injection';
}