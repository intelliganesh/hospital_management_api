<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait OPDValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {

        $rules = [
            'complaint' => 'nullable|string',
            'visit_date' => 'required|date_format:Y-m-d H:i:s',
            'patient_id' => 'required|uuid|exists:patients,id',
            'referred_to_doctor_id' => 'nullable|exists:users,id',
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'status' => 'required|in:Pending,Completed,Converted to IPD,Cancelled',
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules);
    }
}