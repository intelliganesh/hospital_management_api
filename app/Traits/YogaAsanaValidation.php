<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\Yoga\DifficultyLevelEnum;

trait YogaAsanaValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'benefits' => 'nullable|string',
            'description' => 'nullable|string',
            'contraindications' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
            'asana_name' => 'required|string|max:100',
            'recommended_duration' => 'nullable|integer|min:0',
            'difficulty_level' => 'required|in:' . implode(',', array_column(DifficultyLevelEnum::cases(), 'value')),
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