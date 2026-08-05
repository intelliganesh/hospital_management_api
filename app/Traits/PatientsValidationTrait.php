<?php
namespace App\Traits;

use App\Enums\BloodGroupEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\PatientStatusEnum;
use Illuminate\Http\Request;

trait PatientsValidationTrait
{
    use CustomValidatorTrait;
    protected function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'dob'                             => 'nullable|date',
            'age'                             => 'nullable|integer|min:0',
            'city'                            => 'nullable|string|max:255',
            'place_of_living'                 => 'nullable|string|max:255',
            'state'                           => 'nullable|string|max:255',
            'country'                         => 'nullable|string|max:255',
            'address'                         => 'nullable|string|max:1024',
            'last_name'                       => 'nullable|string|max:255',
            // 'enroll_fees' => 'required|numeric|min:0',
            'first_name'                      => 'required|string|max:255',
            'refered_to'                      => 'nullable|string|max:255',
            'gender'                          => 'nullable|in:Male,Female,Other',
            // 'referred_to' => 'nullable|exists:users,id',
            'pincode'                         => 'nullable|digits_between:4,10',
            // 'refered_by' => 'nullable|string|max:255',
            // 'referred_by_email' => 'nullable|email|max:255',
            'insurance_provider'              => 'nullable|string|max:255',
            'insurance_policy_no'             => 'nullable|string|max:255',
            'front_desk_user_id'              => 'nullable|exists:users,id',
            // 'refered_by_phone_no' => 'nullable|string|max:20',
            'attendant_with_patient_name'     => 'nullable|string|max:255',
            'attendant_with_patient_phone_no' => 'nullable|string|max:20',
            // 'payment_status' => 'nullable|in:Payment Pending,Payment Completed',
            // 'referral_status' => 'nullable|in:Not Referred,Referred,Transferred,',
            // 'referred_by_phone_no' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
            // 'emergency_status' => 'nullable|in:Emergency,Critical,Stable,Deceased',
            'dietary_preference'              => 'nullable|in:Vegtarian,Non Vegtarian,Vegan,Eggtarian',
            // 'surgery_status' => 'nullable|in:Surgery Scheduled,Surgery In Progress,Surgery Completed',
            // 'admission_status' => 'nullable|in:Admission Pending,Admitted,Discharge Pending,Discharged,Closed',
            'status'                          => 'nullable|in:' . implode(',', array_column(PatientStatusEnum::cases(), 'value')),
            'bood_group'                      => 'nullable|in:' . implode(',', array_column(BloodGroupEnum::cases(), 'value')),
            'email'                           => isset($id) && $id !== '' && $edit ? 'nullable|email|unique:patients,email,' . $id : 'nullable|email|unique:patients,email',
            'marital_status'                  => 'nullable|in:' . implode(',', array_column(MaritalStatusEnum::cases(), 'value')),
            // 'treatment_status' => 'nullable|in:Under Diagnosis,Test Pending,Test Completed,Prescribed,In Treatment,Under Observation,Follow-up Required',
            'phone_no'                        => 'required',
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}
