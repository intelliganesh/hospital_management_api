<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPDDoctorNotes;
use App\Models\User;
use App\Services\CheckValidation;
use App\Traits\DoctorNotesValidation;
use Illuminate\Http\Request;

class IPDDoctorNotesService implements CRUDContract, FilterContract
{
    use DoctorNotesValidation;

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
        $this->filter                 = IPDDoctorNotes::$filter;
        $this->columns                = IPDDoctorNotes::$columns;
        $this->listcolumns            = IPDDoctorNotes::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create doctor notes record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));

        $doctor = User::find($request->doctor_id);
        $data   = $request->all();
        if ($doctor) {
            $data['doctor_name']  = $doctor->name;
            $data['doctor_phone'] = $doctor->phone;
        }

        IPDDoctorNotes::create($data);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update doctor notes record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));

        $data = $request->all();
        if ($request->has('doctor_id')) {
            $doctor = User::find($request->doctor_id);
            if ($doctor) {
                $data['doctor_name']  = $doctor->name;
                $data['doctor_phone'] = $doctor->phone;
            }
        }

        IPDDoctorNotes::findOrFail($id)->update($data);
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
        IPDDoctorNotes::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return DoctorNotes
     */
    public function get(string $id): IPDDoctorNotes
    {
        return IPDDoctorNotes::with('doctor:id,name,phone,email')->findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $doctorNotes = IPDDoctorNotes::query()->with('doctor:id,name,phone,email')->orderBy('datetime', 'desc');

        if ($request?->has('ipd_id')) {
            $doctorNotes = $doctorNotes->where('ipd_id', $request->ipd_id);
        }

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $doctorNotes = $this->search($searchValue, $doctorNotes);
        }

        if ($request?->has('sort_by')) {
            $sortBy      = $request->sort_by ?? '';
            $sortOrder   = $request->sort_order ?? 'desc';
            $doctorNotes = $doctorNotes->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('multiple_filter')) {
            $doctorNotes = $this->filterMultipleFields($request->multiple_filter, $doctorNotes);
        }

        $perPage = $request?->per_page ?? 10;
        return $doctorNotes->paginate($perPage);
    }
}
