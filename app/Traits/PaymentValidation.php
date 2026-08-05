<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PaymentValidation
{
    use CustomValidatorTrait;
    protected function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'amount' => 'required|numeric',
            'amount_for' => 'required|string',
            'doctor_id' => 'nullable|integer|exists:users,id',
            'patient_id' => 'nullable|uuid|exists:patients,id',
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'front_desk_user_id' => 'nullable|integer|exists:users,id',
            // 'payment_type' => 'nullable|in:Cash,Card,Online,UPI,Bank Transfer,Cheque,Wallet,EMI,By Insurance',
            // 'amount_for' => 'required|in:Test,Surgery,Medicine,Room Rent,Ambulance,Enrollment,Appointment,Lab Charges,ICU Charges,Consultation,Admission Fee,Discharge Fee,Miscellaneous,Nursing Charges,Equipment Charges,Operation Theatre',
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}