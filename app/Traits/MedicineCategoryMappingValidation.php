<?php

namespace App\Traits;

use Illuminate\Http\Request;
trait MedicineCategoryMappingValidation
{
    use CustomValidatorTrait;

    public function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {

        $rules = [
            'medicine_id' => 'required|integer|exists:medicines,id',
            'category_id' => 'required|integer|exists:medicine_categories,id',
        ];


        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
    
}