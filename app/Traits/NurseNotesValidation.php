<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait NurseNotesValidation
{
    use CustomValidatorTrait;

    public function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'nurse_id' => 'required|integer|exists:users,id',
            'bp' => 'nullable|string|max:50',
            'spo2' => 'nullable|string|max:50',
            'temperature' => 'nullable|string|max:50',
            'pulse' => 'nullable|string|max:50',
            'remark1' => 'nullable|string',
            'remark2' => 'nullable|string',
            'datetime' => 'nullable|date',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
