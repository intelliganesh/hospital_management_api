<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\FilterContract;
use App\Enums\Appointment\StatusEnum;
use App\Enums\Consultation\TypeEnum;
use App\Enums\RemovedEnums;
// use App\Models\Payment;
use App\Interceptors\ServiceInterceptor;
use App\Models\Allopathy;
use App\Models\Appointments;
use App\Models\Consultations;
use App\Models\Examination;
use App\Models\NonProctology;
// use App\Models\Master\Test;
use App\Models\Patient;
use App\Models\PatientFistula;
use App\Models\Proctology;
use App\Models\User;
use App\Models\Vital;
use App\Services\Master\MedicinesService;
use App\Services\PostSurgeryFollowUpService;
use App\Services\Shared\AppointmentHelperService;
use App\Services\Shared\ConsultationAppointmentsHelperService;
use App\Services\Shared\ExaminationHelperService;
use App\Services\Shared\VitalHelperService;
use App\Services\Users\UserService;
use App\Traits\ConsultationsValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \App\Models\Invoice;

class ConsultationService implements FilterContract
{

    use ConsultationsValidation;

    private $column;
    private $filters;
    private $container;
    private $userService;
    private $paymentService;
    private $medicineService;
    private $allopathyService;
    private $proctologyService;
    private $vitalHelperService;
    private $appointmentService;
    private $nonProctologyService;
    private $checkValidationService;
    private $vitalValidationColumns;
    private $appointmentHelperService;
    private $examinationHelperService;
    private $examinationValidationColumns;
    private $consultationComorbitiesService;
    private $consultationAppointmentService;
    private $invoiceService;
    private $invoiceColumns;
    private $postSurgeryFollowUpService;

    /**
     * Summary of __construct
     * @param \App\Services\Users\UserService $userService
     * @param \App\Services\PaymentService $paymentService
     * @param \App\Services\Master\MedicinesService $medicineService
     * @param \App\Services\AllopathyService $allopathyService
     * @param \App\Services\ProctologyService $proctologyService
     * @param \App\Services\Shared\VitalHelperService $vitalHelperService
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\NonProctologyService $nonProctologyService
     * @param \App\Services\Shared\AppointmentHelperService $appointmentHelperService
     * @param \App\Services\Shared\ExaminationHelperService $examinationHelperService
     * @param \App\Services\ConsultationComorbiditiesService $consultationComorbitiesService
     * @param \App\Services\Shared\ConsultationAppointmentsHelperService $consultationAppointmentService
     * @param \App\Services\InvoiceService $invoiceService
     * @param \App\Services\PostSurgeryFollowUpService $postSurgeryFollowUpService
     */
    public function __construct(
        UserService $userService,
        PaymentService $paymentService,
        MedicinesService $medicineService,
        AllopathyService $allopathyService,
        ProctologyService $proctologyService,
        VitalHelperService $vitalHelperService,
        CheckValidation $checkValidationService,
        NonProctologyService $nonProctologyService,
        AppointmentHelperService $appointmentHelperService,
        ExaminationHelperService $examinationHelperService,
        ConsultationComorbiditiesService $consultationComorbitiesService,
        ConsultationAppointmentsHelperService $consultationAppointmentService,
        InvoiceService $invoiceService,
        PostSurgeryFollowUpService $postSurgeryFollowUpService
    ) {
        $this->userService                    = $userService;
        $this->column                         = Consultations::$column;
        $this->paymentService                 = $paymentService;
        $this->filters                        = Consultations::$filters;
        $this->medicineService                = $medicineService;
        $this->allopathyService               = $allopathyService;
        $this->proctologyService              = $proctologyService;
        $this->vitalHelperService             = $vitalHelperService;
        $this->invoiceService                 = $invoiceService;
        $this->nonProctologyService           = $nonProctologyService;
        $this->checkValidationService         = $checkValidationService;
        $this->appointmentHelperService       = $appointmentHelperService;
        $this->examinationHelperService       = $examinationHelperService;
        $this->vitalValidationColumns         = Vital::$vitalValidationColumns;
        $this->consultationComorbitiesService = $consultationComorbitiesService;
        $this->consultationAppointmentService = $consultationAppointmentService;
        $this->examinationValidationColumns   = Examination::$examinationValidationColumns;
        $this->invoiceColumns                 = Invoice::$columns;
        $this->postSurgeryFollowUpService     = $postSurgeryFollowUpService;
    }

