<?php

namespace App\Traits;

use App\Enums\AgniEnum;
use Illuminate\Http\Request;

trait AgniValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $enums = implode(',', array_column(AgniEnum::cases(), 'value'));
        $rules = [
            'name' => 'required|in:' . $enums,
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