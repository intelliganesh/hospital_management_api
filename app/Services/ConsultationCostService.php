<?php

namespace App\Services;

use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\ConsultationCost;

use App\Traits\ConsultationCostValidation;


class ConsultationCostService implements CRUDContract, FilterContract
{
    use ConsultationCostValidation;

    private $columns;
    private $listcolumns;
    private $checkValidationService;
    private $consultationCostService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\ConsultationCost $consultationCostService
     */
    public function __construct(CheckValidation $checkValidationService, ConsultationCost $consultationCostService)
    {
        $this->columns = ConsultationCost::$columns;
        $this->listcolumns = ConsultationCost::$listcolumns;
        $this->checkValidationService = $checkValidationService;
        $this->consultationCostService = $consultationCostService;

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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  consultationCost  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        ConsultationCost::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  consultationCost  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        ConsultationCost::findOrFail($id)->update($request->all());
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
        ConsultationCost::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return ConsultationCost
     */
    public function get(string $id): ConsultationCost
    {
        return ConsultationCost::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $consultationCost = ConsultationCost::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $consultationCost = $this->search($searchValue, $consultationCost);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $consultationCost = $consultationCost->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $consultationCost = $this->filterMultipleFields($request->multiple_filter, $consultationCost);
        }

        return $consultationCost->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of consultationCostList
     * @param mixed $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, ConsultationCost>
     */
    public function consultationCostList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return ConsultationCost::where('status', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return ConsultationCost::where('department_type', $departmentType)->where('status', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
    }

}