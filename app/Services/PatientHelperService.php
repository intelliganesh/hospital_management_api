<?php

namespace App\Services;

use App\Models\User;
use App\Models\Patient;
use App\Enums\PatientStatusEnum;
use App\Services\CheckValidation;
use App\Traits\PatientsValidationTrait;
class PatientHelperService
{
    use PatientsValidationTrait;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of getByColumnName
     * @param mixed $columnName
     * @param mixed $id
     * @return Patient|null
     */
    public function getByColumnName($columnName = 'id', $id)
    {
        return Patient::where($columnName, $id)->first();
    }


    /**
     * Summary of getTotalPatientNumberInfo
     * @return array{totalOPD: int, totalPatients: int, totalPatientsActive: int}
     */
    public function getTotalPatientNumberInfo()
    {
        $patient = Patient::query();

        return [
            'totalPatients' => $patient->count(),
            'totalOPD' => $patient->where('opd_number', '!=', null)->count(),
            'totalPatientsActive' => $patient->where('status', PatientStatusEnum::Active->value)->count(),
        ];
    }

    /**
     * Summary of updateOrCreateByColumnName
     * @param mixed $request
     * @param mixed $id
     * @param mixed $columnName
     * @return Patient
     */
    public function updateOrCreateByColumnName($request, $id, $columnName = 'id')
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        return Patient::updateOrCreate([$columnName => $id], $request->all());
    }


    /**
     * Summary of patientListForDropDown
     * @return \Illuminate\Database\Eloquent\Collection<int, Patient>
     */
    public function patientListForDropDown()
    {
        return Patient::select('id', 'patient_number')->get();
    }

    /**
     * Summary of getPatientAndUsers
     * @param mixed $doctor_id
     * @param mixed $patient_id
     * @param mixed $front_desk_user_id
     * @return array{doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, front_desk_email: mixed, front_desk_name: mixed, front_desk_phone: mixed, patient_email: mixed, patient_name: string, patient_number: mixed, patient_phone: mixed}
     */
    public function getPatientAndUsers($doctor_id, $patient_id, $front_desk_user_id)
    {
        $doctor = User::where('id', $doctor_id)->first();
        $patient = Patient::where('id', $patient_id)->first();
        $frontDesk = User::where('id', $front_desk_user_id)->first();

        $appointment = [
            'doctor_name' => $doctor->name,
            'doctor_phone' => $doctor->phone,
            'doctor_email' => $doctor->email,

            'patient_email' => $patient->email,
            'patient_phone' => $patient->phone_no,
            'patient_number' => $patient->patient_number,
            'patient_name' => $patient->first_name . ' ' . $patient->last_name,

            'front_desk_user_name' => $frontDesk->name,
            'front_desk_user_phone' => $frontDesk->phone,
            'front_desk_user_email' => $frontDesk->email,

            // 'referred_by_name' => $patient->referred_by_name,
            // 'referred_by_email' => $patient->referred_by_email,
            // 'referred_by_phone_no' => $patient->referred_by_phone_no,
            // 'referred_by_hospital_name' => $patient->referred_by_hospital_name,
        ];
        return $appointment;
    }
}