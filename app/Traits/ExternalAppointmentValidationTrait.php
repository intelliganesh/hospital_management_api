<?php
namespace App\Traits;

use App\Enums\AppointmentTypeEnum;
use Illuminate\Http\Request;

trait ExternalAppointmentValidationTrait
{
    use CustomValidatorTrait;

    /**
     * Validate external appointment data
     *
     * @param Request|array $request
     * @param bool $edit Whether this is an update operation
     * @param string $id The ID of the appointment being updated
     */
    public function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'name'                 => 'required|string|max:255',
            'age'                  => 'nullable|integer',
            'phone'                => 'required|string|max:20',
            'gender'               => 'nullable|in:Male,Female,Other',
            'email'                => 'required|email',
            'citizenship'          => 'nullable|string|max:255',
            'place_of_living'      => 'nullable|string|max:255',
            'doctor_id'            => 'required|integer|exists:users,id',
            'appointment_datetime' => 'required|date|after:now',
            'alternate_date'       => 'nullable|date|after_or_equal:appointment_datetime',
            'appointment_type'     => 'required|string|max:255',
            'symptoms'             => 'nullable|string|max:1000',
            'status'               => 'nullable|in:Pending,Confirmed,Payment Pending,Paid,Completed,Cancelled',
            'amount'               => 'nullable|numeric|min:0',
            'meeting_link'         => 'nullable|url',
            'payment_type'         => 'nullable|in:link,Bank Transfer',
            'payment_info'         => 'nullable|string|max:1000',
            'visit_type'           => 'nullable|in:' . implode(',', array_column(AppointmentTypeEnum::cases(), 'value')),
            'transaction_id'       => 'nullable|string|max:255',
            'payment_date'         => 'nullable|date',
            'payment_screenshot'   => 'nullable|image|max:2048',
            'meeting_link_type'    => 'nullable|string',
            'currency'             => 'nullable|string|max:10',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
