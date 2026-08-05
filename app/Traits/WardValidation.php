<?php

namespace App\Traits;

use App\Enums\LocationEnum;
use App\Enums\WardTypeEnum;
use Illuminate\Http\Request;
use App\Enums\WardStatusEnum;

trait WardValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {

        $rules = [
            'name' => 'required|string|max:100',
            'type' => 'nullable|in:' . implode(',', array_column(WardTypeEnum::cases(), 'value')),
            'floor' => 'nullable|string|max:10',
            'status' => 'required|in:' . implode(',', array_column(WardStatusEnum::cases(), 'value')),
            'ward_number' => 'required|string|max:20|unique:ward,ward_number,' . ($edit ? $id : 'NULL') . ',id',
            'description' => 'nullable|string',
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