<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
class AutoIdGenerateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "autoidgenerate";
    }
}