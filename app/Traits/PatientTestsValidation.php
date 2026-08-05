<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PatientTestsValidation
{
    use CustomValidatorTrait;
    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'test_description' => 'nullable|string',
            'test_name' => 'required|string|max:255',
            'test_place' => 'required|string|max:255',
            'billing_amount' => 'nullable|integer|min:0',
            'test_id' => 'nullable|exists:tests,id',
            'result_uploaded_by' => 'required|exists:users,id',
            'consultation_id' => 'required|exists:consultations,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'result_status' => 'required|in:Pending,Started,Completed',
            // 'document_upload' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048', // Max 2MB
        ];
        // For PATCH or PUT requests, apply 'sometimes' rule
        // if ($edit) {
        //     foreach ($rules as $field => $rule) {
        //         $rules[$field] = 'sometimes|' . $rule;
        //     }
        // }

        return $this->validator($request, $rules, $edit);
    }
}