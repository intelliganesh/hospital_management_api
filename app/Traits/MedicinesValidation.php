<?php
namespace App\Traits;

use App\Enums\Consultation\TypeEnum;
use App\Enums\Medicine\DosageFormEnum;
use App\Enums\Medicine\StrengthUnitEnum;
use Illuminate\Http\Request;

trait MedicinesValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {

        $rules = [
            'is_active'       => 'nullable|boolean',
            'strength'        => 'nullable|string|max:50',
            'unit_price'      => 'nullable|numeric|min:0',
            'generic_name'    => 'nullable|string|max:100',
            // 'unit_price' => 'required|numeric|min:0',
            'manufacturer'    => 'nullable|string|max:100',
            'expiry_date'     => 'nullable|date|after:today',
            'stock_quantity'  => 'required|integer|min:0',
            'strength_value'  => 'nullable|numeric|min:0',
            'medicine_name'   => 'required|string|max:255' . ($edit && ! empty($id) ? '|unique:medicines,medicine_name,' . $id : '|unique:medicines,medicine_name'),
            "department_type" => "nullable|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
            'dosage_form'     => 'nullable|in:' . implode(',', array_column(DosageFormEnum::cases(), 'value')),
            'strength_unit'   => 'nullable|in:' . implode(',', array_column(StrengthUnitEnum::cases(), 'value')),
        ];

        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }

        return $this->validator($request, $rules, $edit);
    }
}
