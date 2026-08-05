<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
trait DateRangeFilterTrait
{
    protected function filterBy($request, Builder $query, $createAt = 'created_at')
    {
        $today = Carbon::today();
        if ($request->input('filterBy') === 'weekly') {
            $query->whereBetween($createAt, [(clone $today)->startOfWeek(), (clone $today)->endOfWeek()]);
        } else if ($request->input('filterBy') === 'monthly') {
            $query->whereMonth($createAt, $today);
        } else if ($request->input('filterBy') === 'custom-date') {
            $startDate = Carbon::createFromFormat('d M, Y', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('d M, Y', $request->end_date)->endOfDay();
            $query->whereBetween($createAt, [$startDate, $endDate]);
        } else {
            $query->whereDate($createAt, $today);
        }

        return $query;
    }
}
