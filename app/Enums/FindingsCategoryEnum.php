<?php

namespace App\Enums;

enum FindingsCategoryEnum: string
{
    case ENT = 'ENT';
    case Other = 'Other';
    case Vitals = 'Vitals';
    case Dental = 'Dental';
    case ECG_EKG = 'ECG/EKG';
    case Clinical = 'Clinical';
    case Radiology = 'Radiology';
    case Pathology = 'Pathology';
    case Endoscopy = 'Endoscopy';
    case Laboratory = 'Laboratory';
    case Ultrasound = 'Ultrasound';
    case Neurological = 'Neurological';
    case Ophthalmology = 'Ophthalmology';
    case Dermatological = 'Dermatological';

    /**
     * Summary of description
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::Ophthalmology => 'Eye findings',
            self::ENT => 'Ear, nose, throat findings',
            self::Dental => 'Oral/dental observations',
            self::Pathology => 'Tissue/biopsy findings',
            self::ECG_EKG => 'Electrocardiogram results',
            self::Ultrasound => 'Sonographic observations',
            self::Dermatological => 'Skin-related findings',
            self::Endoscopy => 'Internal visuals from scopes',
            self::Radiology => 'Imaging results (e.g., X-ray, MRI, CT scan)',
            self::Neurological => 'Findings related to nervous system exams',
            self::Other => 'Findings that don’t fit into the above categories',
            self::Laboratory => 'Results from lab tests (e.g., blood test, urine test)',
            self::Vitals => 'Basic vital signs (e.g., blood pressure, pulse, temperature)',
            self::Clinical => 'Findings observed by a doctor during physical exams (e.g., swelling, rashes, abnormal sounds)',
        };
    }
}