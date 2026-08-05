<?php

namespace App\Services;

use App\Enums\Consultation\TypeEnum;
// use App\Enums\DepartmentTypeEnum;

class DepartmentService
{
    /**
     * Summary of normalizeDepartmentType
     * @param mixed $type
     * @return string|null
     */
    public function normalizeDepartmentType(?string $type): string
    {
        $validDepartments = [TypeEnum::Proctology->value, TypeEnum::NonProctology->value, TypeEnum::Allopathy->value];
        return in_array($type, $validDepartments) ? $type : TypeEnum::Proctology->value;
    }
}