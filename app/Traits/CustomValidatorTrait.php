<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
trait CustomValidatorTrait
{
    public function validator(Request|array $request, $rules, $edit)
    {
        if ($edit) {
            foreach ($rules as $field => $rule) {
                $rules[$field] = 'sometimes|' . $rule;
            }
        }
        $data = is_array($request) ? $request : $request->all();
        return Validator::make($data, $rules);
    }
}