<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\AppointmentTypeEnum;
use App\Enums\Appointment\StatusEnum;
use App\Enums\Payment\PaymentStatusEnum;
trait AppointmentsValidationTrait
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $userId = '')
    {
        $rules = [
            'complaint' => 'nullable|string',
            'appointment_date' => 'nullable|date',
            // 'appointment_fees' => 'required|numeric|min:0',
            'doctor_id' => 'required|integer|exists:users,id',
            'patient_id' => 'required|uuid|exists:patients,id',
            'appointment_time' => 'nullable|date_format:H:i:s',
            'front_desk_user_id' => 'required|integer|exists:users,id',
            'status' => 'required|in:' . implode(',', array_column(StatusEnum::cases(), 'value')),
            'type' => 'required|in:' . implode(',', array_column(AppointmentTypeEnum::cases(), 'value')),
            'payment_status' => 'nullable|in:' . implode(',', array_column(PaymentStatusEnum::cases(), 'value')),
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