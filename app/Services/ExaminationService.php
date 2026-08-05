<?php

namespace App\Services;

use Throwable;
use App\Models\Examination;
use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Traits\ExaminationsValidation;
use App\Services\Shared\AppointmentHelperService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExaminationService implements CRUDContract, FilterContract
{

    use ExaminationsValidation;

    private $appointmentService;

    private $checkValidationService;
    private $appointmentHelperService;
    private $examinationValidationColumns;


    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\Shared\AppointmentHelperService $appointmentHelperService
     */
    public function __construct(CheckValidation $checkValidationService, AppointmentHelperService $appointmentHelperService)
    {
        $this->checkValidationService = $checkValidationService;
        $this->appointmentHelperService = $appointmentHelperService;
        $this->examinationValidationColumns = Examination::$examinationValidationColumns;
    }


    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        $data->where(function ($query) use ($searchText) {
            $query->where('patient_number', 'like', '%' . $searchText . '%')
                // ->orWhere('name', 'like', '%' . $searchText . '%')
                ->orWhere('temperature', 'like', '%' . $searchText . '%')
                ->orWhere('bp', 'like', '%' . $searchText . '%')
                ->orWhere('pulse', 'like', '%' . $searchText . '%')
                ->orWhere('phone_no', 'like', '%' . $searchText . '%')->orWhere('status', 'like', '%' . $searchText . '%');
        });
        return $data;
    }
    /**
     * @deprecated message
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }
    /**
     * @deprecated message
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Examination::create(array_merge($request->all(), $this->appointmentHelperService->getAppointmentRequiredData($request->appointment_id)));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $examination = Examination::findOrFail($id);
        if (!$examination) {
            throw new NotFoundHttpException('Examination data not found.');
        }
        if ($request->doctor_id && $request->patient_id && $request->front_desk_user_id) {
            $examination->update(array_merge($request->only($this->examinationValidationColumns)));
        } else {
            $examination->update($request->all());
        }
    }



    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $examination = Examination::findOrFail($id);
        if (!$examination) {
            throw new NotFoundHttpException('Patient data not found.');
        }
        $examination->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Examination
     */
    public function get(string $id): Examination|Throwable
    {
        $examination = Examination::findOrFail($id);
        if (!$examination) {
            throw new NotFoundHttpException('Examination data not found.');
        }

        return $examination;
    }



    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $examination = Examination::query();
        if ($request->has('search')) {
            $searchValue = $request->search;
            $examination = $this->search($searchValue, $examination);
        }
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'patient_number';
            $sortOrder = $request->sort_order ?? 'desc';
            $examination = $examination->orderBy($sortBy, $sortOrder);
        }
        return $examination->select('id', 'patient_number', 'temperature', 'bp', 'pulse')->paginate(env('PAGINATION', 25));
    }


}