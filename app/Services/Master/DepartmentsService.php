<?php
namespace App\Services\Master;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\Master\Department;
use App\Services\CheckValidation;
use App\Traits\DepartmentsValidation;
use Illuminate\Http\Request;

class DepartmentsService implements CRUDContract, FilterContract
{

    use DepartmentsValidation;
    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns                = Department::$columns;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        $data->where(function ($query) use ($searchText) {
            foreach ($this->columns as $column) {
                $query->orWhere($column, 'like', '%' . $searchText . '%');
            }
        });

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
            if (! empty($request[$column])) {
                $data->where($column, $request[$column]);
            }
        }
        return $data;

        // if (isset($request['name']) && $request['name'] != null && $request['name'] != '') {
        //     $data = $data->where('name', 'like', '%' . $request['name'] . '%');
        // }
        // if (isset($request['code']) && $request['code'] != null && $request['code'] != '') {
        //     $data = $data->where('code', 'like', '%' . $request['code'] . '%');
        // }

        // if (isset($request['is_active']) && $request['is_active'] != null && $request['is_active'] != '') {
        //     $data = $data->where('is_active', 'like', '%' . $request['is_active'] . '%');
        // }
        // return $data;
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
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Department::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $department = Department::findOrFail($id);
        $department->update($request->all());
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
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
        Department::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Department
     */
    public function get(string $id): Department
    {
        $department = Department::findOrFail($id);
        return $department;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $department = Department::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $department  = $this->search($searchValue, $department);
        }

        if ($request?->has('sort_by')) {
            $sortBy     = $request->sort_by ?? 'name';
            $sortOrder  = $request->sort_order ?? 'desc';
            $department = $department->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $department = $this->filterMultipleFields($request->multiple_filter, $department);
        }

        return $department->select($this->columns)->paginate(env('PAGINATION', 25));

    }

    /**
     * Summary of departmentList
     * @return \Illuminate\Database\Eloquent\Collection<int, Department>
     */
    public function departmentList()
    {
        return Department::where('is_active', 1)->select('id', 'name', 'code', 'description')->get();
    }
}
