<?php

namespace App\Services\Master;

use DepartmentTypeData;
use Illuminate\Http\Request;
use App\Enums\ComanStatusEnum;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Models\Master\ChiefComplaint;
use App\Traits\ChiefComplaintValidation;


class ChiefComplaintService implements CRUDContract, FilterContract
{
    use ChiefComplaintValidation;

    private $columns;
    private $listcolumns;
    private $chiefComplaintService;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\Master\ChiefComplaint $chiefComplaintService
     */
    public function __construct(CheckValidation $checkValidationService, ChiefComplaint $chiefComplaintService)
    {
        $this->columns = ChiefComplaint::$columns;
        $this->listcolumns = ChiefComplaint::$listcolumns;
        $this->chiefComplaintService = $chiefComplaintService;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  chiefComplaint  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        ChiefComplaint::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  chiefComplaint  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true,$id));
        ChiefComplaint::findOrFail($id)->update($request->all());
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
        ChiefComplaint::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return ChiefComplaint
     */
    public function get(string $id): ChiefComplaint
    {
        return ChiefComplaint::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $chiefComplaint = ChiefComplaint::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $chiefComplaint = $this->search($searchValue, $chiefComplaint);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $chiefComplaint = $chiefComplaint->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $chiefComplaint = $this->filterMultipleFields($request->multiple_filter, $chiefComplaint);
        }

        return $chiefComplaint->select($this->columns)->paginate(env('PAGINATION', 25));
    }


    /**
     * Summary of chiefComplaintList
     * @param mixed $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, ChiefComplaint>
     */
    public function chiefComplaintList(?string $departmentValue)
    {
        if ($departmentValue == "All") {
            return ChiefComplaint::where('is_active', ComanStatusEnum::Active->value)->select($this->listcolumns)->get();
        }
        $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        return ChiefComplaint::where('is_active', ComanStatusEnum::Active->value)->where('department_type', $departmentType)->select($this->listcolumns)->get();
    }

}