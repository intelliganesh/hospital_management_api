<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait FieldValuesTrait
{
    public function fileds(Request|array $request): array
    {
        $fields = is_array($request) ? $request : $request->all();

        if (empty($fields)) {
            throw new NotFoundHttpException("Fields are empty.");
        }

        return $fields;
    }
}