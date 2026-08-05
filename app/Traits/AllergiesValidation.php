<?php

namespace App\Traits;

use App\Enums\Consultation\TypeEnum;
use Illuminate\Http\Request;
trait AllergiesValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'notes' => 'nullable|string',
            'allergen_name' => 'required|string|max:50' . ($edit && !empty($id) ? '|unique:allergies,allergen_name,' . $id : '|unique:allergies,allergen_name'),
            // 'reaction_type' => 'required|string|max:255',
            // 'documented_by' => 'required|string|max:255',
            'other_allergen_type' => 'nullable|string|max:255',
            "department_type" => "required|in:" . implode(',', array_column(TypeEnum::cases(), 'value')),
            // 'allergen_type' => 'required|in:Food,Drug,Latex,Plant,Other,Animal,Insect,Vaccine,Chemical,Environmental',
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