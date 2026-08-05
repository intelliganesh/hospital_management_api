<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
trait PrescriptionValidation
{
    use CustomValidatorTrait;
    public function validate(Request $request, ?bool $edit = false, ?string $userId = '')
    {
        $rules = [
            'consultation_id' => 'required|uuid|exists:consultations,id',
            'doctor_id' => 'required|integer|exists:users,id',
            'patient_id' => 'required|uuid|exists:patients,id',
            'medicine_ids' => 'nullable|string', // Could also use JSON if structured
            // 'medicine_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:50',// e.g., 1-0-1
            'duration' => 'required|string|max:50',// e.g., "5 days"
            'time' => 'required|string|max:100',// e.g., "Morning, Evening"
            'food_advice' => 'nullable|string|max:10000',
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