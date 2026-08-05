<?php

namespace App\Enums;

enum WardTypeEnum: string
{
    case ICU = 'ICU';
    case GENERAL = 'General';
    case SURGICAL = 'Surgical';
    case ONCOLOGY = 'Oncology';
    case MATERNITY = 'Maternity';
    case PEDIATRIC = 'Pediatric';
    case NEUROLOGY = 'Neurology';
    case EMERGENCY = "Emergency";
    case CARDIOLOGY = 'Cardiology';
    case ORTHOPEDIC = 'Orthopedic';
    case OBSERVATION = 'Observation';
    case PSYCHIATRIC = 'Psychiatric';

    // case ICU = 'icu';
    // case NICU = 'nicu';
    // case PICU = 'picu';
    // case PRIVATE = 'private';
    // case GENERAL = 'general';
    // case EMERGENCY = 'emergency';
    // case ISOLATION = 'isolation';
    // case SEMI_PRIVATE = 'semi_private';
}