<?php
namespace App\Traits;

use App\Enums\ComanStatusEnum;
use Illuminate\Http\Request;

trait AmountForValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            "description" => "nullable|string|max:1000",
            "amount_for"  => "required|unique:amount_for,amount_for," . ($id ?? ''),
            "status"      => "required|in:" . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
        ];

        // For PATCH or PUT requests, apply 'sometimes' rule
        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}
