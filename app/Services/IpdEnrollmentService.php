<?php

namespace App\Services;

use App\Models\IPD;
use App\Models\Consultations;
use App\Contracts\FilterContract;
use Illuminate\Http\Request;
use App\Enums\Appointment\StatusEnum;
use App\Enums\Consultation\TypeEnum;
use Illuminate\Pagination\Paginator;
use App\Attributes\Transactional;
use App\Enums\RemovedEnums;
use App\Services\Shared\AppointmentHelperService;
use App\Services\Shared\ExaminationHelperService;
use App\Services\Shared\VitalHelperService;
use App\Services\PaymentService;
use App\Services\AllopathyService;
use App\Models\Allopathy;
use App\Models\Appointments;
use App\Models\Examination;
use App\Models\NonProctology;   
use App\Models\Proctology;
use App\Models\User;
use App\Models\Patient;
use App\Models\Vital;
use App\Services\Master\MedicinesService;
use App\Services\Shared\ConsultationAppointmentsHelperService;
use App\Services\Users\UserService;
use App\Traits\ConsultationsValidation;
use App\Services\ConsultationComorbiditiesService;
use Illuminate\Support\Facades\Log;

class IpdEnrollmentService implements FilterContract
{
    private $columns;
    private $filters;
    private $vitalValidationColumns;
    private $appointmentHelperService;
    private $examinationHelperService;
    private $consultationComorbitiesService;
    private $proctologyService;
    private $nonProctologyService;
    private $allopathyService;
    private $paymentService;
    private $medicineService;

    public function __construct(
        VitalHelperService $vitalHelperService,
        AppointmentHelperService $appointmentHelperService,
        ExaminationHelperService $examinationHelperService,
        ProctologyService $proctologyService,
        NonProctologyService $nonProctologyService,
        AllopathyService $allopathyService,
        ConsultationComorbiditiesService $consultationComorbitiesService,
        PaymentService $paymentService,
        MedicinesService $medicineService
    ) {
        $this->columns = Consultations::$column;
        $this->vitalHelperService = $vitalHelperService;
        $this->appointmentHelperService = $appointmentHelperService;
        $this->examinationHelperService = $examinationHelperService;
        $this->proctologyService = $proctologyService;
        $this->nonProctologyService = $nonProctologyService;
        $this->allopathyService = $allopathyService;
        $this->consultationComorbitiesService = $consultationComorbitiesService;
        $this->paymentService = $paymentService;
        $this->medicineService = $medicineService;
    }

    /**
     * Get all consultations with advice_admission = 1 (eligible for IPD enrollment)
     * Excludes consultations that are already enrolled in IPD
     */
    public function all(?Request $request)
    {
        // Prefix all columns with the table name to avoid ambiguity
        $columns = array_map(function ($col) {
            return "consultations.$col";
        }, $this->columns);
        return $this->allConsultation($request)->select($columns)->paginate(env('PAGINATION', 25));

    }

    public function allConsultation(?Request $request)
    {
        $enrolledConsultationIds = IPD::whereNotNull('consultation_id')->pluck('consultation_id')->toArray();
        
        $consultations = Consultations::query();
        // Join with appointments table to enable sorting by appointment fields
        $consultations=$consultations->where('advice_admition', 1)
            ->whereNotIn('id', $enrolledConsultationIds)
            ->where('removed', RemovedEnums::Active->value)->onlyDoctorRelatedIfDoctorLogedIn();

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

        // $sql = vsprintf(
        //     str_replace('?', "'%s'", $consultations->toSql()),
        //     $consultations->getBindings()
        // );
        // var_dump($sql);
        // die;
        return $consultations;
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
                foreach ($this->columns as $column) {
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

        $consultations->patient_age  = $consultations->patient_dob  = '';
        $patient                     = Patient::where('id', $consultations->patient_id)->first();
        if (! is_null($patient)) {
            $consultations->patient_age = $patient->age;
            $consultations->patient_dob = $patient->dob;
            $consultations->patient_document=$patient->getDocuments();
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

        return ['proctologyOrNonProctology' => $proctologyOrNonProctology, 'consultations' => $consultations, 'vitals' => $vitals, 'examinations' => $examinations, 'medicines' => $this->medicineService->relatedMedicinesByIds(['id', 'medicine_name', 'dosage_form', 'strength', 'strength_unit'], $consultations->medical_id, true), "consultationComorbidities" => $consultationComorbidities, 'additionalCost' => $additionalCost];
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
     * Get consultations eligible for IPD enrollment as dropdown list
     * Excludes consultations that are already enrolled in IPD
     */
    public function listForDropdown()
    {
        $enrolledConsultationIds = IPD::pluck('consultation_id')->toArray();
        
        return Consultations::where('advice_admission', 1)
            ->whereNotIn('id', $enrolledConsultationIds)
            ->with(['patient', 'doctor'])
            ->select('id', 'appointment_number', 'patient_id', 'doctor_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'label' => $consultation->appointment_number . ' - ' . 
                        ($consultation->patient ? $consultation->patient->first_name . ' ' . $consultation->patient->last_name : 'N/A'),
                    'patient_id' => $consultation->patient_id,
                    'doctor_id' => $consultation->doctor_id,
                ];
            });
    }

    /**
     * Enroll a patient to IPD from consultation
     */
    public function enrollPatient(Request $request)
    {
        $consultation = Consultations::where('id', $request->consultation_id)
            ->where('advice_admission', 1)
            ->with('patient')
            ->firstOrFail();

        $ipdData = [
            'patient_id' => $consultation->patient_id,
            'patient_name' => $consultation->patient->first_name . ' ' . $consultation->patient->last_name,
            'patient_email' => $consultation->patient->email ?? null,
            'patient_phone' => $consultation->patient->phone_no ?? null,
            'patient_age' => $consultation->patient->age ?? null,
            'patient_attendant_name' => $request->patient_attendant_name ?? null,
            'patient_attendant_phone' => $request->patient_attendant_phone ?? null,
            'patient_address' => $consultation->patient->address ?? null,
            'consultation_id' => $consultation->id,
            'admission_date_time' => $request->admission_date_time ?? now(),
            'ward_id' => $request->ward_id ?? null,
            'ward_number' => $request->ward_number ?? null,
            'ward_type' => $request->ward_type ?? null,
            'room_id' => $request->room_id ?? null,
            'room_type' => $request->room_type ?? null,
            'room_number' => $request->room_number ?? null,
            'bed_number' => $request->bed_number ?? null,
            'ipd_number' => $request->ipd_number ?? null,
        ];

        return IPD::create($ipdData);
    }

    /**
     * Get statistics for IPD enrollment
     */
    public function getStatistics()
    {
        return [
            'total_consultations_for_admission' => Consultations::where('advice_admission', 1)->count(),
            'enrolled_patients' => IPD::count(),
            'pending_enrollment' => Consultations::where('advice_admission', 1)
                ->whereNotIn('id', IPD::pluck('consultation_id'))
                ->count(),
        ];
    }

    public function delete($id)
    {
        $consultation=Consultations::where('id', $id)->first();
        if(!$consultation){
            throw new \Exception('Consultation not found');
        }
        $consultation->advice_admition = 0;
        $consultation->save();
    }   
}
