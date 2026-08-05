<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait InvoiceValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'include_in_invoice' => 'nullable|boolean',
            'balanced_amount' => 'required|numeric|min:0',
            'collected_amount' => 'required|numeric|min:0',
            'doctor_id' => 'nullable|integer|exists:users,id',
            'patient_id' => 'nullable|uuid|exists:patients,id',
            'consultation_id' => 'nullable|uuid|exists:consultations,id',
            'comment' => 'nullable|string',

        ];

        return $this->validator($request, $rules, $edit);
    }
}