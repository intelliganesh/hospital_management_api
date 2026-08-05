<?php
namespace App\Services\Users;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Models\UserAddressProof;
use App\Services\CheckValidation;
use App\Services\EncriptedService;
use App\Traits\UserAddressProofValidation;
use Illuminate\Http\Request;

class UserAddressProofService implements CRUDContract, FilterContract
{
    use UserAddressProofValidation;
    private $checkValidationService;
    protected $encreptionForGovIdService;

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
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        // UserAddressProof::create(array_merge($request->all(), ['consent_given_at' => now()]));

        $proof = UserAddressProof::create(
            [
                'user_id'          => $request->user_id,
                'id_type'          => $request->id_type,
                'consent'          => $request->consent,
                'consent_given_at' => now(),
            ]
        );

        if ($request->filled('id_value')) {
            $encrypted                  = $this->encreptionForGovIdService->encreption($request->id_value);
            $proof->id_number_encrypted = $encrypted['id_number_encrypted'];
            $proof->id_number_masked    = $encrypted['id_number_masked'];
            $proof->save();
        }
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string | null $id): void
    {
        $this->createRecordOrUpdate($request, $id);
    }

    /**
     * Summary of createOrUpdateReturn
     * @param \Illuminate\Http\Request $request
     * @return UserAddressProof
     */
    public function createOrUpdateReturn(Request $request): UserAddressProof
    {
        return $this->createRecordOrUpdate($request, null);
    }

    /**
     * Summary of createRecordOrUpdate
     * @param \Illuminate\Http\Request $request
     * @return UserAddressProof
     */
    private function createRecordOrUpdate(Request $request, string | null $id): UserAddressProof
    {

        $proof = UserAddressProof::where('user_id', $request->user_id)->first();

        if ($proof) {
            $this->checkValidationService->checkValidation($this->validate($request, true));
            $proof->update(
                [
                    'consent_given_at' => now(),
                    'id_type'          => $request->id_type,
                    'consent'          => $request->consent,
                    'id_proof_for_pan' => $request->id_proof_for_pan,
                ]
            );
        } else {
            $encrypted = $this->encreptionForGovIdService->encreption($request->id_number);
            $this->checkValidationService->checkValidation($this->validate($request));
            $proof = UserAddressProof::create(
                [
                    'consent_given_at'    => now(),
                    'user_id'             => $request->user_id,
                    'id_type'             => $request->id_type,
                    'consent'             => $request->consent,
                    'id_proof_for_pan'    => $request->id_proof_for_pan,
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
     * @deprecated This method is not used
     */
    public function partialUpdate(Request $request, string | null $id): void
    {

    }

    /**
     * @deprecated This method is not used
     */
    public function delete(string $id): void
    {

    }

    /**
     * Summary of get
     * @param string $id
     * @return UserAddressProof|null
     */
    public function get(string $id): mixed
    {
        return UserAddressProof::where('user_id', $id)->first();
    }

    /**
     * Summary of getDataByDynamicColumnsName
     * @param string $columnsName
     * @param string $id
     * @return UserAddressProof|null
     */
    public function getDataByDynamicColumnsName(string $columnsName = 'user_id', string $id): mixed
    {
        return UserAddressProof::where($columnsName, $id)->first();
    }

    /**
     * @deprecated This method is not used
     */
    public function all(?Request $request): mixed
    {
        return null;
    }

    /**
     * @deprecated This method is not used
     */
    public function search(string $searchText, $data)
    {
    }

    /**
     * @deprecated This method is not used
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated This method is not used
     */
    public function sortData(string $searchText, $data)
    {
    }
}
