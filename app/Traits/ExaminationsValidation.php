<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ExaminationsValidation
{

    use CustomValidatorTrait;
    protected function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'rs' => 'nullable|string',
            'cvs' => 'nullable|string',
            'bp' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'pulse' => 'nullable|string|max:50',
            'temperature' => 'nullable|string|max:50',
            'doctor_id' => 'required|integer|exists:users,id',
            'patient_id' => 'required|uuid|exists:patients,id',
            'appointment_id' => 'required|uuid|exists:appointments,id',
            // 'examination_categories_id' => 'required|uuid|exists:examination_categories,id',
            'examination_overview' => 'nullable|string',
            // 'patient_number' => 'required|string|unique:examinations,patient_number' . ($edit ? ',' . $id : ''),
            // 'complaint' => 'nullable|string',
            // 'advice' => 'nullable|string',
            // 'preliminary_diagnosis' => 'nullable|string',
            // 'next_visit_date' => 'required|date',
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}