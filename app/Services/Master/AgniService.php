<?php

namespace App\Services\Master;

use App\Enums\AgniEnum;
use App\Models\Master\Agni;
use Illuminate\Http\Request;
use App\Traits\AgniValidation;
use App\Contracts\CRUDContract;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;

class AgniService implements CRUDContract, FilterContract
{

    use AgniValidation;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('name', 'like', '%' . $searchText . '%');
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['name']) && $request['name'] != null && $request['name'] != '') {
            $data = $data->where('name', 'like', '%' . $request['name'] . '%');
        }
        return $data;
    }

    /**
     * @deprecated this method is not used
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated this method is not used
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Agni::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $agani = Agni::findOrFail($id);
        $agani->update($request->all());
    }

    /**
     * @deprecated this method is not used
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $agni = Agni::findOrFail($id);
        $agni->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Agni
     */
    public function get(string $id): Agni
    {
        return Agni::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $agni = Agni::query();
        if ($request->has('search')) {
            $searchValue = $request->search;
            $agni = $this->search($searchValue, $agni);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $agni = $agni->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $agni = $this->filterMultipleFields($request->multiple_filter, $agni);
        }

        return $agni->paginate(env('PAGINATION', 25));

    }

    /**
     * Summary of list
     * @return array
     */
    public function list()
    {
        return AgniEnum::options();
    }

}