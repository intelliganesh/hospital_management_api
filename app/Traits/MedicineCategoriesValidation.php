<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait MedicineCategoriesValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {

        $rules = [
            'category_name' => 'required|string|max:50' . ($edit && !empty($id) ? '|unique:medicine_categories,category_name,' . $id : '|unique:medicine_categories,category_name'),
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}