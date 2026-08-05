<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DepartmentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "department";
    }
}