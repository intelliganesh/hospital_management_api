<?php
namespace App\Services\Shared;

use App\Models\Consultations;
use App\Models\Master\Department;
use App\Models\User;
use App\Services\CheckValidation;
use App\Services\ProctologyService;
use App\Traits\ConsultationsValidation;
use Illuminate\Http\Request;

class AppointmentConsultationHelperService
{
    use ConsultationsValidation;
    private $checkValidationService;
    private $appointmentHelperService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\Shared\AppointmentHelperService $appointmentHelperService
     * @param \App\Services\ProctologyService $proctologyService
     */
    public function __construct(CheckValidation $checkValidationService, AppointmentHelperService $appointmentHelperService, ProctologyService $proctologyService)
    {
        $this->checkValidationService   = $checkValidationService;
        $this->appointmentHelperService = $appointmentHelperService;
        $this->proctologyService        = $proctologyService;

    }

    /**
     * Summary of updatedRelatedData
     * @param \Illuminate\Http\Request|array $request
     * @param string|null $id
     * @return void
     */
    public function updatedRelatedData(Request | array $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $consultations = Consultations::where('appointment_id', $id);
        $consultations = $consultations->update(array_merge($request, $this->appointmentHelperService->getAppointmentRequiredData($request['appointment_id'])));
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request|array $request
     * @return Consultations
     */
    public function create(Request | array $request): mixed
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $departmentId = User::where('id', $request['doctor_id'])->first()->department;
        $depatment    = Department::where('id', $departmentId)->first();
        if (isset($depatment->department_type)) {
            $request['type'] = $depatment->department_type;
        }
        $data          = array_merge($request, $this->appointmentHelperService->getAppointmentRequiredData($request['appointment_id']));
        $consultations = Consultations::create($data);
        if ($depatment->department_type == 'Proctology') {
            $data['amount']           = 0;
            $data['tests']            = '[]';
            $data['diet_plan']        = '[]';
            $data['on_examination']   = '[]';
            $data['treatment_plan']   = '<p class="text-left"></p>';
            $data['chief_complaints'] = '[]';
            $data['surgical_history'] = '[]';
            $proctologyCreatedData    = $this->proctologyService->createOrUpdate(new Request($data), $consultations->id);
        }
        return $consultations;
    }
}
