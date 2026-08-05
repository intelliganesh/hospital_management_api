<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait AllopathyValidation
{
    use CustomValidatorTrait;
    
    public function validate(Request|array $request, ?bool $edit = false, ?string $id = ''){
        $rules = [];

        // For PATCH or PUT requests, apply 'sometimes' rule
        // if ($edit) {
        //     foreach ($rules as $field => $rule) {
        //         $rules[$field] = 'sometimes|' . $rule;
        //     }
        // }

        return $this->validator($request, $rules,$edit);
    }
}