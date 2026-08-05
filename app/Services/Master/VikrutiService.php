<?php

namespace App\Services\Master;

use App\Enums\VrikrutiEnum;
use Illuminate\Http\Request;
use App\Models\Master\Vikruti;
use App\Contracts\CRUDContract;
use App\Traits\VikrutiValidation;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;

class VikrutiService implements CRUDContract, FilterContract
{
    use VikrutiValidation;
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
            $data->where('name', 'like', '%' . $request['name'] . '%');
        }
        return $data;
    }

    /**
     *  @deprecated this method is not used 
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     *  @deprecated this method is not used 
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
        Vikruti::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        $vikruti = Vikruti::findOrFail($id);
        $vikruti->update($request->all());
    }

    /**
     *  @deprecated this method is not used 
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
        $vikruti = Vikruti::findOrFail($id);
        $vikruti->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Vikruti|null
     */
    public function get(string $id): mixed
    {
        return Vikruti::find($id);
    }


    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $vikruti = Vikruti::query();

        if ($request->has('search')) {
            $searchValue = $request->search;
            $vikruti = $this->search($searchValue, $vikruti);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $vikruti = $vikruti->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $this->filterMultipleFields($request->multiple_filter, $vikruti);
        }

        return $vikruti->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of list
     * @return array
     */
    public function list()
    {
        return VrikrutiEnum::options();
    }

}