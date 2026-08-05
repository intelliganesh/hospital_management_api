<?php

namespace App\Services\Master;

use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Master\Comorbidities;

use App\Traits\ComorbiditiesValidation;


class ComorbiditiesService implements CRUDContract, FilterContract
{
    use ComorbiditiesValidation;

    private $columns;
    private $listcolumns;
    private $comorbiditiesService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Master\Comorbidities $comorbiditiesService
     */
    public function __construct(CheckValidation $checkValidationService, Comorbidities $comorbiditiesService)
    {
        $this->columns = Comorbidities::$columns;
        $this->listcolumns = Comorbidities::$listcolumns;
        $this->comorbiditiesService = $comorbiditiesService;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  comorbidities  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Comorbidities::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  comorbidities  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Comorbidities::findOrFail($id)->update($request->all());
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
        Comorbidities::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Comorbidities
     */
    public function get(string $id): Comorbidities
    {
        return Comorbidities::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $comorbidities = Comorbidities::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $comorbidities = $this->search($searchValue, $comorbidities);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $comorbidities = $comorbidities->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $comorbidities = $this->filterMultipleFields($request->multiple_filter, $comorbidities);
        }

        return $comorbidities->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of comorbiditiesList
     * @return \Illuminate\Database\Eloquent\Collection<int, Comorbidities>
     */
    public function comorbiditiesList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return Comorbidities::where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return Comorbidities::where('department_type', $departmentType)->where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
    }

}