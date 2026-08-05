<?php

namespace App\Services;

use App\Models\Fistula;
use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\FistulaValidation;


class FistulaService implements CRUDContract, FilterContract
{
    use FistulaValidation;


    private $filter;
    private $columns;
    private $listcolumns;
    private $fistulaService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->filter = Fistula::$filter;
        $this->columns = Fistula::$columns;
        $this->listcolumns = Fistula::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  fistulaFollowUp  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Fistula::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  fistulaFollowUp  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Fistula::findOrFail($id)->update($request->all());
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
        Fistula::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Fistula
     */
    public function get(string $id): Fistula
    {
        return Fistula::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $fistula = Fistula::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $fistula = $this->search($searchValue, $fistula);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $fistula = $fistula->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $fistula = $this->filterMultipleFields($request->multiple_filter, $fistula);
        }

        return $fistula->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of fistulaList
     * @param string $departmentValue
     */
    public function fistulaList(string $departmentValue)
    {
        // $query = Fistula::selectRaw('
        //     sub_fistula_name,
        //     MAX(id) as id,
        //     MAX(description) as description,
        //     MAX(fistula_name) as fistula_name,
        //     MAX(department_type) as department_type
        // ')
        //     ->where('is_active', ComanStatusEnum::Active->value);
        //     ->groupBy('sub_fistula_name');

        $query = Fistula::selectRaw('
    sub_fistula_name,
    id,
    description,
    fistula_name,
    department_type
')
    ->where('is_active', ComanStatusEnum::Active->value)
    ->groupBy('sub_fistula_name', 'id', 'description', 'fistula_name', 'department_type');

        if ($departmentValue !== "All") {
            $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
            $query->where('department_type', $departmentType);
        }

        return $query->get();
    }
    //sub_fistula_name

}