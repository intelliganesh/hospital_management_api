<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait IPDDischargeSummaryValidation
{
    use CustomValidatorTrait;

    public function validateDischargeSummary(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'doctor_incharge' => 'nullable|string',
            'consultants' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'case_history_and_complaints' => 'nullable|string',
            'general_examination' => 'nullable|string',
            'systemic_examination' => 'nullable|string',
            'investigations' => 'nullable|string',
            'operation_done' => 'nullable|string',
            'findings_and_procedure' => 'nullable|string',
            'course_in_hospital' => 'nullable|string',
            'patient_health_condition_at_discharge' => 'nullable|string',
            'advice_on_discharge' => 'nullable|string',
            'special_instruction' => 'nullable|string',
            'upload_pdf_path' => 'nullable|file|mimes:pdf|max:5120',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
