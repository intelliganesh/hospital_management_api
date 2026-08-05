<?php

namespace App\Services;

use DepartmentTypeData;
use App\Models\DietPlans;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;

use App\Traits\DietPlansValidation;


class DietPlansService implements CRUDContract, FilterContract
{
    use DietPlansValidation;

    private $columns;
    private $listcolumns;
    private $dietPlansService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\DietPlans $dietPlansService
     */
    public function __construct(CheckValidation $checkValidationService, DietPlans $dietPlansService)
    {
        $this->columns = DietPlans::$columns;
        $this->dietPlansService = $dietPlansService;
        $this->listcolumns = DietPlans::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  dietPlans  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        DietPlans::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  dietPlans  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        DietPlans::findOrFail($id)->update($request->all());
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
        DietPlans::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return DietPlans
     */
    public function get(string $id): DietPlans
    {
        return DietPlans::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $dietPlans = DietPlans::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $dietPlans = $this->search($searchValue, $dietPlans);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $dietPlans = $dietPlans->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $dietPlans = $this->filterMultipleFields($request->multiple_filter, $dietPlans);
        }

        return $dietPlans->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of dietPlanList
     * @param mixed $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, DietPlans>
     */
    public function dietPlanList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return DietPlans::where("is_active", ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return DietPlans::where('department_type', $departmentType)->where("is_active", ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
    }

}