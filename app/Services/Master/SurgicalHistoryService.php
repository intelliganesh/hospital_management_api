<?php

namespace App\Services\Master;

use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Master\SurgicalHistory;
use App\Traits\SurgicalHistoryValidation;


class SurgicalHistoryService implements CRUDContract, FilterContract
{
    use SurgicalHistoryValidation;

    private $columns;
    private $listcolumns;
    private $checkValidationService;
    private $surgicalHistoryService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Master\SurgicalHistory $surgicalHistoryService
     */
    public function __construct(CheckValidation $checkValidationService, SurgicalHistory $surgicalHistoryService)
    {
        $this->columns = SurgicalHistory::$columns;
        $this->listcolumns = SurgicalHistory::$listcolumns;
        $this->checkValidationService = $checkValidationService;
        $this->surgicalHistoryService = $surgicalHistoryService;

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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  surgicalHistory  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        SurgicalHistory::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  surgicalHistory  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        SurgicalHistory::findOrFail($id)->update($request->all());
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
        SurgicalHistory::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return SurgicalHistory
     */
    public function get(string $id): SurgicalHistory
    {
        return SurgicalHistory::findOrFail($id);
    }


    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $surgicalHistory = SurgicalHistory::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $surgicalHistory = $this->search($searchValue, $surgicalHistory);
        }

        if ($request?->has('sort_by')) {
            $sortOrder = $request->sort_order ?? 'desc';
            $sortBy = $request->sort_by ?? 'surgery_name';
            $surgicalHistory = $surgicalHistory->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $surgicalHistory = $this->filterMultipleFields($request->multiple_filter, $surgicalHistory);
        }

        return $surgicalHistory->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of surgicalHistoryList
     * @param mixed $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, SurgicalHistory>
     */
    public function surgicalHistoryList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return SurgicalHistory::where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return SurgicalHistory::where('is_active', ComanStatusEnum::Active->value)->where('department_type', $departmentType)->select($this->listcolumns)->get();
    }

}