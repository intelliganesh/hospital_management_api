<?php
namespace App\Enums;

enum AddmissionTypeEnum: string {
    case Elective    = 'Elective';
    case Emergency   = 'Emergency';
    case Transferred = 'Transferred';
}
