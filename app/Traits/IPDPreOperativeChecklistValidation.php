<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait IPDPreOperativeChecklistValidation
{
    use CustomValidatorTrait;

    /**
     * Validate Pre-Operative Checklist data
     */
    public function validatePreOperativeChecklist(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'ipd_surgery_id' => 'required|exists:ipd_surgery,id',
            'summary' => 'nullable|string',
            'datetime' => 'required|date_format:Y-m-d H:i:s',
            'upload_pdf_path' => 'nullable|file|mimes:pdf|max:5120',
            'q01_investigations' => 'nullable|string',
            'q02_chest_xray_ecg' => 'nullable|string',
            'q03_minor_age_parents' => 'nullable|string',
            'q04a_blood_thinners' => 'nullable|string',
            'q04b_blood_thinners_details' => 'nullable|string',
            'q05a_asthma' => 'nullable|string',
            'q05b_asthma_treatment' => 'nullable|string',
            'q06_medication_allergy' => 'nullable|string',
            'q07_tooth_extraction' => 'nullable|string',
            'q08_surgical_procedure' => 'nullable|string',
            'q09a_diabetic' => 'nullable|string',
            'q09b_blood_sugar' => 'nullable|string',
            'q10_thyroid_medication' => 'nullable|string',
            'q11a_hypertension' => 'nullable|string',
            'q11b_hypertension_medicine' => 'nullable|string',
            'q11c_hypertension_medication_taken' => 'nullable|string',
            'q12_informed_consent' => 'nullable|string',
            'q13_anesthesia_awareness' => 'nullable|string',
            'q14_operative_procedure_awareness' => 'nullable|string',
            'q15a_male_patient_age' => 'nullable|string',
            'q15b_urinary_symptoms' => 'nullable|string',
            'q16_urinary_obstruction' => 'nullable|string',
            'q17_lithotomy_position' => 'nullable|string',
            'q18_previous_surgery' => 'nullable|string',
            'q19_community' => 'nullable|string',
            'q20_previous_surgery_events' => 'nullable|string',
            'q21_female_pregnant' => 'nullable|string',
            'q22_epilepsy' => 'nullable|string',
            'q23_antipsychotic' => 'nullable|string',
            'q24_last_food_intake' => 'nullable|string',
        ];

        return $this->validator($request, $rules, $edit);
    }
}

