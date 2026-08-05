<?php

namespace App\Services;

use DepartmentTypeData;
use App\Models\FoodAdvice;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\FoodAdviceValidation;


class FoodAdviceService implements CRUDContract, FilterContract
{
    use FoodAdviceValidation;


    private $columns;
    private $listcolumns;
    private $foodAdviceService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\FoodAdvice $foodAdviceService
     */
    public function __construct(CheckValidation $checkValidationService, FoodAdvice $foodAdviceService)
    {
        $this->columns = FoodAdvice::$columns;
        $this->listcolumns = FoodAdvice::$listcolumns;
        $this->foodAdviceService = $foodAdviceService;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  foodAdvice  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        FoodAdvice::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  foodAdvice  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        FoodAdvice::findOrFail($id)->update($request->all());
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
        FoodAdvice::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return FoodAdvice
     */
    public function get(string $id): FoodAdvice
    {
        return FoodAdvice::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $foodAdvice = FoodAdvice::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $foodAdvice = $this->search($searchValue, $foodAdvice);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $foodAdvice = $foodAdvice->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $foodAdvice = $this->filterMultipleFields($request->multiple_filter, $foodAdvice);
        }

        return $foodAdvice->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of foodAdviceList
     * @return \Illuminate\Database\Eloquent\Collection<int, FoodAdvice>
     */
    public function foodAdviceList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return FoodAdvice::where("status", ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return FoodAdvice::where('department_type', $departmentType)->where("status", ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
    }

}