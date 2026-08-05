<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\AppointmentTypeEnum;
// use App\Enums\Consultation\TypeEnum;
use App\Enums\Appointment\StatusEnum;
trait ConsultationsValidation
{
    use CustomValidatorTrait;
    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'advice' => 'nullable|string',
            'test_id' => 'nullable|string',
            'complaint' => 'nullable|string',
            'medical_id' => 'nullable|string',
            'fees' => 'required|numeric|min:0',
            'next_visit_date' => 'nullable|date',
            'surgical_cost' => 'nullable|numeric|min:0',
            'doctor_id' => 'required|integer|exists:users,id',
            'front_desk_user_id' => 'required|integer|exists:users,id',
            'appointment_id' => 'required|uuid|exists:appointments,id',
            // 'type' => 'required|in:' . implode(',', array_column(TypeEnum::cases(), 'value')),
            'status' => 'required|in:' . implode(',', array_column(StatusEnum::cases(), 'value')),
            'appointment_type' => 'required|in:' . implode(',', array_column(AppointmentTypeEnum::cases(), 'value')),

            'preliminary_diagnosis' => $edit ? 'nullable|string' : 'nullable|string',
            'test_in_same_hospital' => $edit ? 'required|boolean' : 'nullable|boolean',
            'external_appointment_id' => $edit ? 'nullable|uuid|exists:external_appointments,id' : 'nullable|uuid|exists:external_appointments,id',

            // 'diet_plan' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'test_in_same_hospital' => $edit ? 'required|boolean' : "nullable|boolean",
            // 'chief_complaints' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'surgical_history' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'co_morbidities' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'on_examination' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'treatment_plan' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'tests' => $edit ? 'required|string|max:1000' : "nullable|string|max:1000",
            // 'amount' => $edit ? 'required' : "nullable",

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