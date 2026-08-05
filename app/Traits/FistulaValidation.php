<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait FistulaValidation
{
    use CustomValidatorTrait;

    public function validate(Request | array $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [];

        $rules = [
            'fistula_name' => isset($id) && $id !== '' && $edit
                ? 'required|max:100|unique:fistula,fistula_name,' . $id . ',id'
                : 'required|max:100|unique:fistula,fistula_name',
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
