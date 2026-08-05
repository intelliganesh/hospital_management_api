<?php

namespace App\Traits;

use App\Enums\KoshtaEnum;
use Illuminate\Http\Request;

trait KoshtaValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $enums = implode(',', array_column(KoshtaEnum::cases(), 'value'));

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