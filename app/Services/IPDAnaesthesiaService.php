<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPDAnaesthesia;
use App\Services\CheckValidation;
use App\Traits\IPDAnaesthesiaValidation;
use App\Models\IPDPreliminaryNotes;
use Illuminate\Http\Request;

class IPDAnaesthesiaService implements CRUDContract, FilterContract
{
    use IPDAnaesthesiaValidation;

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
        $this->filter                 = IPDAnaesthesia::$filter;
        $this->columns                = IPDAnaesthesia::$columns;
        $this->listcolumns            = IPDAnaesthesia::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD anaesthesia record within a secure transaction')]
    public function create(Request $request)
    {
        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesia($request));

        $exists = IPDAnaesthesia::where('ipd_surgery_id', $data['ipd_surgery_id'])
            ->where('ipd_id', $data['ipd_id'])
            ->first();
            
        if (!$exists) {
            $data = $request->all();
            $preliminaryNotes = IPDPreliminaryNotes::where('ipd_id', $request->ipd_id)->first();
            $data['diagnosis'] = $preliminaryNotes?->diagnosis ?? "-";
            IPDAnaesthesia::create($data);
        }else{
            throw new Exception('Pre-Anaesthesia Assessments already exist for this Surgery. Only one Pre-Anaesthesia Assessments is allowed per Surgery.');
        }
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update IPD anaesthesia record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $anaesthesia = IPDAnaesthesia::findOrFail($id);
        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesia($request, true, $id));

        $data = $request->all();

        $anaesthesia->update($data);
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
        IPDAnaesthesia::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return IPDAnesthesia
     */
    public function get(string $id): IPDAnaesthesia
    {
        return IPDAnaesthesia::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'ipd.patient:id,gender',
            'surgery:id,ipd_id,surgery_name,assistant_surgeon,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime,anaesthetist',
        ])->findOrFail($id);
    }

    /**
     * Summary of all
     * @param \Illuminate\Http\Request|null $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $anaesthesia = IPDAnaesthesia::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'ipd.patient:id,gender',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime,anaesthetist',
        ])->where('ipd_id', $request->ipd_id)->orderBy('created_at', 'desc');

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $anaesthesia = $this->search($searchValue, $anaesthesia);
        }

        if ($request?->has('filter')) {
            $filterValue = $request->filter;
            $anaesthesia = $this->filterMultipleFields($filterValue, $anaesthesia);
        }

        if ($request?->has('sort_by')) {
            $sortBy      = $request->sort_by ?? '';
            $sortOrder   = $request->sort_order ?? 'desc';
            $anaesthesia = $anaesthesia->orderBy($sortBy, $sortOrder);
        }

        return $anaesthesia->paginate($request?->per_page ?? 10);
    }
}
