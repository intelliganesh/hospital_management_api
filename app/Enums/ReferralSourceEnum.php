<?php
namespace App\Enums;

enum ReferralSourceEnum: string {
    case OPD            = 'OPD';
    case Self           = 'Self';
    case External       = 'External';
    case Another_Doctor = 'Another Doctor';
}
