<?php

namespace App\Services;

use Carbon\Carbon;

class FormateDateService
{
    public function getFormateDate($date)
    {
        $dateString = $date;
        return $date = Carbon::parse($dateString)->format('Y-m-d');
    }
}