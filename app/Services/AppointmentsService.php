<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Enums\Appointment\StatusEnum;
use App\Enums\Payment\AmountForEnum;
use App\Enums\ServiceType;
use App\Events\MailEvent;
use App\Interceptors\ServiceInterceptor;
use App\Mail\Appointment\Doctor\ReSchedule as ReScheduleDoctor;
use App\Mail\Appointment\Doctor\Schedule as ScheduleDoctor;
use App\Mail\Appointment\Patient\ReSchedule as ReSchedulePatient;
use App\Mail\Appointment\Patient\Schedule as SchedulePatient;
use App\Models\Appointments;
use App\Models\Consultations;
use App\Models\Invoice;
use App\Services\CheckValidation;
use App\Services\Shared\AppointmentConsultationHelperService;
use App\Services\Shared\AppointmentHelperService;
use App\Services\Shared\ConsultationAppointmentsHelperService;
use App\Traits\AppointmentsValidationTrait;
use App\Traits\FieldValuesTrait;
use App\Traits\ResponseTrait;
use AutoIdGenerate;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentsService implements CRUDContract, FilterContract
{

    use ResponseTrait;
    use FieldValuesTrait;
    use AppointmentsValidationTrait;

    private $filter;
    private $columns;
    private $invoiceService;
    private $invoiceColumns;
    private $paymentService;
    private $consultationService;
    private $patientHelperService;
    private $updateOrCreateColumns;
    private $systemSettingsService;
    private $checkValidationService;
    private $appointmentHelperService;
    private $appointmentValidationColumns;
    private $appointmentConsultationService;
    private $consultationAppointmentService;
    private $consultationsCreateOrUpdateColumns;

    /**
     * Summary of __construct
     * @param \App\Services\Shared\AppointmentConsultationHelperService $consultationService
     * @param \App\Services\PaymentService $paymentService
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\PatientHelperService $patientHelperService
     * @param \App\Services\Shared\AppointmentConsultationHelperService $appointmentConsultationService
     * @param \App\Services\SystemSettingsService $systemSettingsService
     * @param \App\Services\Shared\AppointmentHelperService $appointmentHelperService
     * @param \App\Services\Shared\ConsultationAppointmentsHelperService $consultationAppointmentService
     * @param \App\Services\InvoiceService $invoiceService
     */
    public function __construct(AppointmentConsultationHelperService $consultationService, PaymentService $paymentService, CheckValidation $checkValidationService, PatientHelperService $patientHelperService, AppointmentConsultationHelperService $appointmentConsultationService, SystemSettingsService $systemSettingsService, AppointmentHelperService $appointmentHelperService, ConsultationAppointmentsHelperService $consultationAppointmentService, InvoiceService $invoiceService)
    {
        $this->filter                             = Appointments::$filter;
        $this->columns                            = Appointments::$columns;
        $this->paymentService                     = $paymentService;
        $this->invoiceService                     = $invoiceService;
        $this->invoiceColumns                     = Invoice::$columns;
        $this->consultationService                = $consultationService;
        $this->patientHelperService               = $patientHelperService;
        $this->systemSettingsService              = $systemSettingsService;
        $this->checkValidationService             = $checkValidationService;
        $this->appointmentHelperService           = $appointmentHelperService;
        $this->updateOrCreateColumns              = Appointments::$updateOrCreateColumns;
        $this->consultationAppointmentService     = $consultationAppointmentService;
        $this->appointmentConsultationService     = $appointmentConsultationService;
        $this->appointmentValidationColumns       = Appointments::$appointmentValidationColumns;
        $this->consultationsCreateOrUpdateColumns = Consultations::$consultationsCreateOrUpdateColumns;
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create appointments and consultations record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request->only($this->appointmentValidationColumns))); // validagtion for appointment

        $getDoctorAndPatient = $this->patientHelperService->getPatientAndUsers($request->doctor_id, $request->patient_id, $request->front_desk_user_id);
        $appointmentNumber   = AutoIdGenerate::generateId(ServiceType::Appointments);
        $appointment         = array_merge(['appointment_number' => $appointmentNumber, 'appointment_fees' => $request->amount], (array) $getDoctorAndPatient);

        $appointmentCreated = Appointments::create(array_merge($request->only($this->updateOrCreateColumns), $appointment)); // appointment creation

        $consultation = $this->appointmentConsultationService->create(array_merge($request->only($this->consultationsCreateOrUpdateColumns),$getDoctorAndPatient, ['appointment_id' => $appointmentCreated->id, 'fees' => 0, 'payment_status' => $request->payment_status, 'appointment_type' => $request->type])); // payment creation

        // $this->invoiceService->create(new Request(array_merge($consultation->only($this->invoiceColumns), ['collected_amount' => 0, 'balanced_amount' => 0, 'consultation_id' => $consultation->id])));

        // $this->paymentService->create(array_merge($request->all(), $getDoctorAndPatient, ['appointment_id' => $appointmentCreated->id, 'consultation_id' => $consultation->id]));

        if ($request->amount_for == AmountForEnum::Surgery->value) {
            if ($this->patientHelperService->getByColumnName('id', $request->patient_id)->opd_number == null) {
                $this->patientHelperService->updateOrCreateByColumnName(new Request(['opd_number' => AutoIdGenerate::generateId(ServiceType::OPD)]), $request->patient_id, 'id'); // patient opd number generation
            }
        }

        $emailNotoify = $this->checkEmailNotification();

        if ($getDoctorAndPatient['doctor_email'] && $emailNotoify == 1) {
            event(new MailEvent($getDoctorAndPatient['doctor_email'], new ScheduleDoctor($getDoctorAndPatient, $appointmentNumber, $request->appointment_date . ' ' . $request->appointment_time)));
        }
        if ($getDoctorAndPatient['patient_email'] && $emailNotoify == 1) {
            event(new MailEvent($getDoctorAndPatient['patient_email'], new SchedulePatient($getDoctorAndPatient, $appointmentNumber, $request->appointment_date . ' ' . $request->appointment_time)));
        }
    }

    /**
     * Summary of checkEmailNotification
     */
    private function checkEmailNotification()
    {
        return $this->systemSettingsService->getSystemSettings()->email_notification;
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update appointments and consultations record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        if ($request->status === StatusEnum::Cancelled->value) {
            $proxiedService = ServiceInterceptor::intercept($this->consultationAppointmentService);
            $proxiedService->delete($id, 'appointment_id');
        }

        $this->checkValidationService->checkValidation($this->validate($request, true));
        $appointment = Appointments::findOrFail($id);
        // if (!$appointment) {
        //     throw new NotFoundHttpException('Appointment data not found.');
        // }
        $appointmentData = (object) $this->appointmentHelperService->getDoctorAndPatientAndFrontDesk($appointment);
        // $appointment->update(array_merge(['appointment_fees' => $request->amount, 'appointment_type' => $request->type], $request->only($this->updateOrCreateColumns), (array) $appointmentData));
        if ($request->doctor_id != $appointment->doctor_id) {
            $doctor_data                     = User::where('id', $request->doctor_id)->first();
            $request->merge(['doctor_name' => $doctor_data->name,
                             'doctor_email' => $doctor_data->email,
                             'doctor_phone' => $doctor_data->phone]);
        }
        
        $appointment->update(array_merge(['appointment_fees' => 0, 'appointment_type' => $request->type], $request->only($this->updateOrCreateColumns), (array) $appointmentData));

        $this->consultationService->updatedRelatedData(array_merge($request->only($this->consultationsCreateOrUpdateColumns), ['appointment_id' => $id]), $id); // consultation update

        $emailNotoify = $this->checkEmailNotification();

        if ($appointment->doctor_email && $emailNotoify == 1) {
            event(new MailEvent($appointment->doctor_email, new ReScheduleDoctor($appointment, $appointment->appointment_number, $request->appointment_date . ' ' . $request->appointment_time)));
        }
        if ($appointment->patient_email && $emailNotoify == 1) {
            event(new MailEvent($appointment->patient_email, new ReSchedulePatient($appointment, $appointment->appointment_number, $request->appointment_date . ' ' . $request->appointment_time)));
        }
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
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
        $proxiedService = ServiceInterceptor::intercept($this->appointmentHelperService);
        $proxiedService->deleteAppointmentRelatedData($id);
        // $appointment = Appointments::findOrFail($id);
        // // if (!$appointment) {
        // //     throw new NotFoundHttpException('Appointment data not found.');
        // // }

        // $appointment->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Appointments|null
     */
    public function get(string $id): mixed
    {
        $appointment = Appointments::findOrFail($id);
        return $appointment;
        // if (!$appointment) {
        //     throw new NotFoundHttpException('Appointment data not found.');
        // }
        // $payment = $this->paymentService->getByColumnName($id, 'appointment_id');
        // $appointment['amount'] = $payment?->amount;
        // $appointment['amount_for'] = $payment?->amount_for;
        // $appointment['payment_type'] = $payment?->payment_type;
        // return $appointment;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $appointment = Appointments::query();
        $appointment = $appointment->onlyDoctorRelatedIfDoctorLogedIn();
        $appointment = $appointment->with("consultationOnlyDepartmentType");

        if ($request->has('search')) {
            $appointment = $this->search($request->input('search'), $appointment);
        }
        if ($request->has('sort_by')) {
            $sortBy    = $request->sort_by ?? 'type';
            $sortOrder = $request->sort_order ?? 'desc';
            if ($sortBy === 'status') {
                $statusesAsc = [
                    StatusEnum::Cancelled->value,
                    StatusEnum::Closed->value,
                    StatusEnum::Completed->value,
                    StatusEnum::Ongoing->value,
                    StatusEnum::Pending->value,
                    StatusEnum::Rejected->value,
                    StatusEnum::Rescheduled->value,
                ];
                $statusesDesc = array_reverse($statusesAsc);
                $orderList    = $sortOrder === 'asc' ? $statusesAsc : $statusesDesc;
                $order        = "'" . implode("','", $orderList) . "'";
                $appointment  = $appointment->orderByRaw("FIELD(status, $order)");
            } elseif ($sortBy === 'department_type') {
                // Use a raw subquery for sorting by department_type
                $appointment = $appointment->orderByRaw(
                    "(SELECT type FROM consultations WHERE consultations.appointment_id = appointments.id LIMIT 1) " . $sortOrder
                );
            } else {
                $appointment = $appointment->orderBy($sortBy, $sortOrder);
            }
        } else {
            $appointment = $appointment->orderBy('appointment_date', 'desc');
        }

        //$appointment->todayFirst();
        if ($request->has("from_date") && $request->has("to_date")) {
            $appointment = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $appointment);
        }
        //  else {
        //     $appointment->todayFirst();
        // }

        if ($request->has('multiple_filter')) {
            $filter_data = $request->multiple_filter;
            if (isset($filter_data['referred_to'])) {
                $filter_data['doctor_id'] = $filter_data['referred_to'];
                unset($filter_data['referred_to']);
            }
            $appointment = $this->filterMultipleFields($filter_data, $appointment);
        }
        // return $appointment->select('id', 'type', 'status', 'appointment_time', 'appointment_date', 'appointment_number')->paginate(env('PAGINATION', 25));
        return $appointment->select($this->columns)->paginate(env('PAGINATION', 25));
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
                $data->where($column, $request[$column]);

            }
        }
        return $data;
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
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     * @return mixed
     */
    public function filterByDateRange(string $searchText, $data)
    {
        $dates = explode("|", $searchText);
        $data->whereBetween('appointment_date', [$dates[0], $dates[1]]);
        return $data;
    }

    /**
     * @deprecated This method is not used.
     *
     */
    public function sortData(string $searchText, $data)
    {
        // write code here
    }

    /**
     * Summary of getAppointmentByDynamicColumnName
     * @param mixed $columnName
     * @param mixed $id
     * @return Appointments|null
     */
    public function getAppointmentByDynamicColumnName($columnName = 'id', $id)
    {
        return Appointments::where($columnName, $id)->first();
    }

    /**
     * Summary of getAppointmentList
     * @return \Illuminate\Database\Eloquent\Collection<int, Appointments>
     */
    public function getAppointmentList()
    {
        return Appointments::select('id', 'appointment_number')->get();
    }

    /**
     * Get appointment statistics including total count, today's count, completed and pending counts
     *
     * @return array Statistics data
     */
    public function getStatistics(): array
    {
        // Get total appointments count
        $totalAppointments = Appointments::onlyDoctorRelatedIfDoctorLogedIn()->count();

        // Get today's appointments count
        $todaysAppointments = Appointments::onlyDoctorRelatedIfDoctorLogedIn()->whereDate('appointment_date', now()->toDateString())->count();

        // Get completed appointments count
        $completedAppointments = Appointments::onlyDoctorRelatedIfDoctorLogedIn()->whereDate('appointment_date', now()->toDateString())->where('status', StatusEnum::Completed->value)->count();

        // Get pending appointments count
        $pendingAppointments = Appointments::onlyDoctorRelatedIfDoctorLogedIn()->whereDate('appointment_date', now()->toDateString())->where('status', StatusEnum::Pending->value)->count();

        return [
            'total_appointments'     => $totalAppointments,
            'todays_appointments'    => $todaysAppointments,
            'completed_appointments' => $completedAppointments,
            'pending_appointments'   => $pendingAppointments,
        ];
    }

    // public function appointmentFees(Request $request): void
    // {
    //     $validator = Validator::make($request->all(), [
    //         'appointment_number' => 'required',
    //         'amount' => 'required'
    //     ]);

    //     $this->checkValidationService->checkValidation($validator);
    // }

    // /**
    //  * Summary of getDoctorAndPatientAndFrontDesk
    //  * @param mixed $appointment
    //  * @return array{appointment_number: mixed, doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, front_desk_user_email: mixed, front_desk_user_name: mixed, front_desk_user_phone: mixed, patient_email: mixed, patient_name: mixed, patient_number: mixed, patient_phone: mixed}
    //  */
    // private function getDoctorAndPatientAndFrontDesk($appointment)
    // {
    //     return [
    //         'doctor_name' => $appointment->doctor_name ?? '',
    //         'doctor_email' => $appointment->doctor_email ?? '',
    //         'doctor_phone' => $appointment->doctor_phone ?? '',
    //         'patient_name' => $appointment->patient_name ?? '',
    //         'patient_phone' => $appointment->patient_phone ?? '',
    //         'patient_email' => $appointment->patient_email ?? '',
    //         'patient_number' => $appointment->patient_number ?? '',
    //         'appointment_number' => $appointment->appointment_number ?? '',
    //         'front_desk_user_name' => $appointment->front_desk_user_name ?? '',
    //         'front_desk_user_email' => $appointment->front_desk_user_email ?? '',
    //         'front_desk_user_phone' => $appointment->front_desk_user_phone ?? '',
    //     ];
    // }

    // /**
    //  * Summary of getAppointmentRequiredData
    //  * @param string $id
    //  * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
    //  * @return array{appointment_number: mixed, doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, front_desk_user_email: mixed, front_desk_user_name: mixed, front_desk_user_phone: mixed, patient_email: mixed, patient_name: mixed, patient_number: mixed, patient_phone: mixed}
    //  */
    // public function getAppointmentRequiredData(string $id)
    // {
    //     $appointmentData = Appointments::findOrFail($id);
    //     if (!$appointmentData) {
    //         throw new NotFoundHttpException('Appointment data not found.');
    //     }
    //     return $this->getDoctorAndPatientAndFrontDesk($appointmentData);
    // }

}
