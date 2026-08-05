<?php
namespace App\Traits;

use App\Enums\AvasthaEnum;
use App\Enums\KoshtaEnum;
use App\Enums\PrakritiEnum;
use App\Enums\VrikrutiEnum;
use Illuminate\Http\Request;

trait NonProctologyValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'medicines'        => 'nullable|string',
            'advice_field'     => 'nullable|string',

            'tests'            => 'required|string|max:1000',
            // 'diet_plan' => 'required|string|max:1000',
            'yoga_asana'       => 'required|string|max:1000',
            'food_advice'      => 'required|string|max:1000',
            'on_examination'   => 'required|string|max:1000',
            'treatment_plan'   => 'required|string|max:1000',
            'surgical_history' => 'required|string|max:1000',
            'consultation_id'  => 'nullable|uuid|exists:consultations,id',
            'discount_amount'  => 'nullable|numeric',

            // 'co_morbidities' => 'required|string|max:1000',
            // 'additional_cost' => 'required|string|max:1000',
            // 'chief_complaints' => 'required|string|max:1000',

            'amount'           => 'required',

            'koshta'           => 'nullable|in:' . implode(',', array_column(KoshtaEnum::cases(), 'value')),
            'avastha'          => 'nullable|in:' . implode(',', array_column(AvasthaEnum::cases(), 'value')),
            'vikruti'          => 'nullable|in:' . implode(',', array_column(VrikrutiEnum::cases(), 'value')),
            'prakriti'         => 'nullable|in:' . implode(',', array_column(PrakritiEnum::cases(), 'value')),

            // 'lunch' => 'nullable|string|max:255',
            // 'finding_fields' => 'nullable|string',
            // 'dinner' => 'nullable|string|max:255',
            // 'breakfast' => 'nullable|string|max:255',
            // 'diagnosis_summary' => 'nullable|string',
            // 'food_prescription' => 'nullable|string',
            // 'examination_overview' => 'nullable|string',
            // 'preliminary_diagnostic' => 'nullable|string',
            // 'yoga_asana' => 'nullable|integer|exists:yoga_asana,id',
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
