<?php

namespace App\Enums;

use App\Models\Master\Doctors;

enum ListBasedOnRolesEnum: string
{
    case Nurse = 'Nurse';
    case Doctor = 'Doctor';

    /**
     * Summary of typeOfModal
     * @return string
     */
    public function typeOfModal(): string
    {
        return $this->value;
    }


    /**
     * Summary of model
     * @return string
     */
    public function model(): string
    {
        return match ($this) {
            self::Doctor => Doctors::class,
        // self::Nurse => Nurse::class,
        };
    }

}