    /**
     * Summary of all
     * @param mixed $request
     */
    public function all(?Request $request)
    {
        // Prefix all columns with the table name to avoid ambiguity
        $columns = array_map(function ($col) {
            return "consultations.$col";
        }, $this->column);
        // Add appointment_reference_number from external_appointments
        $columns[] = 'external_appointments.appointment_reference_number as external_appointment_reference_number';
        $columns[] = 'external_appointments.appointment_type as external_appointment_type';
        return $this->allConsultation($request, false)
            ->leftJoin('external_appointments', 'consultations.external_appointment_id', '=', 'external_appointments.id')
            ->select($columns)
            ->paginate(env('PAGINATION', 25));
    }

    public function allConsultation(?Request $request, $upComing = false)
    {
        $consultations = Consultations::query();
        // Join with appointments table to enable sorting by appointment fields

        if ($upComing) {
            $consultations = $consultations->upcomingFirst();
        }
        // else{
        //     $consultations = $consultations->leftJoin('appointments', 'consultations.appointment_id', '=', 'appointments.id');
        // }

        $consultations = $consultations->where('removed', RemovedEnums::Active->value)->onlyDoctorRelatedIfDoctorLogedIn();
        // $consultations = $consultations->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $consultations = $this->search($request->input('search'), $consultations);
        }

