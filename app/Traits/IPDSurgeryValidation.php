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
            'shloka_ids' => 'nullable|array',
            'shloka_ids.*' => 'integer',
        ];

        $data = is_array($request) ? $request : $request->all();

        // Saving the surgery report (flag sent by the report form, or any report-only field present)
        // requires at least one Shloka. Other edits of the surgery (name/date/etc.) do not.
        if ($edit && $this->isSurgeryReportSave($data)) {
            $rules['shloka_ids'] = 'required|array|min:1';
            if (! array_key_exists('shloka_ids', $data)) {
                $data['shloka_ids'] = null;
            }
        }

        return $this->validator($data, $rules, $edit);
    }

    protected function isSurgeryReportSave(array $data): bool
    {
        if (! empty($data['surgery_report'])) {
            return true;
        }
        foreach (['operative_notes', 'operative_findings', 'post_operative_instructions', 'specimen_for_hpe'] as $key) {
            if (array_key_exists($key, $data)) {
                return true;
            }
        }
        return false;
    }
}
