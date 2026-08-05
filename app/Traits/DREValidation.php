<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\Consultation\TypeEnum;

trait DREValidation
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'dre_name' => isset($id) && $id !== '' && $edit
                ? 'required|max:100|unique:_d_r_e,dre_name,' . $id . ',id'
                : 'required|max:100|unique:_d_r_e,dre_name',
            "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
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