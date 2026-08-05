<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait IpdEnrollmentValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'consultation_id' => 'nullable|exists:consultations,id',

            'patient_id' => 'nullable|exists:patients,id',

            // Required only when patient_id is NOT provided
            'patient_first_name'       => 'nullable|string|max:100',
            'patient_last_name'        => 'nullable|string|max:100',
            'patient_gender'           => 'nullable|in:Male,Female,Other',
            'patient_attendant_name'   => 'nullable|string|max:100',
            'patient_attendant_phone'  => 'nullable|digits_between:10,15',

            'admission_date_time' => 'required|date_format:Y-m-d H:i:s',

            'ward_id' => 'nullable|integer|exists:ward,id',
            'room_id' => 'nullable|integer|exists:rooms,id',
            'bed_id'  => 'nullable|integer|exists:bed,id',

            'advance_amount' => 'nullable|numeric|min:0',

            // Doctors
            'consultant_doctor' => 'nullable|array|min:1',
            'consultant_doctor.*' => 'integer|exists:users,id',

            'duty_doctor' => 'nullable|array',
            'duty_doctor.*' => 'integer|exists:users,id',

            'nurse' => 'nullable|array',
            'nurse.*' => 'integer|exists:users,id',
        ];


        // For PATCH or PUT requests, apply 'sometimes' rule
        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        $validator = $this->validator($request, $rules, $edit);

        // Add custom validation: if patient_id is not provided, patient details are required (only for create)
        if (!$edit) {
            $validator->after(function ($validator) use ($request) {
                if (empty($request->patient_id)) {
                    if (empty($request->patient_first_name)) {
                        $validator->errors()->add('patient_first_name', 'The patient first name is required when patient_id is not provided.');
                    }
                    if (empty($request->patient_last_name)) {
                        $validator->errors()->add('patient_last_name', 'The patient last name is required when patient_id is not provided.');
                    }
                    if (empty($request->patient_gender)) {
                        $validator->errors()->add('patient_gender', 'The patient gender is required when patient_id is not provided.');
                    }
                    if (empty($request->patient_attendant_name)) {
                        $validator->errors()->add('patient_attendant_name', 'The patient attendant name is required when patient_id is not provided.');
                    }
                    if (empty($request->patient_attendant_phone)) {
                        $validator->errors()->add('patient_attendant_phone', 'The patient attendant phone is required when patient_id is not provided.');
                    }
                }
            });
        }

        return $validator;
    }
}
