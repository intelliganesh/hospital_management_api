<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
class LogActivityFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "logactivity";
    }
}