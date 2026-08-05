<?php

namespace App\Traits;

use App\Enums\Consultation\TypeEnum;
use Illuminate\Http\Request;

trait ProctoscopyValidation
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'proctoscopys_name' => isset($id) && $id !== '' && $edit
                ? 'required|max:100|unique:proctoscopys,proctoscopys_name,' . $id . ',id'
                : 'required|max:100|unique:proctoscopys,proctoscopys_name',
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