<?php

namespace App\Services;

use DepartmentTypeData;
use App\Models\Management;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;

use App\Traits\ManagementValidation;


class ManagementService implements CRUDContract, FilterContract
{
    use ManagementValidation;


    private $filter;
    private $columns;
    private $listcolumns;
    private $managementService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, Management $managementService)
    {
        $this->filter = Management::$filter;
        $this->columns = Management::$columns;
        $this->listcolumns = Management::$listcolumns;
        $this->checkValidationService = $checkValidationService;
        $this->managementService = $managementService;

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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  management  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Management::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  management  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Management::findOrFail($id)->update($request->all());
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
        Management::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Management
     */
    public function get(string $id): Management
    {
        return Management::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $management = Management::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $management = $this->search($searchValue, $management);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $management = $management->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $management = $this->filterMultipleFields($request->multiple_filter, $management);
        }

        return $management->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of managementList
     * @param string $departmentValue
     */
    public function managementList(string $departmentValue)
    {
        if ($departmentValue == "All") {
            return Management::where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return Management::where('is_active', ComanStatusEnum::Active->value)->where('department_type', $departmentType)->select($this->listcolumns)->get();
    }

}