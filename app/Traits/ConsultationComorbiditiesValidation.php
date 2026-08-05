<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ConsultationComorbiditiesValidation
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        // $rules = [
        //     "is_chronic" => "required|boolean",
        //     "description" => "nullable|string|max:1000",
        //     "consultation_id" => "required|exists:consultations,id",
        //     "comorbidities_id" => "required|exists:comorbidities,id",
        //     // "consultation_id" => "required|exists:consultations,id" . ($edit ? ',' . $id : ''),
        //     // "comorbidities_id" => "required|exists:comorbidities,id" . ($edit ? ',' . $id : ''),
        //     "name" => "required|string|max:255|unique:consultation_comorbidities,name" . ($edit ? ',' . $id : ''),
        // ];

        $rules = [
            "is_chronic" => "required|boolean",
            "description" => "nullable|string|max:1000",
            "consultation_id" => "required|exists:consultations,id",
            "comorbidities_id" => "required|exists:comorbidities,id",
            // "consultation_id" => "required|exists:consultations,id" . ($edit ? ',' . $id : ''),
            // "comorbidities_id" => "required|exists:comorbidities,id" . ($edit ? ',' . $id : ''),
            // "name" => "required|string|max:255|unique:consultation_comorbidities,name" . ($edit ? ',' . $id . ',id' : ''),

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