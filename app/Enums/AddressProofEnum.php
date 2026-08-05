<?php
namespace App\Enums;

enum AddressProofEnum: string {
    case Aadhaar        = 'aadhaar';
    case VoterId        = 'voter_id';
    case Passport       = 'passport';
    case RationCard     = 'ration_card';
    case DrivingLicense = 'driving_license';
    case Reports        = 'reports';
}
