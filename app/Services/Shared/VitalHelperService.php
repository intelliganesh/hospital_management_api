<?php

namespace App\Services\Shared;

use App\Models\Vital;
use App\Traits\VitalValidation;
use App\Services\CheckValidation;

class VitalHelperService
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
     * Summary of getByDynamicColumn
     * @param mixed $id
     * @param mixed $columnName
     * @return array|Vital
     */
    public function getByDynamicColumn($id, $columnName = 'id'): array|Vital
    {
        $vital = Vital::where($columnName, $id)->first();
        if (!$vital) {
            return [];
        }
        return $vital;
    }

    /**
     * Summary of deleteByDynamicColumn
     * @param mixed $id
     * @param mixed $columnName
     * @return void
     */
    public function deleteByDynamicColumn($id, $columnName = 'id'): void
    {
        $vital = Vital::where($columnName, $id);
        $vital->delete();
    }

    /**
     * Summary of updateOrCreateByColumnName
     * @param mixed $request
     * @param mixed $id
     * @param mixed $columnName
     * @return void
     */
    public function updateOrCreateByColumnName($request, $id, $columnName = 'id')
    {
        $vitalData = Vital::query();
        if (!$vitalData->where($columnName, $id)->first()) {
            $this->checkValidationService->checkValidation($this->validate($request));
            $vitalData->create($request);
        } else {
            $this->checkValidationService->checkValidation($this->validate($request, true));
            $vitalData->where($columnName, $id)->update($request);
        }
        // if (!$vitalData->where($columnName, $id)->first()) {
        //     $this->checkValidationService->checkValidation($this->validate($request->only($this->vitalValidationColumns)));
        //     $vitalData->create($request->only($this->vitalValidationColumns));
        // } else {
        //     $this->checkValidationService->checkValidation($this->validate($request->only($this->vitalValidationColumns), true));
        //     $vitalData->update($request->only($this->vitalValidationColumns));
        // }
    }
}