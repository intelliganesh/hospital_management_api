<?php
namespace App\Traits;

use App\Enums\ComanStatusEnum;
use App\Enums\DepartmentTypeEnum;
use Illuminate\Http\Request;

trait ServiceCostValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            "cost"         => "required|numeric|min:0",
            "description"  => "nullable|string|max:255",
            "service_name" => "required|string|max:255|unique:service_cost,service_name," . ($edit ? $id : ''),
            "status"       => "required|in:" . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
            // "department_type" => "nullable|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
            "case_type"    => "required|in:" . implode(',', array_column(DepartmentTypeEnum::cases(), 'value')),
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
