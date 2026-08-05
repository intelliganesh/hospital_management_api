<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Enums\Consultation\TypeEnum;

trait ManagementValidation
{
    use CustomValidatorTrait;

    public function validate(Request|array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'description' => 'nullable|string|max:1000',
            'management_name' => isset($id) && $id !== '' && $edit
                ? 'required|string|max:255|unique:managements,management_name,' . $id . ',id'
                : 'required|string|max:255|unique:managements,management_name',
            "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
            'is_active' => 'required|in:' . implode(',', array_column(ComanStatusEnum::cases(), 'value')),
        ];

        return $this->validator($request, $rules, $edit);
    }
}