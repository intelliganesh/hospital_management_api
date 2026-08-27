<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPDAnaesthesia;
use App\Models\IPD;
use App\Models\IPDPreliminaryNotes;
use App\Models\IPDAnaesthesiaDepartment;
use App\Models\IPDAnaesthesiaRecoverObservation;
use App\Models\IPDDischargeSummary;
use App\Models\IPDPreOperativeAnaesthesiaEvaluation;
use App\Models\IPDPreOperativeChecklist;
use App\Models\IPDSurgery;
use App\Services\CheckValidation;
use App\Traits\IPDSurgeryValidation;
use Illuminate\Http\Request;

class IPDSurgeryService implements CRUDContract, FilterContract
{
    use IPDSurgeryValidation;

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
        $this->filter                 = IPDSurgery::$filter;
        $this->columns                = IPDSurgery::$columns;
        $this->listcolumns            = IPDSurgery::$listcolumns;
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
                // For date columns, filter by date only (YYYY-MM-DD format)
                if ($column === 'surgery_date') {
                    $data->whereDate($column, $request[$column]);
                } elseif ($column === 'surgery_start_datetime') {
                    $data->whereDate($column, $request[$column]);
                } elseif ($column === 'surgery_end_datetime') {
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
     * Handle file upload for PDF
     */
    private function handleFileUpload(Request $request, string $field_name, $file_name): ?string
    {
        if (! $request->hasFile($field_name)) {
            return null;
        }

        $file = $request->file($field_name);
        if (! $file->isValid()) {
            return null;
        }

        $ipd = IPD::find($request->ipd_id);
        if (! $ipd) {
            return null;
        }
        // Store file in public/uploads/ipd_pre_operative_checklist directory
        $filePath = $file->store("app/public/pdfs/ipd/{$ipd->ipd_number}/uploads/{$file_name}_{$ipd->ipd_number}_" . str_replace(['.', ' '], '_', $file->getClientOriginalName()) . ".pdf", 'storage');
        return $filePath;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD surgery record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $data = $request->all();

        // Handle file upload
        $filePath = $this->handleFileUpload($request, 'uploaded_report_path', 'surgery_report');
        if ($filePath) {
            $data['uploaded_report_path'] = $filePath;
        }

        $surgery = IPDSurgery::create($data);
        $preliminaryNotes = IPDPreliminaryNotes::where('ipd_id', $request->ipd_id)->first();

        $anaesthesia = IPDAnaesthesia::create([
            'ipd_id'         => $request->ipd_id,
            'ipd_surgery_id' => $surgery->id,
            'diagnosis'       => $preliminaryNotes?->final_diagnosis ?? "-",
        ]);

        IPDPreOperativeAnaesthesiaEvaluation::create([
            'ipd_id'             => $request->ipd_id,
            'ipd_surgery_id'     => $surgery->id,
            'ipd_anaesthesia_id' => $anaesthesia->id,
        ]);

        IPDPreOperativeChecklist::create([
            'ipd_id'         => $request->ipd_id,
            'ipd_surgery_id' => $surgery->id,
        ]);

        IPDAnaesthesiaDepartment::create([
            'ipd_id'             => $request->ipd_id,
            'ipd_surgery_id'     => $surgery->id,
            'ipd_anaesthesia_id' => $anaesthesia->id,
        ]);
        IPDAnaesthesiaRecoverObservation::create([
            'ipd_id'             => $request->ipd_id,
            'ipd_surgery_id'     => $surgery->id,
            'ipd_anaesthesia_id' => $anaesthesia->id,
        ]);
        IPDDischargeSummary::create([
            'ipd_id'         => $request->ipd_id,
            'ipd_surgery_id' => $surgery->id,
        ]);
        IPDPreliminaryNotes::create([
            'ipd_id'         => $request->ipd_id,
            'ipd_surgery_id' => $surgery->id,
        ]);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update IPD surgery record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $data    = $request->all();
        $surgery = IPDSurgery::findOrFail($id);

        // Handle file upload
        $filePath = $this->handleFileUpload($request, 'uploaded_report_path', 'surgery_report');
        if ($filePath) {

            $data['uploaded_report_path'] = $filePath;
        }

        $surgery->update($data);
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
        IPDSurgery::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return IPDSurgery
     */
    public function get(string $id): IPDSurgery
    {
        return IPDSurgery::findOrFail($id);
    }

    public function all(?Request $request): mixed
    {
        $surgery = IPDSurgery::query()->orderBy('surgery_date', 'desc');

        if ($request?->has('ipd_id')) {
            $surgery = $surgery->where('ipd_id', $request->ipd_id);
        }

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $surgery     = $this->search($searchValue, $surgery);
        }

        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $surgery   = $surgery->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('multiple_filter')) {
            $surgery = $this->filterMultipleFields($request->multiple_filter, $surgery);
        }

        $perPage = $request?->per_page ?? 10;
        return $surgery->paginate($perPage);
    }

    public function updateConsentDetails(Request $request, string | null $id): void
    {
        $surgery = IPDSurgery::findOrFail($id);
        $data    = $request->only(['consent_summary']);

        // Handle consent file upload
        $filePath = $this->handleFileUpload($request, 'uploaded_consent_path', 'surgery_consent_form');
        if ($filePath) {

            $data['uploaded_consent_path'] = $filePath;
        }
        $surgery->update($data);
    }

    /**
     * Get all surgery records for a particular IPD
     * @param string $ipd_id
     * @return mixed
     */
    public function getByIPDId(string $ipd_id): mixed
    {
        return IPDSurgery::where('ipd_id', $ipd_id)
            ->orderBy('surgery_date', 'desc')
            ->select($this->listcolumns)
            ->get();
    }
}
