<?php

namespace App\Services\Master;

use DepartmentTypeData;
use App\Models\Master\DRE;
use Illuminate\Http\Request;
use App\Traits\DREValidation;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;


class DREService implements CRUDContract, FilterContract
{
    use DREValidation;

    private $filter;
    private $columns;
    private $dREService;
    private $listcolumns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Master\DRE $dREService
     */
    public function __construct(CheckValidation $checkValidationService, DRE $dREService)
    {
        $this->filter = DRE::$filter;
        $this->columns = DRE::$columns;
        $this->dREService = $dREService;
        $this->listcolumns = DRE::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  dRE  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        DRE::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  dRE  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        DRE::findOrFail($id)->update($request->all());
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
        DRE::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return DRE
     */
    public function get(string $id): DRE
    {
        return DRE::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $dRE = DRE::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $dRE = $this->search($searchValue, $dRE);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $dRE = $dRE->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $dRE = $this->filterMultipleFields($request->multiple_filter, $dRE);
        }

        return $dRE->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of dreList
     * @param mixed $departmentValue
     */
    public function dreList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return DRE::where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return DRE::where('department_type', $departmentType)->select($this->listcolumns)->get();
    }
}