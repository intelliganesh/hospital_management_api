<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\FoodAdviceEnum;
use App\Enums\ComanStatusEnum;
use App\Enums\Consultation\TypeEnum;
// use App\Enums\DepartmentTypeEnum;

trait FoodAdviceValidation
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            "advice_text" => "required|string|max:255",
            "status" => "required|in:" . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
            "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
            "meal_times" => "required|in:" . implode(',', array_column(FoodAdviceEnum::cases(), 'value')),
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