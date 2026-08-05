<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\IPD;
use App\Models\IPDPreOperativeChecklist;
use App\Services\CheckValidation;
use App\Traits\IPDPreOperativeChecklistValidation;
use Illuminate\Http\Request;

class IPDPreOperativeChecklistService implements CRUDContract, FilterContract
{
    use IPDPreOperativeChecklistValidation;

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
        $this->filter                 = IPDPreOperativeChecklist::$filter;
        $this->columns                = IPDPreOperativeChecklist::$columns;
        $this->listcolumns            = IPDPreOperativeChecklist::$listcolumns;
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
        // Store file in public/uploads/ipd_pre_operative_checklist directory
        $filePath = $file->store("app/public/pdfs/ipd/{$ipd->ipd_number}/uploads/pre_operative_checklist_{$ipd->ipd_number}_" . str_replace(['.', ' '], '_', $file->getClientOriginalName()) . ".pdf", 'storage');
        return $filePath;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create pre-operative checklist record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validatePreOperativeChecklist($request));

        $data = $request->all();

        // Handle file upload
        $filePath = $this->handleFileUpload($request);
        if ($filePath) {
            $data['upload_pdf_path'] = $filePath;
        }

        IPDPreOperativeChecklist::create($data);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update pre-operative checklist record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $checklist = IPDPreOperativeChecklist::where('ipd_surgery_id', $id)->first();

        if (is_null($checklist)) {
            $this->create($request);
        } else {
            $this->checkValidationService->checkValidation($this->validatePreOperativeChecklist($request, true, $checklist->id));

            $data = $request->all();

            // Handle file upload
            $filePath = $this->handleFileUpload($request);
            if ($filePath) {
                // Delete old file if exists
                if ($checklist->upload_pdf_path && Storage::disk('public')->exists($checklist->upload_pdf_path)) {
                    Storage::disk('public')->delete($checklist->upload_pdf_path);
                }
                $data['upload_pdf_path'] = $filePath;
            }

            $checklist->update($data);
        }
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
        IPDPreOperativeChecklist::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return IPDPreOperativeChecklist
     */
    public function get(string $id): IPDPreOperativeChecklist
    {

        // Try to find by primary id first
        $record = IPDPreOperativeChecklist::find($id);
        if ($record) {
            return $record;
        }

        // Try by ipd_surgery_id
        $record = IPDPreOperativeChecklist::where('ipd_surgery_id', $id)->first();
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
        $checklist = IPDPreOperativeChecklist::query()->orderBy('datetime', 'desc');

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $checklist   = $this->search($searchValue, $checklist);
        }

        if ($request?->has('sort_by')) {
            $sortBy    = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            $checklist = $checklist->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('multiple_filter')) {
            $checklist = $this->filterMultipleFields($request->multiple_filter, $checklist);
        }

        $perPage = $request?->per_page ?? 10;
        return $checklist->paginate($perPage);
    }
}
