<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait IPDAnaesthesiaDepartmentValidation
{
    /**
     * Validate IPD Anaesthesia Department data
     * @param Request $request
     * @param bool $isUpdate
     * @param string|null $id
     * @return mixed
     */
    public function validateIPDAnaesthesiaDepartment(Request $request, bool $isUpdate = false, string|null $id = null): mixed
    {
        $rules = [
            'ipd_id' => 'required|exists:ipd,id',
            'ipd_surgery_id' => 'required|exists:ipd_surgery,id',
            'ipd_anaesthesia_id' => 'required|exists:ipd_anaesthesia,id',
            'pre_anaesthesia_state' => 'nullable|string',
            'ventilated_patient' => 'nullable|string',
            'npo_status' => 'nullable|string',
            'patient_safety' => 'nullable|string',
            'pre_oxygenation' => 'nullable|string',
            'induction' => 'nullable|string',
            'laryngoscopy' => 'nullable|string',
            'difficult_intubation' => 'nullable|boolean',
            'endotracheal_tube' => 'nullable|string',
            'endotracheal_tube_size' => 'nullable|string',
            'endotracheal_tube_fixed_at' => 'nullable|string',
            'airway' => 'nullable|string',
            'airway_size' => 'nullable|string',
            'mask_anaesthesia' => 'nullable|string',
            'throat_pack' => 'nullable|string',
            'nasogastric_tube' => 'nullable|string',
            'maintenance' => 'nullable|string',
            'iv_access' => 'nullable|string',
            'central_blocks_spinal' => 'nullable|string',
            'central_blocks_epidural' => 'nullable|string',
            'central_blocks_epidural_g' => 'nullable|string',
            'central_blocks_spinal_needle_g' => 'nullable|string',
            'regional_blocks' => 'nullable|string',
            'nerve_stimulator' => 'nullable|string',
            'regional_supplements' => 'nullable|string',
            'drugs_regional' => 'nullable|string',
            'monitoring' => 'nullable|string',
            'temperature' => 'nullable|string',
            'crystalloids_ml' => 'nullable|integer',
            'colloids_ml' => 'nullable|integer',
            'blood_ml' => 'nullable|integer',
            'anaesthesia_technique_brief' => 'nullable|string',
            'summary' => 'nullable|string',
            'abp_details' => 'nullable|string',
            'cvp_details' => 'nullable|string',
            'upload_pdf_path' => 'nullable|string',
        ];

        return Validator::make($request->all(), $rules);
    }
}
