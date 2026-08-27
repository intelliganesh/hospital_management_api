<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait IPDSurgeryValidation
{
    use CustomValidatorTrait;

    public function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'surgery_name' => 'required|string|max:255',
            'surgery_type' => 'required|string|max:255',
            'surgery_date' => 'required|date',
            'status' => 'nullable|string|max:100',
            'surgeon' => 'nullable|string|max:255',
            'anaesthetist' => 'nullable|string|max:255',
            'external_anaesthetist' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'surgery_start_datetime' => 'nullable|date',
            'surgery_end_datetime' => 'nullable|date',
            'assistant_surgeon' => 'nullable|string|max:255',
            'scrub_nurse' => 'nullable|string|max:255',
            'specimen_for_hpe' => 'nullable|string',
            'operative_notes' => 'nullable|string',
            'operative_findings' => 'nullable|string',
            'post_operative_instructions' => 'nullable|string',
            'summary' => 'nullable|string',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
