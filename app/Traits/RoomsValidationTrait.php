<?php

namespace App\Traits;

use App\Enums\LocationEnum;
use Illuminate\Http\Request;

trait RoomsValidationTrait
{
    use CustomValidatorTrait;
    public function validate(Request $request, ?bool $edit = false, ?string $userId = '')
    {
        $rules = [
            'bed_count' => 'required',
            'name' => 'required|string|max:100',
            'floor' => 'nullable|string|max:10',
            'room_type' => 'required|string|max:100',
            'status' => 'required|string|max:100',
            'ward_id' => 'required|exists:ward,id',
            'description' => 'nullable|string',
            'room_number' => 'required|string|max:20|unique:rooms,room_number,' . ($edit ? $userId : 'NULL') . ',id',
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