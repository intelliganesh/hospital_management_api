<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait VitalValidation
{
    use CustomValidatorTrait;
    public function validate(Request|array $request, ?bool $edit = false, ?string $userId = '')
    {
        $rules = [
            'bp' => 'nullable|string|max:255',
            'pulse' => 'nullable|string|max:255',
            'rs' => 'nullable|string|max:10000',
            'cvs' => 'nullable|string|max:10000',
            'temperature' => 'nullable|string|max:255',
            'consultation_id' => 'nullable|uuid|exists:consultations,id',
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