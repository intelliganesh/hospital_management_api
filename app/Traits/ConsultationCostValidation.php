<?php
namespace App\Traits;

use App\Enums\ComanStatusEnum;
use Illuminate\Http\Request;
// use App\Enums\DepartmentTypeEnum;

trait ConsultationCostValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'amount'            => 'required|numeric|min:0',
            "consultation_name" => "required|unique:consultation_cost,consultation_name," . ($id ?? ''),
            'status'            => 'required|in:' . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
            // "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
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
