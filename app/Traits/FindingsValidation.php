<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\FindingsCategoryEnum;

trait FindingsValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $enums = implode(',', array_column(FindingsCategoryEnum::cases(), 'value'));

        $rules = [
            'category' => 'required|in:' . $enums,
            'status' => 'required|in:Active,Inactive',
            'finding_description' => 'nullable|string',
            'finding_name' => 'required|string|max:100',
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