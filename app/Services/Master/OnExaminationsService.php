<?php

namespace App\Services\Master;

use App\Enums\ComanStatusEnum;
use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Master\OnExaminations;
use App\Traits\OnExaminationsValidation;


class OnExaminationsService implements CRUDContract, FilterContract
{
    use OnExaminationsValidation;

    private $columns;
    private $listcolumns;
    private $onExaminationsService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Master\OnExaminations $onExaminationsService
     */
    public function __construct(CheckValidation $checkValidationService, OnExaminations $onExaminationsService)
    {
        $this->columns = OnExaminations::$columns;
        $this->listcolumns = OnExaminations::$listcolumns;
        $this->onExaminationsService = $onExaminationsService;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  onExaminations  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        OnExaminations::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  onExaminations  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true,$id));
        OnExaminations::findOrFail($id)->update($request->all());
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
        OnExaminations::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return OnExaminations
     */
    public function get(string $id): OnExaminations
    {
        return OnExaminations::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $onExaminations = OnExaminations::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $onExaminations = $this->search($searchValue, $onExaminations);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $onExaminations = $onExaminations->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $onExaminations = $this->filterMultipleFields($request->multiple_filter, $onExaminations);
        }

        return $onExaminations->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of onExaminationList
     * @param mixed $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, OnExaminations>
     */
    public function onExaminationList(string $departmentValue)
    {
        if ($departmentValue == "All") {
            return OnExaminations::where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return OnExaminations::where('department_type', $departmentType)->where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
    }

}