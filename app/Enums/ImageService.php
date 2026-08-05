<?php
namespace App\Enums;

use App\Models\Master\Expenses;
use App\Models\NonProctology;
use App\Models\PatientAddressProof;
use App\Models\PatientDocument;
use App\Models\PatientAttendantAddressProof;
use App\Models\IPDSurgery;
use App\Models\IPDPreOperativeChecklist;
use App\Models\Proctology;
use App\Models\SystemSettings;
use App\Models\User;
use App\Models\UserAddressProof;
use App\Models\ExternalAppointment;
use App\Models\PatientTests;
use App\Models\IPDPreOperativeAnaesthesiaEvaluation;
use App\Models\IPDAnaesthesiaDepartment;
use App\Models\IPDAnaesthesiaRecoverObservation;
use App\Models\IPDAnaesthesia;
use App\Models\IPDDischargeSummary;


enum ImageService: string {
    case User                = 'user';
    case Expense             = 'expense';
    case Proctology          = 'proctology';
    case PatientTest         = 'patient_tests';
    case NonProctology       = 'non_proctology';
    case Consultations       = 'consultations';
    case SystemSettings      = 'system_settings';
    case UserAddressProof    = 'user_address_proof';
    case PatientAddressProof = 'patient_address_proof';
    case PatientDocument = 'patient_documents';
    case PatientAttendantAddressProof = 'patient_attendant_address_proof';
    case IPDSurgery ='ipd_surgery';
    case IPDPreOperativeChecklist ='ipd_pre_operative_checklist';
    case ExternalAppointment = 'external_appointment';
    case IPDAnaesthesia = 'ipd_anaesthesia';
    case IPDPreOperativeAnaesthesiaEvaluation = 'ipd_pre_operative_anaesthesia_evaluation';
    case IPDAnaesthesiaDepartment = 'ipd_department_anaesthesia';
    case IPDAnaesthesiaRecoverObservation = 'ipd_anaesthesia_recover_observation';
    case IPDDischargeSummary = 'ipd_discharge_summary';
    

    /**
     * Summary of typeOfModal
     * @return string
     */
    public function typeOfModal(): string
    {
        return $this->value;
    }

    /**
     * Summary of model
     * @return string
     */
    public function model(): string
    {
        return match ($this) {
            self::User => User::class,
            self::Expense => Expenses::class,
            self::Proctology => Proctology::class,
            self::PatientTest => PatientTests::class,
            self::NonProctology => NonProctology::class,
            self::SystemSettings => SystemSettings::class,
            self::UserAddressProof => UserAddressProof::class,
            self::PatientAddressProof => PatientAddressProof::class,
            self::PatientDocument => PatientDocument::class,
            self::PatientAttendantAddressProof => PatientAttendantAddressProof::class,
            self::IPDSurgery=>IPDSurgery::class,
            self::IPDPreOperativeChecklist=>IPDPreOperativeChecklist::class,
            self::ExternalAppointment=>ExternalAppointment::class,
            self::IPDPreOperativeAnaesthesiaEvaluation=>IPDPreOperativeAnaesthesiaEvaluation::class,
            self::IPDAnaesthesiaDepartment=>IPDAnaesthesiaDepartment::class,
            self::IPDAnaesthesiaRecoverObservation=>IPDAnaesthesiaRecoverObservation::class,
            self::IPDAnaesthesia=>IPDAnaesthesia::class,
            self::IPDDischargeSummary=>IPDDischargeSummary::class,
        };
    }
}
