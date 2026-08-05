<?php
namespace App\Traits;

use App\Enums\DoctorStatusEnum;
use App\Enums\GenderEnum;
use Illuminate\Http\Request;

trait DoctorsValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'dob'                 => 'nullable|date',
            'address'             => 'nullable|string|max:500',
            'availability_days'   => 'nullable|array',
            'full_name'           => 'required|string|max:100',
            'qualification'       => 'nullable|string|max:255',
            'specialization'      => 'nullable|string|max:255',
            'available_timings'   => 'nullable|string|max:100',
            // 'department_id' => 'nullable|exists:departments,id',
            'experience_years'    => 'nullable|integer|min:0|max:60',
            'photo'               => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'consulting_type'     => 'required|in:Consulting,Attending,Both',
            'email'               => 'nullable|email|max:255|unique:doctors,email' . ($edit ? ',' . $id : ''),
            'availability_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'doctor_code'         => 'required|string|max:20|unique:doctors,doctor_code' . ($edit ? ',' . $id : ''),
            'phone_number'        => 'nullable|string|max:20|unique:doctors,phone_number' . ($edit ? ',' . $id : ''),
            'registration_no'     => 'nullable|string|max:100|unique:doctors,registration_no' . ($edit ? ',' . $id : ''),
            'gender'              => 'required|in:' . implode(',', array_column(GenderEnum::cases(), 'value')),
            'status'              => 'nullable|in:' . implode(',', array_column(DoctorStatusEnum::cases(), 'value')),
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
