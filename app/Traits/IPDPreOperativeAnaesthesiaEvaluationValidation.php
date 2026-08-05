<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait IPDPreOperativeAnaesthesiaEvaluationValidation
{
    use CustomValidatorTrait;

    /**
     * Validate Pre-Operative Anaesthesia Evaluation data
     */
    public function validatePreOperativeAnaesthesiaEvaluation(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'ipd_surgery_id' => 'required|exists:ipd_surgery,id',
            'ipd_anaesthesia_id' => 'nullable|exists:ipd_anaesthesia,id',
            'previous_anaesthesia_surgery' => 'nullable|string',
            'current_medication' => 'nullable|string',
            'allergies' => 'nullable|string',
            'asa_grading' => 'nullable|string',
            'airway_assessment' => 'nullable|string',
            'respiratory_system' => 'nullable|string',
            'cardio_vascular_system' => 'nullable|string',
            'cns_musculoskeletal' => 'nullable|string',
            'hepatic_renal' => 'nullable|string',
            'endocrine' => 'nullable|string',
            'other_system' => 'nullable|string',
            'clinical_evaluation' => 'nullable|string',
            'hb_hct' => 'nullable|string',
            'tc' => 'nullable|string',
            'platelets' => 'nullable|string',
            'bt_ct' => 'nullable|string',
            'pt_ptt' => 'nullable|string',
            'inr' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'fbs_rbs' => 'nullable|string',
            'bun' => 'nullable|string',
            'na_k' => 'nullable|string',
            'chest_xray' => 'nullable|string',
            'ecg' => 'nullable|string',
            'echo' => 'nullable|string',
            'other_investigation' => 'nullable|string',
            'specific_anaesthesia_problem' => 'nullable|string',
            'pre_operative_anaesthesia_instruction' => 'nullable|string',
            'summary' => 'nullable|string',
            'datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'upload_pdf_path' => 'nullable|file|mimes:pdf|max:5120',
            'mouth_opening' => 'nullable|string',
            'teeth' => 'nullable|string',
            'neck_movement' => 'nullable|string',
            'mallampati_score' => 'nullable|string',
            'dentures_check' => 'nullable|string',
            'tmd' => 'nullable|string',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
