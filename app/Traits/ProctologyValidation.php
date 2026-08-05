<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait ProctologyValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'medicines'              => 'nullable|string',
            'advice_field'           => 'nullable|string',
            'finding_fields'         => 'nullable|string',
            'diagnosis_summary'      => 'nullable|string',
            'doc_upload'             => 'nullable|string|max:1024',
            'examination_overview'   => 'nullable|string',
            'preliminary_diagnostic' => 'nullable|string',
            'consultation_id'        => 'nullable|uuid|exists:consultations,id',

            'discount_amount'        => 'nullable|numeric',
            'amount'                 => 'required',
            'tests'                  => 'required|string|max:1000',
            'diet_plan'              => 'required|string|max:1000',
            // 'co_morbidities' => 'required|string|max:1000',
            'on_examination'         => 'required|string|max:1000',
            'treatment_plan'         => 'required|string|max:1000',
            'chief_complaints'       => 'required|string|max:1000',
            'surgical_history'       => 'required|string|max:1000',

            // 'test_id' => 'nullable|integer|exists:tests,id',
        ];

        // For PATCH or PUT requests, apply 'sometimes' rule
        // if ($edit) {
        //     foreach ($rules as $field => $rule) {
        //         $rules[$field] = 'sometimes|' . $rule;
        //     }
        // }

        return $this->validator($request, $rules, $edit);
    }
}
