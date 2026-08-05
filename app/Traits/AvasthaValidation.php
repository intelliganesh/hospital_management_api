<?php

namespace App\Traits;

use App\Enums\AvasthaEnum;
use Illuminate\Http\Request;

trait AvasthaValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $enums = implode(',', array_column(AvasthaEnum::cases(), 'value'));

        $rules = [
            'name' => 'required|in:' . $enums
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