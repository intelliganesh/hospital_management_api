<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Enums\Consultation\TypeEnum;
// use App\Enums\DepartmentTypeEnum;

trait DietPlansValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            "calories" => "nullable|string|max:255",
            "description" => "nullable|string|max:1000",
            "diet_name" => "required|string|max:255|unique:diet_plans,diet_name" . ($edit ? ',' . $id : ''),
            "is_active" => "nullable|in:" . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
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