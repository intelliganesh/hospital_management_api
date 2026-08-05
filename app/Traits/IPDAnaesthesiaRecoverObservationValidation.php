<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait IPDAnaesthesiaRecoverObservationValidation
{
    /**
     * Validate IPD Anaesthesia Recovery Observation data
     * @param Request $request
     * @param bool $isUpdate
     * @param string|null $id
     * @return mixed
     */
    public function validateIPDAnaesthesiaRecoverObservation(Request $request, bool $isUpdate = false, string|null $id = null): mixed
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'ipd_surgery_id' => 'required|exists:ipd_surgery,id',
            'ipd_anaesthesia_id' => 'required|exists:ipd_anaesthesia,id',
            'surgical_procedure' => 'nullable|string',
            'time_patient_received' => 'nullable|date_format:Y-m-d H:i:s',
            'post_operative_instructions' => 'nullable|string',
            'monitors' => 'nullable|string',
            'post_operative_complications' => 'nullable|string',
            'post_operative_medications' => 'nullable|string',
            'patient_score_on_admission' => 'nullable|string',
            'patient_score_before_transfer' => 'nullable|string',
            'vital_monitoring' => 'nullable|json',
            'transfer_to' => 'nullable|string',
            'time_of_transfer' => 'nullable|date_format:Y-m-d H:i:s',
            'pulse_at_shifting' => 'nullable|string',
            'sbp_at_shifting' => 'nullable|string',
            'dbp_at_shifting' => 'nullable|string',
            'rr_at_shifting' => 'nullable|string',
            'summary' => 'nullable|string',
            'upload_pdf_path' => 'nullable|string',
        ];

        return Validator::make($request->all(), $rules);
    }
}
