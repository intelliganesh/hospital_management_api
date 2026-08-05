<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\AddressProofEnum;

trait PatientAttendantAddressProofValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'consent' => 'required|boolean',
            'id_number' => 'required|string',
            "id_proof_for_pan" => 'nullable|string',
            'id_type' => 'required|in:' . implode(',', array_column(AddressProofEnum::cases(), 'value')),
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
