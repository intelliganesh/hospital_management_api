<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PatientFistulaValidation
{
    use CustomValidatorTrait;

    /**
     * Validate patient fistula data
     */
    public function validate(Request|array $request, ?bool $isUpdate = false, ?string $id = null)
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'no_of_fistula' => 'nullable|string',
            'no_of_tracks_in_one_fistula' => 'nullable|string',
            'no_of_external_opening_position' => 'nullable|string',
            'external_opening_position' => 'nullable|string',
            'internal_opening_position' => 'nullable|string',
            'any_other' => 'nullable|string',
            'no_of_secondary_opening_position' => 'nullable|string',
            'secondary_opening_position' => 'nullable|string',
            'secondary_anal_valve' => 'nullable|string',
            'other_investigation' => 'nullable|string',
            'anal_valve' => 'nullable|string',
            'type_of_crypt' => 'nullable|string',
            'crypt_cause' => 'nullable|string',
            'type_of_fistula_position' => 'nullable|string',
            'type_of_fistula_sphincter' => 'nullable|string',
            'basis_of_high_low_riding' => 'nullable|string',
            'distant_visceral_communication' => 'nullable|string',
            'sono_fistula_gram' => 'nullable|string',
            'mri_fistula_gram' => 'nullable|string',
            'sonologist_findings' => 'nullable|string',
            'fistula_recurrence' => 'nullable|string',
            'fistula_recurrence_surgery_count' => 'nullable|string',
            'fistula_remark' => 'nullable|string',
            'posterior_fistulous_angle' => 'nullable|string',
            'sonologist' => 'nullable|string',
        ];

        if ($isUpdate) {
            $rules['patient_id'] = 'sometimes|required|exists:patients,id';
        }

        return $this->validator($request, $rules, $isUpdate);
    }
}
