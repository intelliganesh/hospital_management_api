<?php

namespace App\Services;

use App\Models\PatientFistula;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Contracts\CRUDContract;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\PatientFistulaValidation;
use Illuminate\Support\Facades\Auth;

class PatientFistulaService implements CRUDContract, FilterContract
{
    use PatientFistulaValidation;

    private $filter;
    private $columns;
    private $checkValidationService;

    public function __construct(CheckValidation $checkValidationService)
    {
        $this->filter = PatientFistula::$filter ?? [];
        $this->columns = PatientFistula::$columns ?? [];
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Search in multiple columns
     */
    public function search(string $searchText, $data)
    {
        $searchColumns = ['name', 'email', 'phone'];
        foreach ($searchColumns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * Filter by multiple fields
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filter as $column) {
            if (!empty($request[$column])) {
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
     * Create patient fistula record
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create patient fistula record within a secure transaction')]
    public function create(Request $request): void
    {
        $patient = Patient::findOrFail($request->patient_id);
        $request->merge([
            'patient_id' => $patient->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => $patient->phone,
            'patient_number' => $patient->patient_number,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        $this->checkValidationService->checkValidation($this->validate($request));
        PatientFistula::create($request->all());
    }

    /**
     * Update patient fistula record
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update patient fistula record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));

         $request->merge([
            'updated_by' => Auth::id(),
        ]);
        PatientFistula::findOrFail($id)->update($request->all());
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
    }

    /**
     * Delete patient fistula record
     */
    public function delete(string $id): void
    {
        PatientFistula::findOrFail($id)->delete();
    }

    /**
     * Get single patient fistula record
     */
    public function get(string $id): PatientFistula
    {
        return PatientFistula::findOrFail($id);
    }

    /**
     * Get all patient fistula records with filters and pagination
     */
    public function all(?Request $request): mixed
    {
        $query = PatientFistula::query();

        if ($request?->has('search')) {
            $searchValue = $request->search;
            $query = $this->search($searchValue, $query);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query = $query->orderBy($sortBy, $sortOrder);
        }

        if ($request?->has('patient_id')) {
            $query = $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('multiple_filter')) {
            $query = $this->filterMultipleFields($request->multiple_filter, $query);
        }

        return $query->paginate(env('PAGINATION', 25));
    }

    /**
     * Get patient fistula records by patient ID
     */
    public function getByPatientId(string $patientId)
    {
        return PatientFistula::where('patient_id', $patientId)->get();
    }
}
