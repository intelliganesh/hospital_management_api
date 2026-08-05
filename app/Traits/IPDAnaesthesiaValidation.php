<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait IPDAnaesthesiaValidation
{
    /**
     * Validate IPD Anaesthesia data
     */
    public function validateIPDAnaesthesia(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'ipd_surgery_id' => 'required|exists:ipd_surgery,id',
            'diagnosis' => 'nullable|string',
            'position' => 'nullable|string',
            'anaesthetist_assistant' => 'nullable|string',
            'type_of_anaesthesia' => 'nullable|string',
            'uploaded_consent_path' => 'nullable|string',
            'consent_summary' => 'nullable|string',
            'upload_anaesthesia_record_path' => 'nullable|string',
            'anaesthesia_record_summary' => 'nullable|string',
            'datetime' => 'nullable|date_format:Y-m-d H:i:s',
            'patient_height' => 'nullable|numeric|min:0|max:300',
            'patient_weight' => 'nullable|numeric|min:0|max:500',
            'patient_community' => 'nullable|string',
            'patient_mother_tongue' => 'nullable|string',
        ];

        if ($request instanceof Request) {
            return Validator::make($request->all(), $rules);
        }
        return Validator::make($request, $rules);
    }
}
