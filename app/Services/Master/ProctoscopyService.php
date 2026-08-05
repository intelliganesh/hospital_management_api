<?php

namespace App\Services\Master;

use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Master\Proctoscopy;

use App\Traits\ProctoscopyValidation;


class ProctoscopyService implements CRUDContract, FilterContract
{
    use ProctoscopyValidation;


    private $filter;
    private $columns;
    private $listcolumns;
    private $proctoscopyService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Proctoscopy $proctoscopyService
     */
    public function __construct(CheckValidation $checkValidationService, Proctoscopy $proctoscopyService)
    {
        $this->filter = Proctoscopy::$filter;
        $this->columns = Proctoscopy::$columns;
        $this->listcolumns = Proctoscopy::$listcolumns;
        $this->proctoscopyService = $proctoscopyService;
        $this->checkValidationService = $checkValidationService;

    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filter as $column) {
            if (!empty($request[$column])) {
                $data->where($column, $request[$column]);
            }
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  proctoscopy  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Proctoscopy::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  proctoscopy  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Proctoscopy::findOrFail($id)->update($request->all());
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
        Proctoscopy::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Proctoscopy
     */
    public function get(string $id): Proctoscopy
    {
        return Proctoscopy::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $proctoscopy = Proctoscopy::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $proctoscopy = $this->search($searchValue, $proctoscopy);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $proctoscopy = $proctoscopy->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $proctoscopy = $this->filterMultipleFields($request->multiple_filter, $proctoscopy);
        }

        return $proctoscopy->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of proctoscopyList
     * @param mixed $departmentValue
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public function proctoscopyList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return Proctoscopy::select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return Proctoscopy::where('department_type', $departmentType)->select($this->listcolumns)->get();
    }

}