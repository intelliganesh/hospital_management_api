<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPD;
use App\Models\IPDDischargeSummary;
use App\Traits\IPDDischargeSummaryValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IPDDischargeSummaryService implements CRUDContract, FilterContract
{
    use IPDDischargeSummaryValidation;

    private $filter;
    private $columns;
    private $listcolumns;
    private $checkValidationService;

    public function __construct(CheckValidation $checkValidationService)
    {
        $this->filter                 = IPDDischargeSummary::$filter;
        $this->columns                = IPDDischargeSummary::$columns;
        $this->listcolumns            = IPDDischargeSummary::$listcolumns;
        $this->checkValidationService = $checkValidationService;
    }

    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

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

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName     = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        $fileName     = 'discharge_summary_' . $ipd->ipd_number . '_' . time() . '_' . $safeName . '.pdf';

        return $file->storeAs("pdfs/ipd/{$ipd->ipd_number}/uploads", $fileName, 'public');
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD discharge summary within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validateDischargeSummary($request));

        $data     = $request->all();
        


        $this->checkValidationService->checkValidation($this->validateIPDAnaesthesiaRecoverObservation($request));

        $data = $request->all();

        // Enforce uniqueness for ipd_id 
        $exists = IPDDischargeSummary::where('ipd_id', $data['ipd_id'])
            ->first();

        if ($exists) {
            $this->update($request, $exists->id);
        }else{
            $filePath = $this->handleFileUpload($request);
            if ($filePath) {
                $data['upload_pdf_path'] = $filePath;
            }

            IPDDischargeSummary::create($data);
        }
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Update IPD discharge summary within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validateDischargeSummary($request, true, $id));

        $dischargeSummary = IPDDischargeSummary::where('ipd_id', $id)->first();
        $data             = $request->all();
        $filePath         = $this->handleFileUpload($request);
        if ($filePath) {
            if ($dischargeSummary->upload_pdf_path && Storage::disk('public')->exists($dischargeSummary->upload_pdf_path)) {
                Storage::disk('public')->delete($dischargeSummary->upload_pdf_path);
            }
            $data['upload_pdf_path'] = $filePath;
        }

        $dischargeSummary->update($data);
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
    }

    public function delete(string $id): void
    {
        $dischargeSummary = IPDDischargeSummary::findOrFail($id);
        if ($dischargeSummary->upload_pdf_path && Storage::disk('public')->exists($dischargeSummary->upload_pdf_path)) {
            Storage::disk('public')->delete($dischargeSummary->upload_pdf_path);
        }
        $dischargeSummary->delete();
    }

    public function get(string $id): IPDDischargeSummary
    {
        // Try by ipd_id
        $record = IPDDischargeSummary::with('ipd')->where('ipd_id', $id)->first();
        if ($record) {
            return $record;
        } else {
            $ipd               = IPD::with('preliminaryNotes', 'surgery')->find($id);
            $preliminaryNotes  = $ipd->preliminaryNotes->first();
            $surgery           = $ipd->surgery;
            $consultantDoctors = $ipd->consultantDoctors
                ->pluck('user_name')
                ->filter()
                ->implode(', ');

            $operationDone = $surgery
                ->pluck('name')
                ->filter()
                ->implode(', ');

            $findingsAndProcedure = $surgery
                ->pluck('operative_findings')
                ->filter()
                ->implode(', ');
            $generalExamination =
                "RS - " . ($preliminaryNotes->rs ?? "__________________________________________") .
                ", CVS - " . ($preliminaryNotes->cvs ?? "_________________________________________") .
                ", Per Abdomen - " . ($preliminaryNotes->per_abdomen ?? "_________________________________________");
            $data = [
                'ipd_id'                      => $id,
                'doctor_incharge'             => $preliminaryNotes->doctor_incharge,
                'consultants'                 => $consultantDoctors,
                'diagnosis'                   => $preliminaryNotes->final_diagnosis,
                'case_history_and_complaints' => $preliminaryNotes->chief_complaint,
                'general_examination'         => $generalExamination,
                'systemic_examination'        => $preliminaryNotes->local_examination,
                'investigations'              => $preliminaryNotes->investigation,
                'operation_done'              => $operationDone,
                'findings_and_procedure'      => $findingsAndProcedure,
            ];

            return IPDDischargeSummary::create($data);

        }

    }

    public function all(?Request $request): mixed
    {
        $dischargeSummaries = IPDDischargeSummary::query()->with('ipd')->latest();

        if ($request?->has('ipd_id')) {
            $dischargeSummaries = $dischargeSummaries->where('ipd_id', $request->ipd_id);
        }

        if ($request?->has('search')) {
            $dischargeSummaries = $this->search($request->search, $dischargeSummaries);
        }

        if ($request?->has('sort_by')) {
            $sortBy             = $request->sort_by ?? '';
            $sortOrder          = $request->sort_order ?? 'desc';
            $dischargeSummaries = $dischargeSummaries->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('multiple_filter')) {
            $dischargeSummaries = $this->filterMultipleFields($request->multiple_filter, $dischargeSummaries);
        }

        $perPage = $request?->per_page ?? 10;
        return $dischargeSummaries->paginate($perPage);
    }
}
