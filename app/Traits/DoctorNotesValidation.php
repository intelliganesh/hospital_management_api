<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait DoctorNotesValidation
{
    use CustomValidatorTrait;

    public function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'doctor_id' => 'required|integer|exists:users,id',
            'datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'gc' => 'nullable|string|max:255',
            'bp' => 'nullable|string|max:50',
            'pr' => 'nullable|string|max:50',
            'clinical_notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
