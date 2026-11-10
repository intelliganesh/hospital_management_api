<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ShlokaValidation
{
    use CustomValidatorTrait;

    public function validate(Request $request, ?bool $edit = false, ?string $id = '')
    {
        $rules = [
            'title' => 'required|string|max:255',
            'verse_text' => 'required|string',
            'status' => 'required|in:Active,Inactive',
        ];

        return $this->validator($request, $rules, $edit);
    }
}
