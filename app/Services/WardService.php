<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\Ward;
use App\Services\CheckValidation;
use App\Traits\WardValidation;
use Illuminate\Http\Request;

class WardService implements CRUDContract, FilterContract
{
    use WardValidation;

    private $columns;
    private $wardService;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, Ward $wardService)
    {
        $this->columns                = ward::$columns;
        $this->wardService            = $wardService;
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
            if (! empty($request[$column])) {
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create  ward  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        // $wardNewColumn = [
        //   'ward_number' => AutoIdGenerateFacade::generateId(ServiceType::Ward),
        // ];
        Ward::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  ward  record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        Ward::findOrFail($id)->update($request->all());
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
        Ward::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Ward
     */
    public function get(string $id): Ward
    {
        return Ward::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $ward = Ward::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $ward        = $this->search($searchValue, $ward);
        }

        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $ward      = $ward->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $ward = $this->filterMultipleFields($request->multiple_filter, $ward);
        }

        return $ward->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    public function listForDropdown()
    {
        return Ward::select('name', 'id', 'type')->get();
    }

}
