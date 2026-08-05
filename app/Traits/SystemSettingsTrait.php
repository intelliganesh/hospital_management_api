<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait SystemSettingsTrait
{
    use CustomValidatorTrait;
    protected function validate(Request $request, $edit = false)
    {
        $rules = [
            "address"              => "required|string|max:1000",
            'theme'                => 'required|in:dark,light,system',
            'primary_color'        => 'nullable|string|max:20',
            'hospital_name'        => 'required|string|max:225',
            'tertiary_color'       => 'nullable|string|max:20',
            'currency_symbol'      => 'required|string|size:1',
            'secondary_color'      => 'nullable|string|max:20',
            'bg_primary_color'     => 'required|string|max:20',
            'bg_tertiary_color'    => 'required|string|max:20',
            'text_primary_color'   => 'nullable|string|max:20',
            'bg_secondary_color'   => 'required|string|max:20',
            'text_tertiary_color'  => 'nullable|string|max:20',
            'text_secondary_color' => 'nullable|string|max:20',
            'hospital_logo'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'invoice_status'       => 'nullable|boolean',
            'invoice_start_number' => 'nullable|integer',
            'payment_status' => 'nullable|boolean',
            'payment_start_number' => 'nullable|integer',
            'appointment_status' => 'nullable|boolean',
            'appointment_start_number' => 'nullable|integer',
            'consultation_status' => 'nullable|boolean',
            'consultation_start_number' => 'nullable|integer',
            'patient_status' => 'nullable|boolean',
            'patient_start_number' => 'nullable|integer',
            'findings_status' => 'nullable|boolean',
            'findings_start_number' => 'nullable|integer',
            'ipd_status' => 'nullable|boolean',
            'ipd_start_number' => 'nullable|integer',
            'opd_status' => 'nullable|boolean',
            'opd_start_number' => 'nullable|integer',
            'test_status' => 'nullable|boolean',
            'test_start_number' => 'nullable|integer',
            'voucher_status' => 'nullable|boolean',
            'voucher_start_number' => 'nullable|integer',
            'currency' => 'required|string|min:2|max:15',
            'opd_prefix' => 'required|string|min:2|max:15',
            'ipd_prefix' => 'required|string|min:2|max:15',
            'patient_prefix' => 'required|string|min:2|max:15',
            'hospital_prefix' => 'required|string|min:2|max:15',
            'findings_prefix'      => 'nullable|string|min:2|max:15',
            'upi'                  => 'nullable|string|max:255',
            'qr_code'              => 'nullable|string|max:255',

            // 'currency' => 'required|string|size:3',
            // 'opd_prefix' => 'required|string|size:3',
            // 'ipd_prefix' => 'required|string|size:3',
            // 'patient_prefix' => 'required|string|size:3',
            // 'hospital_prefix' => 'required|string|size:3',
            // 'findings_prefix' => 'required|string|size:3',
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
