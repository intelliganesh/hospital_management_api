<?php

namespace App\Services\Shared;

use App\Models\Examination;
use App\Services\CheckValidation;
use App\Traits\ExaminationsValidation;

class ExaminationHelperService
{
    use ExaminationsValidation;

    private $checkValidationService;

    public $examinationValidationColumns;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
        $this->examinationValidationColumns = Examination::$examinationValidationColumns;
    }


    /**
     * Summary of deleteByDynamicColumn
     * @param mixed $id
     * @param mixed $columnName
     * @return void
     */
    public function deleteByDynamicColumn($id, $columnName = 'id'): void
    {
        $vital = Examination::where($columnName, $id);
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
        $examination = Examination::query();
        if (!$examination->where($columnName, $id)->first()) {
            $this->checkValidationService->checkValidation($this->validate($request));
            $examination->create($request);
        } else {
            $this->checkValidationService->checkValidation($this->validate($request, true, $id));
            $examination->where($columnName, $id)->update($request);
        }
        // if (!$examination->where($columnName, $id)->first()) {
        //     $this->checkValidationService->checkValidation($this->validate($request));
        //     $examination->create($request->only($this->examinationValidationColumns));
        // } else {
        //     $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        //     $examination->where($columnName, $id)->update($request->only($this->examinationValidationColumns));
        // }
    }

    /**
     * Summary of getByDynamicColumn
     * @param mixed $id
     * @param mixed $columnName
     * @return array|Examination
     */
    public function getByDynamicColumn($id, $columnName = 'id'): array|Examination
    {
        $examination = Examination::where($columnName, $id)->first();
        if (!$examination) {
            return [];
        }
        return $examination;
    }
}
