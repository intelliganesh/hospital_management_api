<?php

namespace App\Services\Master;

use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Models\Master\YogaAsana;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\YogaAsanaValidation;

class YogaAsanaService implements CRUDContract, FilterContract
{

    use YogaAsanaValidation;

    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = YogaAsana::$columns;
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('asana_name', 'like', '%' . $searchText . '%')->orWhere('status', 'like', '%' . $searchText . '%')->orWhere('difficulty_level', 'like', '%' . $searchText . '%');
    }

    public function filterMultipleFields($request, $data)
    {
        if (isset($request['asana_name']) && $request['asana_name'] != null) {
            $data = $data->where('asana_name', 'like', '%' . $request['asana_name'] . '%');
        }
        if (isset($request['status']) && $request['status'] != null) {
            $data = $data->where('status', 'like', '%' . $request['status'] . '%');
        }
        if (isset($request['difficulty_level']) && $request['difficulty_level'] != null) {
            $data = $data->where('difficulty_level', 'like', '%' . $request['difficulty_level'] . '%');
        }

        return $data;
    }

    /**
     * @deprecated this function is not in use
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated this function is not in use
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
        YogaAsana::create($request->all());
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
        $yogaAsana = YogaAsana::findOrFail($id);
        $yogaAsana->update($request->all());
    }

    /**
     * @deprecated this function is not in use
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
        $yogaAsana = YogaAsana::findOrFail($id);
        $yogaAsana->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return YogaAsana
     */
    public function get(string $id): YogaAsana
    {
        return YogaAsana::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $yogaAsana = YogaAsana::query();

        if ($request->has('search')) {
            $searchValue = $request->search;
            $yogaAsana = $this->search($searchValue, $yogaAsana);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'asana_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $yogaAsana = $yogaAsana->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $yogaAsana = $this->filterMultipleFields($request->multiple_filter, $yogaAsana);
        }

        return $yogaAsana->select($this->columns)->paginate(env('PAGINATION', 25));

    }


    /**
     * Summary of optionsList
     * @return \Illuminate\Database\Eloquent\Collection<int, YogaAsana>
     */
    public function optionsList()
    {
        return YogaAsana::get();
    }

}