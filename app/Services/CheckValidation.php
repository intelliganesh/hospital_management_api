<?php

namespace App\Services;

use Throwable;
use Illuminate\Validation\ValidationException;
class CheckValidation
{
    /**
     * Summary of checkValidation
     * @param mixed $validator
     * @throws \Illuminate\Validation\ValidationException
     * @return null
     */
    public function checkValidation($validator): Throwable|null
    {
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return null;
    }
}