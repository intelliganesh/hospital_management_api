<?php
namespace App\Traits;

use App\Enums\ComanStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Http\Request;
trait UserValidationTrait
{
    use CustomValidatorTrait;
    protected function validate(Request $request, ?bool $edit = false, ?string $userId = '')
    {
        $rules = [
            'name'               => 'required|string|max:255',
            // 'city' => 'required|string|max:100',
            'city'               => 'nullable|string|max:100',
            // 'state' => 'required|string|max:100',
            'state'              => 'nullable|string|max:100',
            // 'password' => 'nullable|string|min:6',
            // 'address' => 'required|string|max:500',
            'address'            => 'nullable|string|max:500',
            // 'DOB' => 'required|date|before:today',
            'dob'                => 'nullable|date|before:today',
            // 'country' => 'required|string|max:100',
            'country'            => 'nullable|string|max:100',
            'pincode'            => 'nullable|digits_between:4,10',
            // 'age' => 'required|integer|min:18|max:120',
            'age'                => 'nullable|integer|max:120',
            // 'designation' => 'required|string|max:100',
            'role'               => 'required|string|max:100',
            'designation'        => 'nullable|string|max:100',
            'department'         => 'required|string|max:255',
            // 'qualification' => 'required|string|max:255',
            'qualification'      => 'nullable|string|max:255',
            // 'email' => 'required|string|email|max:255',
            // 'phone' => 'required|string|regex:/^[6-9]\d{9}$/',
            // 'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'system_settings_id' => 'required|exists:system_settings,id',
            'gender'             => 'required|in:' . implode(",", array_column(GenderEnum::cases(), 'value')),
            'marital_status'     => 'nullable|in:' . implode(",", array_column(MaritalStatusEnum::cases(), 'value')),
            'available_days'     => 'nullable|string',
            'slot_duration'      => 'nullable|string|max:50',
            'leave_date'         => 'nullable|string',
            'email'              => isset($userId) && $userId !== '' && $edit ? 'required|string|email|max:255|unique:users,email,' . $userId . ',id' : 'required|string|email|max:255|unique:users,email',
            'phone'              => isset($userId) && $userId !== '' && $edit ? 'required|string|unique:users,phone,' . $userId . ',id' : 'required|string|unique:users,phone',
            'status'             => 'required|in:' . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
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
