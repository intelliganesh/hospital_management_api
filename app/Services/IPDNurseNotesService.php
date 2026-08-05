<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPDNurseNotes;
use App\Models\User;
use App\Services\CheckValidation;
use App\Traits\NurseNotesValidation;
use Illuminate\Http\Request;

class IPDNurseNotesService implements CRUDContract, FilterContract
{
    use NurseNotesValidation;

    private $filter;
    private $columns;
    private $listcolumns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->filter                 = IPDNurseNotes::$filter;
        $this->columns                = IPDNurseNotes::$columns;
        $this->listcolumns            = IPDNurseNotes::$listcolumns;
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
            if (! empty($request[$column])) {
                // For datetime columns, filter by date only (YYYY-MM-DD format)
                if ($column === 'datetime') {
                    $data->whereDate($column, $request[$column]);
                } else {
                    $data->where($column, $request[$column]);
                }
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create nurse notes record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));

        $nurse = User::find($request->nurse_id);
        $data  = $request->all();
        if ($nurse) {
            $data['nurse_name']  = $nurse->name;
            $data['nurse_phone'] = $nurse->phone;
        }

        IPDNurseNotes::create($data);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update nurse notes record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));

        $data = $request->all();
        if ($request->has('nurse_id')) {
            $nurse = User::find($request->nurse_id);
            if ($nurse) {
                $data['nurse_name']  = $nurse->name;
                $data['nurse_phone'] = $nurse->phone;
            }
        }

        IPDNurseNotes::findOrFail($id)->update($data);
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
        IPDNurseNotes::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return IPDNurseNotes
     */
    public function get(string $id): IPDNurseNotes
    {
        return IPDNurseNotes::with('nurse:id,name,phone,email')->findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $nurseNotes = IPDNurseNotes::query()->with('nurse:id,name,phone,email')->orderBy('datetime', 'desc');

        if ($request?->has('ipd_id')) {
            $nurseNotes = $nurseNotes->where('ipd_id', $request->ipd_id);
        }

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $nurseNotes  = $this->search($searchValue, $nurseNotes);
        }

        if ($request?->has('sort_by')) {
            $sortBy     = $request->sort_by ?? '';
            $sortOrder  = $request->sort_order ?? 'desc';
            $nurseNotes = $nurseNotes->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('multiple_filter')) {
            $nurseNotes = $this->filterMultipleFields($request->multiple_filter, $nurseNotes);
        }

        $perPage = $request?->per_page ?? 10;
        return $nurseNotes->paginate($perPage);
    }
}
