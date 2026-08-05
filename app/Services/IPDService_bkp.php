<?php

namespace App\Services;

use App\Models\IPD;
use App\Enums\ServiceType;
use Illuminate\Http\Request;
use App\Traits\IPDValidation;
use App\Contracts\CRUDContract;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Attributes\Transactional;
use App\Facades\AutoIdGenerateFacade;

class IPDService_bkp implements CRUDContract, FilterContract
{

    use IPDValidation;

    private $columns;
    private $patientService;
    private $patientHelperService;
    private $checkValidationService;
    private $patientAddressProofService;

    /**
     * Summary of __construct
     * @param \App\Services\PatientService $patientService
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\PatientHelperService $patientHelperService
     * @param \App\Services\PatientAddressProofService $patientAddressProofService
     */
    public function __construct(CheckValidation $checkValidationService, PatientAddressProofService $patientAddressProofService, PatientHelperService $patientHelperService, PatientService $patientService)
    {
        $this->columns = IPD::$columns;
        $this->patientService = $patientService;
        $this->patientHelperService = $patientHelperService;
        $this->checkValidationService = $checkValidationService;
        $this->patientAddressProofService = $patientAddressProofService;
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
        foreach ($this->columns as $column) {
            if (!empty($request[$column])) {
                $data->where($column, $request[$column]);
            }
        }
        return $data;
    }

    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     * @return void
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * Summary of sortData
     * @param string $searchText
     * @param mixed $data
     * @return void
     */
    public function sortData(string $searchText, $data)
    {
    }


    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create  IPD  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $patient = null;
        if (!empty($request->email)) {
            $patient = $this->patientHelperService->getByColumnName('email', $request->email);
        } else if (empty($request->phone_no)) {
            $patient = $this->patientHelperService->getByColumnName('phone_no', $request->phone_no);
        }
        if (empty($patient)) {
            $this->patientService->create($request);
            $request->merge(['patient_id' => $patient->id]);
        } else if (!empty($patient)) {
            $this->patientService->update($request, $patient->id);
            $request->merge(['patient_id' => $patient->id]);
        }
        $ipd = ['ipd_number' => AutoIdGenerateFacade::generateId(ServiceType::IPD)];
        IPD::create(array_merge($request->all(), $ipd));
    }


    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  IPD  record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        IPD::findOrFail($id)->update($request->all());
    }


    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
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
        IPD::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        $ipd = IPD::findOrFail($id);
        $patientAddressProof = $this->patientAddressProofService->getPatientAddressProofByPatientId($ipd->patient_id);
        $ipd['id_type'] = $patientAddressProof->id_type ?? "";
        $ipd['consent'] = $patientAddressProof->consent ?? "";
        $ipd['id_number_masked'] = $patientAddressProof->id_number_masked ?? "";
        return $ipd;
    }

    public function all(?Request $request): mixed
    {
        $ipd = IPD::query();
        if ($request?->has('search')) {
            $searchValue = $request->search;
            $ipd = $this->search($searchValue, $ipd);
        }

        if ($request?->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'ipd_number';
            $sortOrder = $request->sort_order ?? 'desc';
            $ipd = $ipd->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $ipd = $this->filterMultipleFields($request->multiple_filter, $ipd);
        }

        return $ipd->select($this->columns)->paginate(env('PAGINATION', 25));

    }

}