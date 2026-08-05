<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
// use App\Enums\DepartmentTypeEnum;
use App\Enums\Consultation\TypeEnum;

trait ChiefComplaintValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            "description " => 'nullable|string|max:1000',
            "complaint_name" => isset($id) && $id !== '' && $edit
                ? 'required|max:255|unique:chief_complaint,complaint_name,' . $id . ',id'
                : 'required|max:255|unique:chief_complaint,complaint_name',
            "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
            "is_active" => 'nullable|in:' . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
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