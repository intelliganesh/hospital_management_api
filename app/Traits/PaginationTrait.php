<?php

namespace App\Traits;
use Illuminate\Http\Request;
trait PaginationTrait
{
    public function paginate(Request $request, $data)
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $data->forPage($request->page ?? 1, env('PAGINATION', 25))->values(),
            $data->count(),
            env('PAGINATION', 25),
            $request->page ?? 1,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }
}