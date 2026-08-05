<?php
namespace App\Services;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\PatientAddressProof;
use App\Services\CheckValidation;
use App\Traits\PatientAddressProofValidation;
use Illuminate\Http\Request;

class PatientAddressProofService implements CRUDContract, FilterContract
{

    use PatientAddressProofValidation;

    private $checkValidationService;
    private $encreptionForGovIdService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\EncriptedService $encreptionForGovIdService
     */
    public function __construct(CheckValidation $checkValidationService, EncriptedService $encreptionForGovIdService)
    {
        $this->checkValidationService    = $checkValidationService;
        $this->encreptionForGovIdService = $encreptionForGovIdService;
    }

    /**
     * @deprecated This method is not used
     */
    public function search(string $searchText, $data)
    {
        return $data;
    }

    /**
     * @deprecated This method is not used
     */
    public function filterMultipleFields($request, $data)
    {
        return $data;
    }

    /**
     * @deprecated This method is not used
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * Summary of getDataByDynamicColumnsName
     * @param string $columnsName
     * @param string $id
     * @return PatientAddressProof|null
     */
    public function getDataByDynamicColumnsName(string $columnsName = 'patient_id', string $id): mixed
    {
        return PatientAddressProof::where($columnsName, $id)->first();
    }

    /**
     * @deprecated This method is not used
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * Summary of createRecordOrUpdate
     * @param \Illuminate\Http\Request $request
     * @return PatientAddressProof
     */
    private function createRecordOrUpdate(Request $request, string | null $id): PatientAddressProof
    {
        $proof = PatientAddressProof::where('patient_id', $request->patient_id)->first();
        if ($proof) {
            $this->checkValidationService->checkValidation($this->validate($request, true));
            $proof->update(
                [
                    'consent_given_at' => now(),
                    'id_type'          => $request->id_type,
                    'consent'          => $request->consent,
                    "id_proof_for_pan" => $request->id_proof_for_pan,
                ]
            );
        } else {
            $this->checkValidationService->checkValidation($this->validate($request));
            $encrypted = $this->encreptionForGovIdService->encreption($request->id_number);
            $proof     = PatientAddressProof::create(
                [
                    'consent_given_at'    => now(),
                    'id_type'             => $request->id_type,
                    'consent'             => $request->consent,
                    'patient_id'          => $request->patient_id,
                    "id_proof_for_pan"    => $request->id_proof_for_pan,
                    "id_number_masked"    => $encrypted['id_number_masked'],
                    "id_number_encrypted" => $encrypted['id_number_encrypted'],
                ]
            );
        }

        return $proof;

        // $proof = UserAddressProof::updateOrCreate(
        //     ['user_id' => $request->user_id],
        //     [
        //         'id_type' => $request->id_type,
        //         'consent' => $request->consent,
        //         'consent_given_at' => now(),
        //     ]
        // );

        // if ($request->filled('id_value')) {
        //     $encrypted = $this->encreptionForGovIdService->encreption($request->id_value);
        //     $proof->id_number_encrypted = $encrypted['id_number_encrypted'];
        //     $proof->id_number_masked = $encrypted['id_number_masked'];
        //     $proof->save();
        // }
    }

    /**
     * Summary of recordCreate
     * @param \Illuminate\Http\Request $request
     * @return PatientAddressProof
     */
    private function recordCreate(Request $request): PatientAddressProof
    {
        $this->checkValidationService->checkValidation($this->validate($request));

        $proof = PatientAddressProof::create(
            [
                'patient_id'       => $request->patient_id,
                'id_type'          => $request->id_type,
                'consent'          => $request->consent,
                'consent_given_at' => now(),
            ]
        );
        if ($request->filled('id_number')) {
            $encrypted                  = $this->encreptionForGovIdService->encreption($request->id_number);
            $proof->id_number_encrypted = $encrypted['id_number_encrypted'];
            $proof->id_number_masked    = $encrypted['id_number_masked'];
            $proof->save();
        }

        return $proof;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->recordCreate($request);
    }

    /**
     * Summary of createAndGet
     * @param \Illuminate\Http\Request $request
     * @return PatientAddressProof
     */
    public function createAndGet(Request $request): mixed
    {
        return $this->createRecordOrUpdate($request, null);

    }

    /**
     * @deprecated This method is not used
     */
    public function update(Request $request, string | null $id): void
    {
    }

    /**
     * @deprecated This method is not used
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        //code here
    }

    /**
     * @deprecated This method is not used
     */
    public function delete(string $id): void
    {
    }

    /**
     * Summary of getPatientAddressProofByPatientId
     * @param string $id
     * @return PatientAddressProof|null
     */
    public function getPatientAddressProofByPatientId(string $id): mixed
    {
        return PatientAddressProof::where('patient_id', $id)->first();
    }

    /**
     * @deprecated This method is not used
     */
    public function get(string $id): mixed
    {
        return null;
    }

    /**
     * @deprecated This method is not used
     */
    public function all(?Request $request): mixed
    {
        // if ($request?->has('search')) {
        //     $searchValue = $request->search;
        // }

        // if ($request?->has('sort_by')) {
        //     $sortBy = $request->sort_by ?? '';
        //     $sortOrder = $request->sort_order ?? 'desc';
        // }

        // if ($request->has('multiple_filter')) {
        //     $this->filterMultipleFields($request->multiple_filter, []);
        // }

        return null;

    }

}
