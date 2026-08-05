<?php
namespace App\Traits;

use App\Enums\Consultation\TypeEnum;
// use App\Enums\DepartmentTypeEnum;
use Illuminate\Http\Request;

trait DepartmentsValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'is_active'       => 'nullable|boolean',
            'description'     => 'nullable|string|max:255',
            'code'            => 'nullable|string|max:20|unique:departments,code' . ($edit ? ',' . $id : ''),
            'name'            => 'required|string|max:100|unique:departments,name' . ($edit ? ',' . $id : ''),
            // 'type_of_department' => 'required|in:' . implode(',', array_column(TypeEnum::cases(), 'value')),
            // "department_type" => "required|in:" . implode(',', array_column(DepartmentTypeEnum::cases(), 'value')),
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