        if ($request->has('sort_by')) {
            // $sortBy = $request->sort_by ?? 'next_visit_date';
            // $consultations = $consultations->orderBy($sortBy, $sortOrder);
            $sortBy    = $request->sort_by ?? 'appointment_date';
            $sortOrder = $request->sort_order ?? 'desc';

            if ($sortBy === 'status') {
                $statusesAsc   = array_column(StatusEnum::cases(), 'value');
                $statusesDesc  = array_reverse($statusesAsc);
                $orderList     = $sortOrder === 'asc' ? $statusesAsc : $statusesDesc;
                $order         = "'" . implode("','", $orderList) . "'";
                $consultations = $consultations->orderByRaw("FIELD(consultations.status, $order)");
            } elseif ($sortBy == "appointment_date") {
                // $consultations = $consultations->orderBy("appointments.$sortBy", $sortOrder);
                $consultations = $consultations->orderBy(
                    Appointments::select('appointment_date')
                        ->whereColumn('appointments.id', 'consultations.appointment_id'),
                    $sortOrder
                );
            } elseif ($sortBy == "department_type") {
                $consultations = $consultations->orderBy("consultations.type", $sortOrder);
            } else {
                $consultations = $consultations->orderBy("consultations.$sortBy", $sortOrder);
            }
        } else {
            $consultations = $consultations->orderBy('consultations.created_at', 'desc');
        }
        if ($request->has('multiple_filter')) {
            $consultations = $this->filterMultipleFields($request->multiple_filter, $consultations);
        }
        if ($request->has("from_date") && $request->has("to_date")) {
            $consultations = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $consultations);
        }

        return $consultations;
    }

    /**
     * Summary of allforUpComingAppointments
     * @param mixed $request
     * @return mixed
     */
    public function allforUpComingAppointments(?Request $request): mixed
    {
        //->upcomingFirst()
        $columns = array_map(function ($col) {
            return "consultations.$col";
        }, $this->column);

        return $this->allConsultation($request, true)->select($columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filters as $column) {
            if (! empty($request[$column])) {
                if ($column == "patient_name") {
                    $data->where($column, 'like', '%' . $request[$column] . '%');
                } else {
                    $data->where("consultations.$column", $request[$column]);
                }
            }
        }

        return $data;

        // if (isset($request['next_visit_date']) && $request['next_visit_date'] != null && $request['next_visit_date'] != '') {
        //     $data->where('next_visit_date', $request['next_visit_date']);
        // }

        // if (isset($request['patient_number']) && $request['patient_number'] != null && $request['patient_number'] != '') {
        //     $data->where('patient_number', $request['patient_number']);
        // }

        // if (isset($request['appointment_number']) && $request['appointment_number'] != null && $request['appointment_number'] != '') {
        //     $data->where('appointment_number', $request['appointment_number']);
        // }

        // if (isset($request['doctor_name']) && $request['doctor_name'] != null && $request['doctor_name'] != '') {
        //     $data->where('doctor_name', $request['doctor_name']);
        // }

        // if (isset($request['status']) && $request['status'] != null && $request['status'] != '') {
        //     $data->where('status', $request['status']);
        // }

        // if (isset($request['payment_status']) && $request['payment_status'] != null && $request['payment_status'] != '') {
        //     $data->where('payment_status', $request['payment_status']);
        // }
        // return $data;

    }

    /**
     * Summary of get
     * @param string $id
     * @return array{consultations: Consultations, examinations: \App\Models\Examination, vitals: \App\Models\Vital}
     */
    public function get(string $id): mixed
    {
        $consultations = Consultations::findOrFail($id);
        // if (!$consultations) {
        //     throw new NotFoundHttpException('Consultations data not found.');
        // }
        $vitals                    = $this->vitalHelperService->getByDynamicColumn($id, 'consultation_id');
        $examinations              = $this->examinationHelperService->getByDynamicColumn($id, 'consultation_id');
        $proctologyOrNonProctology = null;
        if ($consultations->type === TypeEnum::Proctology->value) {
            $proctologyOrNonProctology = Proctology::where('consultation_id', $consultations->id)->first();

            $fields               = ['no_of_fistula', 'no_of_tracks_in_one_fistula', 'no_of_external_opening_position', 'external_opening_position', 'internal_opening_position', 'internal_opening_distance', 'any_other', 'no_of_secondary_opening_position', 'secondary_opening_position', 'secondary_anal_valve', 'other_investigation', 'anal_valve', 'type_of_crypt', 'crypt_cause', 'type_of_fistula_position', 'type_of_fistula_sphincter', 'basis_of_high_low_riding', 'distant_visceral_communication', 'sono_fistula_gram', 'mri_fistula_gram', 'sonologist_findings', 'fistula_recurrence', 'fistula_recurrence_surgery_count', 'fistula_remark', 'posterior_fistulous_angle', 'sonologist'];
            $last_fistula_details = PatientFistula::where('patient_id', $consultations->patient_id)->orderBy('updated_at', 'desc')->select($fields)->first();
            if (! is_null($last_fistula_details) && ! is_null($proctologyOrNonProctology)) {
                $proctologyArray = $proctologyOrNonProctology->toArray();
                // Remove fields that are present in $fields array
                $proctologyArray           = array_diff_key($proctologyArray, array_flip($fields));
                $proctologyOrNonProctology = (object) array_merge($proctologyArray, $last_fistula_details->toArray());
            }

        } else if ($consultations->type === TypeEnum::NonProctology->value) {
            $proctologyOrNonProctology = NonProctology::where('consultation_id', $consultations->id)->first();
        } else if ($consultations->type === TypeEnum::Allopathy->value) {
            $proctologyOrNonProctology = Allopathy::where('consultation_id', $consultations->id)->first();
        }
        $consultationComorbidities = $this->consultationComorbitiesService->getByDynamicColumn($id, 'consultation_id');
        $allPayments               = $this->paymentService->getByColumnNameDynamic('consultation_id', $id);

        $totalAmount = 0;
        foreach ($allPayments as $pmt) {
            if ($pmt->include_in_invoice) {
                $totalAmount += (float) $pmt->amount;
            }
        }

        $consultations->patient_age = $consultations->patient_dob = '';
        $patient                    = Patient::where('id', $consultations->patient_id)->first();
        if (! is_null($patient)) {
            $consultations->patient_age      = $patient->age;
            $consultations->patient_dob      = $patient->dob;
            $consultations->patient_document = $patient->getDocuments();
            $consultations->relinkPatient    = 0;
        } else {
            $consultations->relinkPatient = 1;
        }

        if (! is_null($consultations->external_appointment_id)) {
            $consultations->meeting_link      = $consultations->getExternalAppointment?->meeting_link;
            $consultations->consutlation_type = $consultations->getExternalAppointment?->appointment_type;
        } else {
            $consultations->consutlation_type = 'Offline';
        }

        $consultations->total_amount    = $totalAmount;
        $consultations->discount_amount = 0;
        if (! is_null($proctologyOrNonProctology)) {
            if ((float) $proctologyOrNonProctology->consultation_discount != 0 && $proctologyOrNonProctology->consultation_discount != null && $proctologyOrNonProctology->consultation_discount != "") {
                $consultations->discount_amount = ($totalAmount / 100) * $proctologyOrNonProctology->consultation_discount;
                $consultations->total_amount    = $totalAmount - $consultations->discount_amount;

            }
        }

        // Filter out Consultation Cost from additionalCost and convert to array of objects
        $additionalCost = $allPayments->reject(function ($payment) {
            return $payment->amount_for === 'Consultation Cost';
        })->values()->toArray();

        $postSurgeryFollowUp     = $this->postSurgeryFollowUpService->getByDynamicColumn($consultations->id, 'consultation_id');
        $consultations->currency = Invoice::where('consultation_id', $consultations->id)->value('currency') ?? '₹';
        return ['proctologyOrNonProctology' => $proctologyOrNonProctology, 'consultations' => $consultations, 'vitals' => $vitals, 'examinations' => $examinations, 'medicines' => $this->medicineService->relatedMedicinesByIds(['id', 'medicine_name', 'dosage_form', 'strength', 'strength_unit'], $consultations->medical_id, true), "consultationComorbidities" => $consultationComorbidities, 'additionalCost' => $additionalCost, 'postSurgeryFollowUp' => $postSurgeryFollowUp];
    }

    /**
     * Summary of getByColumnNameDynamic
     * @param mixed $columnName
     * @param mixed $columnValue
     * @return Consultations|null
     */
    public function getByColumnNameDynamic($columnName, $columnValue)
    {
        return Consultations::where($columnName, $columnValue)->first();
    }

    /**
     * Summary of consultationList
     * @return \Illuminate\Database\Eloquent\Collection<int, Consultations>
     */
    public function consultationList()
    {
        return Consultations::select('id', 'appointment_number')->get();
    }

    /**
     * Summary of patientConsultationList
     * @param \Illuminate\Http\Request $request
     */
    public function patientConsultationList(Request $request)
    {
        return Consultations::select('id', 'appointment_number')->where('patient_id', $request->patient_id)->get();
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create consultations record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $fields               = ['no_of_fistula', 'no_of_tracks_in_one_fistula', 'no_of_external_opening_position', 'external_opening_position', 'internal_opening_position', 'internal_opening_distance', 'any_other', 'no_of_secondary_opening_position', 'secondary_opening_position', 'secondary_anal_valve', 'other_investigation', 'anal_valve', 'type_of_crypt', 'crypt_cause', 'type_of_fistula_position', 'type_of_fistula_sphincter', 'basis_of_high_low_riding', 'distant_visceral_communication', 'sono_fistula_gram', 'mri_fistula_gram', 'sonologist_findings', 'fistula_recurrence', 'fistula_recurrence_surgery_count', 'fistula_remark', 'posterior_fistulous_angle', 'sonologist'];
        $last_fistula_details = PatientFistula::where('patient_id', $request->patient_id)->orderBy('updated_at', 'desc')->select($fields)->first();
        $data                 = array_merge($request->all(), $this->appointmentHelperService->getAppointmentRequiredData($request->appointment_id));
        if (! is_null($last_fistula_details)) {
            $data = array_merge($data, $last_fistula_details->toArray());
        }
        Consultations::create($data);
    }

    /**
     * @deprecated this function is not in use
     */
    public function update(Request $request, string | null $id): void
    {

    }

    /**
     * Summary of updateConsultation
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return string
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update consultations and examinations record within a secure transaction')]
    public function updateConsultation(Request $request, string | null $id): string | null
    {
        if ($request->status === StatusEnum::Cancelled->value) {
            $proxiedService = ServiceInterceptor::intercept($this->consultationAppointmentService);
            $proxiedService->delete($id);
        }

        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $consultations = Consultations::findOrFail($id);

        if (isset($request->co_morbidities_data) && count($request->co_morbidities_data) > 0) {
            foreach ($request->co_morbidities_data as $coMorbiditiesData) {
                // $this->consultationComorbitiesService->updateOrCreate(['id' => $coMorbiditiesData['comorbidities_id'], 'consultation_id' => $id], [
                $this->consultationComorbitiesService->updateOrCreate(['comorbidities_id' => $coMorbiditiesData['comorbidities_id'], 'consultation_id' => $id], [
                    'consultation_id'  => $id,
                    'name'             => $coMorbiditiesData['name'],
                    'is_chronic'       => $coMorbiditiesData['is_chronic'],
                    'description'      => $coMorbiditiesData['description'],
                    'comorbidities_id' => $coMorbiditiesData['comorbidities_id'],
                ]);
                // if (isset($request->test_in_same_hospital) && $request->test_in_same_hospital) {
                // }
            }
        }
        // $this->consultationComorbitiesService->updateOrCreate(['id' => $request->comorbidities_id, 'consultation_id' => $id], [
        //     'name' => $request->name,
        //     'consultation_id' => $id,
        //     'is_chronic' => $request->is_chronic,
        //     'description' => $request->description,
        //     'comorbidities_id' => $request->comorbidities_id
        // ]);

        // if (isset($request->test_in_same_hospital) && $request->test_in_same_hospital) {
        // Payment::where('consultation_id', $consultations->id)
        //     ->update(['amount' => $consultations->fees]);
        // }

        // $payment = Payment::query();
        $doctor        = User::where('id', $request->doctor_id)->first();
        $frontDeskUser = User::where('id', $request->front_desk_user_id)->first();
        $patient       = Patient::where('id', $request->patient_id)->first();
        $appointment   = Appointments::where('id', $request->appointment_id)->first();

        $documentId         = null;
        $discountPercentage = 0;
        if ($request->type === TypeEnum::Proctology->value) {
            $proctologyCreatedData = $this->proctologyService->createOrUpdate($request, $id);
            $discountPercentage    = $proctologyCreatedData->consultation_discount;
            // $discountAmount = $proctologyCreatedData->discount_amount;
            $documentId = $proctologyCreatedData->id;
        } else if ($request->type === TypeEnum::NonProctology->value) {
            $nonProctologyCreatedData = $this->nonProctologyService->createOrUpdate($request, $id);
            $discountPercentage       = $nonProctologyCreatedData->consultation_discount;
            $documentId               = $nonProctologyCreatedData->id;
        } else if ($request->type === TypeEnum::Allopathy->value) {
            $allopathyCreatedData = $this->allopathyService->createOrUpdate($request, $id);
            $discountPercentage   = $allopathyCreatedData->consultation_discount;
            $documentId           = $allopathyCreatedData->id;
        }
        // if (isset($request->test_in_same_hospital) && $request->test_in_same_hospital) {
        //     $testArray = json_decode($request->tests, true);
        //     foreach ($testArray as $test) {

        //         $test = Test::where('id', $test['value'])->first();

        //         $this->paymentService->updateByColumns([
        //             'consultation_id' => $id,
        //             'doctor_id' => $doctor->id,
        //             'patient_id' => $patient->id,
        //             'doctor_name' => $doctor->name,
        //             'doctor_email' => $doctor->email,
        //             "amount_for" => $test->test_name,
        //             'patient_email' => $patient->email,
        //             'doctor_phone' => $doctor->phone_no,
        //             'appointment_id' => $appointment->id,
        //             'patient_phone' => $patient->phone_no,
        //             'front_desk_user_id' => $frontDeskUser->id,
        //             'patient_number' => $patient->patient_number,
        //             'front_desk_user_name' => $frontDeskUser->name,
        //             "amount" => $test->test_price + $test->tax_price,
        //             'front_desk_user_email' => $frontDeskUser->email,
        //             'front_desk_user_phone' => $frontDeskUser->phone_no,
        //             'patient_name' => $patient->first_name . ' ' . $patient->last_name,
        //         ], ['consultation_id' => $id, 'amount_for' => $test->test_name]);
        //     }
        // }

        $allPayments = [];

        if (isset($request->Service) && $request->Service) {
            $additionalCost = explode(',', $request->Service);
            foreach ($additionalCost as $cost) {
                $allPayments[] = explode("#", $cost)[0];
                $this->paymentService->updateByColumns(
                    [
                        'consultation_id'       => $id,
                        'doctor_id'             => $doctor->id,
                        'patient_id'            => $patient->id,
                        'doctor_name'           => $doctor->name,
                        'doctor_email'          => $doctor->email,
                        'patient_email'         => $patient->email,
                        'doctor_phone'          => $doctor->phone_no,
                        'appointment_id'        => $appointment->id,
                        'patient_phone'         => $patient->phone_no,
                        'front_desk_user_id'    => $frontDeskUser->id,
                        'discount_percentage'   => $discountPercentage,
                        'patient_number'        => $patient->patient_number,
                        'front_desk_user_name'  => $frontDeskUser->name,
                        'front_desk_user_email' => $frontDeskUser->email,
                        'front_desk_user_phone' => $frontDeskUser->phone_no,
                        'amount_for'            => explode("#", $cost)[0],
                        'amount'                => (float) explode("#", $cost)[1],
                        'patient_name'          => $patient->first_name . ' ' . $patient->last_name,
                        'discount_amount'       => (float) explode("#", $cost)[1] * ($discountPercentage / 100),
                    ],
                    [
                        'consultation_id' => $id,
                        'amount_for'      => explode("#", $cost)[0],
                    ]
                );
                // $payment->updateOrCreate([
                //     'consultation_id' => $id,
                // ], [
                //     'consultation_id' => $id,
                //     'amount' => $cost
                // ]);
            }
        }

        // $amountArray = json_decode($request->amount, true);
        // $amountArray = $request->amount;
        // if (isset($amountArray) && $amountArray) {
        //     $this->paymentService->updateByColumns([
        //         'amount' => $amountArray,
        //         'consultation_id' => $id,
        //         'doctor_id' => $doctor->id,
        //         'patient_id' => $patient->id,
        //         'doctor_name' => $doctor->name,
        //         'amount_for' => 'Estimated Cost',
        //         'doctor_email' => $doctor->email,
        //         'patient_email' => $patient->email,
        //         'doctor_phone' => $doctor->phone_no,
        //         'appointment_id' => $appointment->id,
        //         'patient_phone' => $patient->phone_no,
        //         'front_desk_user_id' => $frontDeskUser->id,
        //         'patient_number' => $patient->patient_number,
        //         'front_desk_user_name' => $frontDeskUser->name,
        //         'front_desk_user_email' => $frontDeskUser->email,
        //         'front_desk_user_phone' => $frontDeskUser->phone_no,
        //         'patient_name' => $patient->first_name . ' ' . $patient->last_name,
        //     ], ['consultation_id' => $id, 'amount_for' => 'Estimated Cost']);
        // }

        if (isset($request->consultation_amount) && $request->consultation_amount) {
            $allPayments[] = 'Consultation Cost';
            $this->paymentService->updateByColumns([
                'consultation_id'       => $id,
                'doctor_id'             => $doctor->id,
                'patient_id'            => $patient->id,
                'doctor_name'           => $doctor->name,
                'doctor_email'          => $doctor->email,
                'patient_email'         => $patient->email,
                'doctor_phone'          => $doctor->phone_no,
                'amount_for'            => 'Consultation Cost',
                'appointment_id'        => $appointment->id,
                'patient_phone'         => $patient->phone_no,
                'amount'                => $request->consultation_amount,
                'front_desk_user_id'    => $frontDeskUser->id,
                'discount_percentage'   => $discountPercentage,
                'patient_number'        => $patient->patient_number,
                'front_desk_user_name'  => $frontDeskUser->name,
                'front_desk_user_email' => $frontDeskUser->email,
                'front_desk_user_phone' => $frontDeskUser->phone_no,
                'patient_name'          => $patient->first_name . ' ' . $patient->last_name,
                'discount_amount'       => $request->consultation_amount * ($discountPercentage / 100),
            ], ['consultation_id' => $id, 'amount_for' => 'Consultation Cost']);
        }

        if (count($allPayments) > 0) {
            // Delete all payments for this consultation except those in the allPayments array
            $this->paymentService->deletePaymentsByConsultationId($id, $allPayments);
        }

        $appointmentRequiredData = $this->appointmentHelperService->updateFieldDynamicAndReturn($request->appointment_id, [
            'status'         => $request->status,
            'complaint'      => $request->complaint,
            'patient_id'     => $request->patient_id,
            'patient_name'   => $request->patient_name,
            'patient_email'  => $patient->email,
            'patient_phone'  => $patient->phone_no,
            'patient_number' => $patient->patient_number,
        ]);
        $updateData = array_merge($request->all(), $appointmentRequiredData);

        $consultations->update($updateData);

        $payments      = $this->paymentService->getByColumnNameDynamic('consultation_id', $id);
        $balanceAmount = 0;
        if (count($payments) > 0) {
            foreach ($payments as $pmt) {
                if ($pmt->include_in_invoice) {
                    $balanceAmount += $pmt->amount;
                }
            }
        }

        $check = Invoice::where('consultation_id', $id)->first();
        if ($request->status == StatusEnum::Completed->value && is_null($check)) {
            if (is_null($consultations->external_appointment_id)) {
                $this->invoiceService->create(
                    new Request(array_merge(
                        $consultations->only($this->invoiceColumns),
                        [
                            'collected_amount' => 0,
                            'balanced_amount'  => 0,
                            'consultation_id'  => $consultations->id,
                            'currency'         => '₹',
                        ]
                    ))
                );
            } else {
                $external_appointment = ExternalAppointment::where('id', $consultations->external_appointment_id)->first();
                $external_appointment->update(['status' => StatusEnum::Completed->value]);
            }
        }

        $invoice = Invoice::where('consultation_id', $id)->first();
        if ($invoice) {
            if ($invoice->discount_percentage > 0) {
                $balanceAmount = $balanceAmount - ($balanceAmount * ($invoice->discount_percentage / 100));
                $invoice->update(['balanced_amount' => $balanceAmount]);
            } else {
                $invoice->update(['balanced_amount' => $balanceAmount]);
            }
        }

        $this->examinationHelperService->updateOrCreateByColumnName(array_merge($request->only($this->examinationValidationColumns), ['consultation_id' => $id], array_intersect_key($appointmentRequiredData, array_flip($this->examinationValidationColumns))), $id, 'consultation_id');
        $this->vitalHelperService->updateOrCreateByColumnName(array_merge($request->only($this->vitalValidationColumns), ['consultation_id' => $id]), $id, 'consultation_id');

        $last_fistula_details = PatientFistula::where('patient_id', $updateData['patient_id'])->orderBy('updated_at', 'desc')->first();
        if ($last_fistula_details) {
            $last_fistula_details->update($updateData);
        } else {
            $updateData['created_by'] = auth()->user()->id;
            $updateData['updated_by'] = auth()->user()->id;
            PatientFistula::create($updateData);
        }
        if (isset($request->post_surgery_details) && $request->post_surgery_details == "yes") {
            $postSurgeryDetails = $this->postSurgeryFollowUpService->updateOrCreatePostSurgeryDetails($consultations->id, json_decode($request->post_surgery_followup), true);
        }

        return $documentId;
    }

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    // #[Transactional(secure: true, requiredRole: null, description: 'Delete consultations and examinations and vitals record within a secure transaction')]
    public function delete(string $id): void
    {
        $proxiedService = ServiceInterceptor::intercept($this->consultationAppointmentService);
        $proxiedService->delete($id);
        // $patient = Consultations::findOrFail($id);
        // // if (!$patient) {
        // //     throw new NotFoundHttpException('Consultationa data not found.');
        // // }
        // $this->vitalHelperService->deleteByDynamicColumn($id, 'consultation_id');
        // $this->examinationHelperService->deleteByDynamicColumn($id, 'consultation_id');
        // $patient->delete();
    }

    /**
     * Summary of search
     * @param string $search
     * @param mixed $user
     */
    public function search(string $searchText, $data)
    {
        if (! empty($searchText)) {
            $data->where(function ($query) use ($searchText) {
                foreach ($this->column as $column) {
                    $query->orWhere("consultations.$column", 'like', '%' . $searchText . '%');
                }
            });
        }

        return $data;
    }

    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     */
    public function filterByDateRange(string $searchText, $data)
    {
        $dates = explode("|", $searchText);

        // Join with appointments table and filter by appointment_date
        $data->join('appointments', 'consultations.appointment_id', '=', 'appointments.id')
            ->whereBetween('appointments.appointment_date', [$dates[0], $dates[1]]);

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
     * Get consultation statistics
     *
     * @return array
     */
    public function getStatistics()
    {
        // Get total consultations count
        $totalConsultations = Consultations::where('removed', RemovedEnums::Active->value)->onlyDoctorRelatedIfDoctorLogedIn()->count();

        // Get today's consultations count
        $todaysConsultations = Consultations::whereDate('created_at', now()->toDateString())->onlyDoctorRelatedIfDoctorLogedIn()->where('removed', RemovedEnums::Active->value)->count();

        // Get completed consultations count for today
        $completedConsultations = Consultations::whereDate('created_at', now()->toDateString())->onlyDoctorRelatedIfDoctorLogedIn()
            ->where('status', StatusEnum::Completed->value)
            ->where('removed', RemovedEnums::Active->value)
            ->count();

        return [
            'total_consultations'     => $totalConsultations,
            'todays_consultations'    => $todaysConsultations,
            'completed_consultations' => $completedConsultations,
        ];
    }

    public function getConsultationDatesForPatient(string $patient_id)
    {
        return Appointments::where('patient_id', $patient_id)
                            ->where('status', 'Completed')
                            ->select('appointment_number','appointment_date', 'appointment_time', 'id as appointment_id')
                            ->orderByDesc('appointment_date')
                            ->orderByDesc('appointment_time')
                            ->get();
    }
}
