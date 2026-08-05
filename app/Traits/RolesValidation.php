<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait RolesValidation
{
    use CustomValidatorTrait;
    protected function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'description' => 'nullable|max:255',
            'status' => 'nullable|in:Active,Inactive',
            'name' => isset($id) && $id !== '' && $edit
                ? 'required|max:100|unique:roles_name,name,' . $id . ',id'
                : 'required|max:100|unique:roles_name,name',
        ];
        // if ($edit) {
        //     foreach ($rules as $field => $rule) {
        //         $rules[$field] = 'sometimes|' . $rule;
        //     }
        // }

        return $this->validator($request, $rules, $edit);
    }
}