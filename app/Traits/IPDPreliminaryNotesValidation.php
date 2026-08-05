<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait IPDPreliminaryNotesValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'chief_complaint'            => 'nullable|string',
            'associated_complaint'       => 'nullable|string',
            'previous_treatment_history' => 'nullable|string',
            'medical_history'            => 'nullable|string',
            'family_history'             => 'nullable|string',
            'personal_history'           => 'nullable|string',
            'allergy'                    => 'nullable|string',
            'bp'                         => 'nullable|string|max:50',
            'pulse'                      => 'nullable|string|max:50',
            'temperature'                => 'nullable|string|max:50',
            'spo2'                       => 'nullable|string|max:50',
            'weight'                     => 'nullable|string|max:50',
            'height'                     => 'nullable|string|max:50',
            'cvs'                        => 'nullable|string',
            'rs'                         => 'nullable|string',
            'per_abdomen'                => 'nullable|string',
            'local_examination'          => 'nullable|string',
            'pr'                         => 'nullable|string',
            'dre'                        => 'nullable|string',
            'proctoscopy'                => 'nullable|string',
            'investigation'              => 'nullable|string',
            'hb'                         => 'nullable|string|max:50',
            'tc'                         => 'nullable|string|max:50',
            'esr'                        => 'nullable|string|max:50',
            'rbs'                        => 'nullable|string|max:50',
            'bt'                         => 'nullable|string|max:50',
            'ct'                         => 'nullable|string|max:50',
            'blood_urea'                 => 'nullable|string|max:50',
            'hiv'                        => 'nullable|string|max:50',
            'hbsag'                      => 'nullable|string|max:50',
            'line_of_treatment'          => 'nullable|string',
            'provisional_diagnosis'      => 'nullable|string',
            'final_diagnosis'            => 'nullable|string',
            'treatment_advised'          => 'nullable|string',
            'treatment_given'            => 'nullable|string',
            'preoperative_instruction'   => 'nullable|string',
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}
