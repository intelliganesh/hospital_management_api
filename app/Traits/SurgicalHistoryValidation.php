<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Enums\Consultation\TypeEnum;
use Illuminate\Validation\Rule;
// use App\Enums\DepartmentTypeEnum;

trait SurgicalHistoryValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {        

        $rules = [
            'surgery_name' => 'required|string|max:50' . ($edit && !empty($id) ? '|unique:surgical_history,surgery_name,' . $id : '|unique:surgical_history,surgery_name'),
            'description' => 'nullable|string|max:1000',
            // 'department_type' => 'required|in:' . implode(',', array_column(TypeEnum::cases(), 'value')),
            "department_type" =>'nullable',
            'is_active' => 'nullable|in:' . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
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