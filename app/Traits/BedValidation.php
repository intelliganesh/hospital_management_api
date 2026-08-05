<?php

namespace App\Traits;

use App\Enums\BedTypeEnum;
use App\Enums\BedSizeEnum;
use App\Enums\BedStatusEnum;
use Illuminate\Http\Request;
trait BedValidation
{
    use CustomValidatorTrait;
    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'nullable|string',
            'bed_number' => 'required|string|max:20|unique:bed,bed_number,' . ($edit ? $id : 'NULL') . ',id',
            'status' => 'required|in:' . implode(',', array_column(BedStatusEnum::cases(), 'value')),
            'bed_type' => 'required|in:' . implode(',', array_column(BedTypeEnum::cases(), 'value')),
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