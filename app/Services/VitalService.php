<?php

namespace App\Services;

use App\Models\Vital;
use Illuminate\Http\Request;
use App\Traits\VitalValidation;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Attributes\Transactional;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VitalService implements CRUDContract, FilterContract
{

    use VitalValidation;

    private $checkValidationService;
    private $vitalValidationColumns;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
        $this->vitalValidationColumns = Vital::$vitalValidationColumns;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create vital record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate(new Request($request->only($this->vitalValidationColumns))));
        Vital::create($request->only($this->vitalValidationColumns));

    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update vital record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        $vital = Vital::findOrFail($request->id);
        $vital->update($request->only($this->vitalValidationColumns));
    }



    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // write code here.
    }

    /**
     * Summary of delete
     * @param string $id
     * @return bool
     */
    public function delete(string $id): void
    {
        $vital = Vital::findOrFail($id);
        if (!$vital) {
            throw new NotFoundHttpException('Vital data not found.');
        }
        $vital->delete();
    }


    public function get(string $id): Vital
    {
        $vital = Vital::findOrFail($id);
        if (!$vital) {
            throw new NotFoundHttpException('Vital data not found.');
        }
        return $vital;
    }



    /**
     * @deprecated This method is not used.
     */
    public function all(?Request $request): mixed
    {
        return null;
    }



    /**
     * @deprecated This method is not used.
     */
    public function search(string $searchText, $data)
    {
        // write code here
    }


    /**
     * @deprecated This method is not used.
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated This method is not used.
     * 
     */
    public function sortData(string $searchText, $data)
    {
        // write code here
    }




}