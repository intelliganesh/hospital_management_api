<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;

class FamilyHistoryService implements CRUDContract, FilterContract
{
    public function create(Request $request): void
    {
    }
    public function update(Request $request, string|null $id): void
    {
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
    }

    public function delete(string $id): void
    {
    }

    public function get(string $id): mixed
    {
        return null;
    }

    public function all(?Request $request): mixed
    {
        return [];
    }

    public function search(string $searchText, $data)
    {
    }

    public function filterByDateRange(string $searchText, $data)
    {
    }

    public function sortData(string $searchText, $data)
    {
    }
}