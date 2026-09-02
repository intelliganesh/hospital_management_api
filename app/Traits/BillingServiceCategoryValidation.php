<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait BillingServiceCategoryValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'category_name' => 'required|string|max:100',
            'status' => 'required|in:Active,Inactive',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
