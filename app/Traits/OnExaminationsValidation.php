<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
// use App\Enums\DepartmentTypeEnum;
use App\Enums\Consultation\TypeEnum;

trait OnExaminationsValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'finding' => 'required|string|max:1000' . ($edit && !empty($id) ? '|unique:on_examinations,finding,' . $id : '|unique:on_examinations,finding'),
            "normal_range" => "nullable|string|max:1000",
            "examination_type" => "nullable|string|max:1000",
            "is_active" => "required|in:" . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
            "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
        ];

        // For PATCH or PUT requests, apply 'sometimes' rule
        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}