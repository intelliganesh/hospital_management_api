<?php

namespace App\Traits;


use App\Enums\GenderEnum;
use App\Enums\RelationEnum;
use Illuminate\Http\Request;
use App\Enums\BloodGroupEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\ReferralSourceEnum;
use App\Enums\AddmissionTypeEnum;
use App\Enums\DietaryPreferenceEnum;

trait IPDValidation
{
    use CustomValidatorTrait;
    
    public function validate(Request $request, ?bool $edit = false, ?string $id = ''){
        $rules = [
            'patient_id' => 'nullable|uuid|exists:patients,id',
            'consultation_id' => 'nullable|uuid|exists:consultations,id',
            'ipd_number' => 'required|string|max:50|unique:ipd,ipd_number,' . $id . ',id',
            'phone_no' => 'nullable|string|max:15',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:' . implode(',', array_column(GenderEnum::cases(), 'value')),
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'marital_status' => 'nullable|in:' . implode(',', array_column(MaritalStatusEnum::cases(), 'value')),
            'pincode' => 'nullable|string|max:10',
            'dietary_preference' => 'nullable|in:' . implode(',', array_column(DietaryPreferenceEnum::cases(), 'value')),
            'blood_group' => 'nullable|in:' . implode(',', array_column(BloodGroupEnum::cases(), 'value')),
            'insurance_provider' => 'nullable|string|max:100',
            'insurance_policy_no' => 'nullable|string|max:100',

            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone_number' => 'nullable|string|max:15',
            'attendant_with_patient_name' => 'nullable|string|max:100',
            'attendant_with_patient_phone_no' => 'nullable|string|max:15',
            'relation_to_patient' => 'nullable|in:' . implode(',', array_column(RelationEnum::cases(), 'value')),

            // Admission
            'admission_date' => 'nullable|date',
            'admission_time' => 'nullable|date_format:H:i',
            'admission_type' => 'nullable|in:' . implode(',', array_column(AddmissionTypeEnum::cases(), 'value')),

            // Department
            'admitting_department_id' => 'nullable|integer|exists:departments,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',

            // Doctor
            'doctor_id' => 'nullable|integer|exists:doctors,id',
            'doctor_type' => 'required|string|max:50',
            'doctor_name' => 'required|string|max:100',
            'doctor_email' => 'required|email|max:150|unique:ipd,doctor_email,' . $id . ',id',
            'doctor_phone' => 'nullable|string|max:15',

            // Nurse
            'nurse_id' => 'nullable|integer|exists:nurses,id',
            'nurse_type' => 'required|string|max:50',
            'nurse_name' => 'required|string|max:100',
            'nurse_email' => 'required|email|max:150|unique:ipd,nurse_email,' . $id . ',id',
            'nurse_phone' => 'nullable|string|max:15',

            // Referred by
            'referred_by_name' => 'nullable|string|max:100',
            'referred_by_phone_no' => 'nullable|string|max:15',
            'referred_by_email' => 'nullable|email|max:150',
            'referred_by_hospital_name' => 'nullable|string|max:150',

            // Referral Source
            'referral_source' => 'nullable|in:' . implode(',', array_column(ReferralSourceEnum::cases(), 'value')),

            'complaint' => 'nullable|string|max:255',
            'intial_diagnosis' => 'nullable|string|max:255',
        ];

        // For PATCH or PUT requests, apply 'sometimes' rule
        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules);
    }
}