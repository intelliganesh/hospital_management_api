<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\Payment\PaymentTypeEnum;

trait ExpensesValidation
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'date' => 'required|date',
            // 'proof' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'other' => 'nullable|string|max:255',
            'for_name' => 'nullable|string|max:255',
            'entered_name' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'expense_name' => 'required|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'mode_of_payment' => 'required|string|in:' . implode(',', array_column(PaymentTypeEnum::cases(), 'value')),
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