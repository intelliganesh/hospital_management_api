<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\Consultation\TypeEnum;

trait TestValidation
{
    use CustomValidatorTrait;
    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {

        $rules = [
            'test_description' => 'nullable|string',
            'test_name' => isset($id) && $id !== '' && $edit
                ? 'required|string|max:255|unique:tests,test_name,' . $id . ',id'
                : 'required|string|max:255|unique:tests,test_name',
            // "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}