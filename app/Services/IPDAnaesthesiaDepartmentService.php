<?php

namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPDAnaesthesiaDepartment;
use App\Services\CheckValidation;
use App\Traits\IPDAnaesthesiaDepartmentValidation;
use Illuminate\Http\Request;

class IPDAnaesthesiaDepartmentService implements CRUDContract, FilterContract
{
    use IPDAnaesthesiaDepartmentValidation;

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
        $this->filter                 = IPDAnaesthesiaDepartment::$filter;
        $this->columns                = IPDAnaesthesiaDepartment::$columns;
        $this->listcolumns            = IPDAnaesthesiaDepartment::$listcolumns;
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
    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD anaesthesia department record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesiaDepartment($request));

        $data = $request->all();

        // Enforce uniqueness for ipd_surgery_id and ipd_anaesthesia_id
        $exists = IPDAnaesthesiaDepartment::where('ipd_surgery_id', $data['ipd_surgery_id'])
            ->orWhere('ipd_anaesthesia_id', $data['ipd_anaesthesia_id'])
            ->first();
        if ($exists) {
            $this->update($request, $exists->id);
        }else{
                // Handle file upload
            $filePath = $this->handleFileUpload($request);
            if ($filePath) {
                $data['upload_pdf_path'] = $filePath;
            }

            IPDAnaesthesiaDepartment::create($data);
        }
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update IPD anaesthesia department record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        // Try to find by primary id first
        $anaesthesiaDept = IPDAnaesthesiaDepartment::find($id);
        if (! $anaesthesiaDept) {
            // Try by ipd_surgery_id
            $anaesthesiaDept = IPDAnaesthesiaDepartment::where('ipd_surgery_id', $id)->first();
        }
        if (! $anaesthesiaDept) {
            // Try by ipd_anaesthesia_id
            $anaesthesiaDept = IPDAnaesthesiaDepartment::where('ipd_anaesthesia_id', $id)->first();
        }
        if (! $anaesthesiaDept) {
            abort(404, 'Record not found');
        }

        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesiaDepartment($request, true, $anaesthesiaDept->id));

        $data = $request->all();

        $filePath = $this->handleFileUpload($request);
            if ($filePath) {
                // Delete old file if exists
                if ($anaesthesiaDept->upload_pdf_path && Storage::disk('public')->exists($anaesthesiaDept->upload_pdf_path)) {
                    Storage::disk('public')->delete($anaesthesiaDept->upload_pdf_path);
                }
                $data['upload_pdf_path'] = $filePath;
            }

        $anaesthesiaDept->update($data);
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
        IPDAnaesthesiaDepartment::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id - Can be primary ID, ipd_anaesthesia_id, or ipd_surgery_id
     * @return IPDAnaesthesiaDepartment
     */
    public function get(string $id): IPDAnaesthesiaDepartment
    {
        // Try to find by primary id first
        $record = IPDAnaesthesiaDepartment::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime,anaesthetist',
            'anaesthesia:id,ipd_id,ipd_surgery_id,diagnosis,position,anaesthetist_assistant'
        ])->find($id);
        if ($record) {
            return $record;
        }

        // Try by ipd_anaesthesia_id
        $record = IPDAnaesthesiaDepartment::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime,anaesthetist',
            'anaesthesia:id,ipd_id,ipd_surgery_id,diagnosis,position,anaesthetist_assistant'
        ])->where('ipd_anaesthesia_id', $id)->first();
        if ($record) {
            return $record;
        }

        // Try by ipd_surgery_id
        $record = IPDAnaesthesiaDepartment::with([
            'ipd:id,ipd_number,patient_id,patient_name,patient_email,patient_phone,patient_age,patient_address,patient_attendant_name,patient_attendant_phone',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date,surgeon,department,status,surgery_start_datetime,surgery_end_datetime,anaesthetist',
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
        $anaesthesiaDept = IPDAnaesthesiaDepartment::with([
            'ipd:id,ipd_number,patient_id,patient_name',
            'surgery:id,ipd_id,surgery_name,surgery_type,surgery_date',
            'anaesthesia:id,ipd_id,ipd_surgery_id'
        ])->orderBy('created_at', 'desc');

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $anaesthesiaDept = $this->search($searchValue, $anaesthesiaDept);
        }

        if ($request?->has('filter')) {
            $filterValue = $request->filter;
            $anaesthesiaDept = $this->filterMultipleFields($filterValue, $anaesthesiaDept);
        }

        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $anaesthesiaDept = $anaesthesiaDept->orderBy($sortBy, $sortOrder);
        }

        return $anaesthesiaDept->paginate($request?->per_page ?? 10);
    }

    /**
     * Get all anaesthesia department records for a particular IPD
     * @param string $ipd_id
     * @return mixed
     */
    public function getByIPDId(string $ipd_id): mixed
    {
        return IPDAnaesthesiaDepartment::where('ipd_id', $ipd_id)
            ->with([
                'ipd:id,ipd_number,patient_name',
                'surgery:id,surgery_name,surgery_date',
                'anaesthesia:id,diagnosis'
            ])
            ->orderBy('created_at', 'desc')
            ->select($this->listcolumns)
            ->get();
    }

    /**
     * Handle file upload for PDF
     */
    private function handleFileUpload(Request $request): ?string
    {
        if (! $request->hasFile('upload_pdf_path')) {
            return null;
        }

        $file = $request->file('upload_pdf_path');
        if (! $file->isValid()) {
            return null;
        }

        $ipd = IPD::find($request->ipd_id);
        if (! $ipd) {
            return null;
        }
        // Store file in public/uploads/ipd_anaesthesia_recover_room_observation directory
        $filePath = $file->store("app/public/pdfs/ipd/{$ipd->ipd_number}/uploads/anaesthesia_recover_room_observation_{$ipd->ipd_number}_" . str_replace(['.', ' '], '_', $file->getClientOriginalName()) . ".pdf", 'storage');
        return $filePath;
    }
}
