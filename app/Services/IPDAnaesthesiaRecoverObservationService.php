<?php

namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPDAnaesthesiaRecoverObservation;
use App\Services\CheckValidation;
use App\Traits\IPDAnaesthesiaRecoverObservationValidation;
use Illuminate\Http\Request;

class IPDAnaesthesiaRecoverObservationService implements CRUDContract, FilterContract
{
    use IPDAnaesthesiaRecoverObservationValidation;

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
        $this->filter                 = IPDAnaesthesiaRecoverObservation::$filter;
        $this->columns                = IPDAnaesthesiaRecoverObservation::$columns;
        $this->listcolumns            = IPDAnaesthesiaRecoverObservation::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD anaesthesia recovery observation record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesiaRecoverObservation($request));

        $data = $request->all();

        // Enforce uniqueness for ipd_surgery_id and ipd_anaesthesia_id
        $exists = IPDAnaesthesiaRecoverObservation::where('ipd_surgery_id', $data['ipd_surgery_id'])
            ->orWhere('ipd_anaesthesia_id', $data['ipd_anaesthesia_id'])
            ->first();
        if ($exists) {
            $this->update($request, $exists->id);
        }

        IPDAnaesthesiaRecoverObservation::create($data);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update IPD anaesthesia recovery observation record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        // Try to find by primary id first
        $recovery = IPDAnaesthesiaRecoverObservation::find($id);
        if (! $recovery) {
            // Try by ipd_surgery_id
            $recovery = IPDAnaesthesiaRecoverObservation::where('ipd_surgery_id', $id)->first();
        }
        if (! $recovery) {
            // Try by ipd_anaesthesia_id
            $recovery = IPDAnaesthesiaRecoverObservation::where('ipd_anaesthesia_id', $id)->first();
        }
        if (! $recovery) {
            abort(404, 'Record not found');
        }

        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesiaRecoverObservation($request, true, $recovery->id));

        $data = $request->all();

        $recovery->update($data);
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
        IPDAnaesthesiaRecoverObservation::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id - Can be primary ID, ipd_anaesthesia_id, or ipd_surgery_id
     * @return IPDAnaesthesiaRecoverObservation
     */
    public function get(string $id): IPDAnaesthesiaRecoverObservation
    {
        // Try to find by primary id first
        $record = IPDAnaesthesiaRecoverObservation::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime',
            'anaesthesia:id,ipd_id,ipd_surgery_id,diagnosis,position,anaesthetist_assistant'
        ])->find($id);
        if ($record) {
            return $record;
        }

        // Try by ipd_anaesthesia_id
        $record = IPDAnaesthesiaRecoverObservation::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime',
            'anaesthesia:id,ipd_id,ipd_surgery_id,diagnosis,position,anaesthetist_assistant'
        ])->where('ipd_anaesthesia_id', $id)->first();
        if ($record) {
            return $record;
        }

        // Try by ipd_surgery_id
        $record = IPDAnaesthesiaRecoverObservation::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime',
            'anaesthesia:id,ipd_id,ipd_surgery_id,diagnosis,position,anaesthetist_assistant'
        ])->where('ipd_surgery_id', $id)->first();
        if ($record) {
            return $record;
        }

        abort(404, 'Record not found');
    }

    /**
     * Summary of all
     * @param \Illuminate\Http\Request|null $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $recovery = IPDAnaesthesiaRecoverObservation::with([
            'ipd:id,ipd_number,patient_id,patient_name',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date',
            'anaesthesia:id,ipd_id,ipd_surgery_id'
        ])->orderBy('created_at', 'desc');

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $recovery = $this->search($searchValue, $recovery);
        }

        if ($request?->has('filter')) {
            $filterValue = $request->filter;
            $recovery = $this->filterMultipleFields($filterValue, $recovery);
        }

        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $recovery = $recovery->orderBy($sortBy, $sortOrder);
        }

        return $recovery->paginate($request?->per_page ?? 10);
    }

    /**
     * Get all recovery observation records for a particular IPD
     * @param string $ipd_id
     * @return mixed
     */
    public function getByIPDId(string $ipd_id): mixed
    {
        return IPDAnaesthesiaRecoverObservation::where('ipd_id', $ipd_id)
            ->with([
                'ipd:id,ipd_number,patient_id,patient_name',
                'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date',
                'anaesthesia:id,ipd_id,ipd_surgery_id'
            ])
            ->orderBy('created_at', 'desc')
            ->select($this->listcolumns)
            ->get();
    }
}
