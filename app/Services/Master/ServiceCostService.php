<?php

namespace App\Services\Master;

use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Master\ServiceCost;

use App\Traits\ServiceCostValidation;


class ServiceCostService implements CRUDContract, FilterContract
{
    use ServiceCostValidation;

    private $columns;
    private $listcolumns;
    private $serviceCostService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, ServiceCost $serviceCostService)
    {
        $this->columns = ServiceCost::$columns;
        $this->listcolumns = ServiceCost::$listcolumns;
        $this->serviceCostService = $serviceCostService;
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
        foreach ($this->columns as $column) {
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  serviceCost  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        ServiceCost::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  serviceCost  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        ServiceCost::findOrFail($id)->update($request->all());
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
        ServiceCost::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return ServiceCost
     */
    public function get(string $id): ServiceCost
    {
        return ServiceCost::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $serviceCost = ServiceCost::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $serviceCost = $this->search($searchValue, $serviceCost);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $serviceCost = $serviceCost->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $serviceCost = $this->filterMultipleFields($request->multiple_filter, $serviceCost);
        }

        return $serviceCost->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of serviceCostList
     * @param string $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, ServiceCost>
     */
    public function serviceCostList(string $departmentValue)
    {
        if ($departmentValue == "All") {
            return ServiceCost::where('status', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return ServiceCost::where('department_type', $departmentType)->where('status', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
    }

}