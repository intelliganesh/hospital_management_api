<?php
namespace App\Traits;

use App\Enums\AddressProofEnum;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

trait UserAddressProofValidation
{
    use CustomValidatorTrait;
    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'consent'          => 'required|boolean',
            'id_number'        => 'required|string',
            "id_proof_for_pan" => 'nullable|string',
            'id_type'          => 'required|in:' . implode(',', array_column(AddressProofEnum::cases(), 'value')),
        ];
        // 'id_number' => 'required|string',

        return $this->validator($request, $rules, $edit);

    }
}
