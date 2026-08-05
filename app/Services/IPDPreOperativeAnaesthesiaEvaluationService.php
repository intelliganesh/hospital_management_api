<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPD;
use App\Models\IPDPreOperativeAnaesthesiaEvaluation;
use App\Services\CheckValidation;
use App\Traits\IPDPreOperativeAnaesthesiaEvaluationValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IPDPreOperativeAnaesthesiaEvaluationService implements CRUDContract, FilterContract
{
    use IPDPreOperativeAnaesthesiaEvaluationValidation;

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
        $this->filter                 = IPDPreOperativeAnaesthesiaEvaluation::$filter;
        $this->columns                = IPDPreOperativeAnaesthesiaEvaluation::$columns;
        $this->listcolumns            = IPDPreOperativeAnaesthesiaEvaluation::$listcolumns;
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
        // Store file in public/uploads/ipd_pre_operative_anaesthesia_evaluation directory
        $filePath = $file->store("app/public/pdfs/ipd/{$ipd->ipd_number}/uploads/pre_operative_anaesthesia_evaluation_{$ipd->ipd_number}_" . str_replace(['.', ' '], '_', $file->getClientOriginalName()) . ".pdf", 'storage');
        return $filePath;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create pre-operative anaesthesia evaluation record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validatePreOperativeAnaesthesiaEvaluation($request));

        $data = $request->all();

        // Enforce uniqueness for ipd_surgery_id and ipd_anaesthesia_id
        $exists = IPDPreOperativeAnaesthesiaEvaluation::where('ipd_surgery_id', $data['ipd_surgery_id'])
            ->orWhere('ipd_anaesthesia_id', $data['ipd_anaesthesia_id'])
            ->first();
        if ($exists) {
            $this->update($request, $exists->id);
        }

        // Handle file upload
        $filePath = $this->handleFileUpload($request);
        if ($filePath) {
            $data['upload_pdf_path'] = $filePath;
        }

        IPDPreOperativeAnaesthesiaEvaluation::create($data);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update pre-operative anaesthesia evaluation record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        // Try to find by primary id first
        $evaluation = IPDPreOperativeAnaesthesiaEvaluation::find($id);
        if (! $evaluation) {
            // Try by ipd_surgery_id
            $evaluation = IPDPreOperativeAnaesthesiaEvaluation::where('ipd_surgery_id', $id)->first();
        }
        if (! $evaluation) {
            // Try by ipd_anaesthesia_id
            $evaluation = IPDPreOperativeAnaesthesiaEvaluation::where('ipd_anaesthesia_id', $id)->first();
        }
        if (! $evaluation) {
            abort(404, 'Record not found');
        }

        $this->checkValidationService->checkValidation($this->validatePreOperativeAnaesthesiaEvaluation($request, true, $evaluation->id));

        $data = $request->except(['_token', '_method','id']);

        // Handle file upload
        $filePath = $this->handleFileUpload($request);
        if ($filePath) {
            // Delete old file if exists
            if ($evaluation->upload_pdf_path && Storage::disk('public')->exists($evaluation->upload_pdf_path)) {
                Storage::disk('public')->delete($evaluation->upload_pdf_path);
            }
            $data['upload_pdf_path'] = $filePath;
        }

        $evaluation->update($data);
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
        IPDPreOperativeAnaesthesiaEvaluation::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id - Can be primary ID, ipd_surgery_id, or ipd_anaesthesia_id
     * @return IPDPreOperativeAnaesthesiaEvaluation
     */
    public function get(string $id): IPDPreOperativeAnaesthesiaEvaluation
    {
        // Try to find by primary id first
        $record = IPDPreOperativeAnaesthesiaEvaluation::find($id);
        if ($record) {
            return $record;
        }

        // Try by ipd_surgery_id
        $record = IPDPreOperativeAnaesthesiaEvaluation::where('ipd_surgery_id', $id)->first();
        if ($record) {
            return $record;
        }

        // Try by ipd_anaesthesia_id
        $record = IPDPreOperativeAnaesthesiaEvaluation::where('ipd_anaesthesia_id', $id)->first();
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
        $evaluation = IPDPreOperativeAnaesthesiaEvaluation::query()->orderBy('datetime', 'desc');

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $evaluation  = $this->search($searchValue, $evaluation);
        }

        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $evaluation = $evaluation->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('multiple_filter')) {
            $evaluation = $this->filterMultipleFields($request->multiple_filter, $evaluation);
        }

        $perPage = $request?->per_page ?? 10;
        return $evaluation->paginate($perPage);
    }

    /**
     * Get all pre-operative anaesthesia evaluation records for a particular IPD Anaesthesia
     * @param string $ipd_anaesthesia_id
     * @return mixed
     */
    public function getByIPDAnaesthesiaId(string $ipd_anaesthesia_id): mixed
    {
        return IPDPreOperativeAnaesthesiaEvaluation::where('ipd_anaesthesia_id', $ipd_anaesthesia_id)
            ->orderBy('datetime', 'desc')
            ->get();
    }
}
