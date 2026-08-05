<?php
namespace App\Services;

use App\Models\IPD;
use App\Models\Patient;
use App\Contracts\CRUDContract;
use App\Enums\AddmissionTypeEnum;
use App\Attributes\Transactional;
use App\Enums\ReferralSourceEnum;
use App\Contracts\FilterContract;
use App\Enums\ServiceType;
use App\Models\OPD;
use App\Services\Users\UserService;
use App\Traits\OPDValidation;
use AutoIdGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OPDService extends UserService implements CRUDContract, FilterContract
{

    use OPDValidation;
    protected $userService;
    protected $patientService;
    protected $appointmentService;
    private $checkValidationOPDService;

    /**
     * @param \App\Services\PatientService $patientService
     * @param \App\Services\Users\UserService $userService
     * @param \App\Services\AppointmentsService $appointmentService
     * @param \App\Services\CheckValidation $checkValidationOPDService
     */
    public function __construct(PatientService $patientService, AppointmentsService $appointmentService, UserService $userService, CheckValidation $checkValidationOPDService)
    {
        $this->userService               = $userService;
        $this->patientService            = $patientService;
        $this->appointmentService        = $appointmentService;
        $this->checkValidationOPDService = $checkValidationOPDService;
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return OPD
     */
    public function get(string $id): array
    {
        $opd = OPD::findOrFail($id);
        if (! $opd) {
            throw new NotFoundHttpException('OPD data not found.');
        }
        $patient     = $this->patientService->get($opd->patient_id);
        $doctor      = $this->userService->get($opd->referred_to_doctor_id);
        $appointment = $this->appointmentService->get($opd->appointment_id);
        return ['opd' => $opd, 'patient' => $patient, "doctor" => ['id' => $doctor->id, 'name' => $doctor->name], "appointment" => ['id' => $appointment->id, 'number' => $appointment->appointment_number]];
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $opd = OPD::findOrFail($id);
        if (! $opd) {
            throw new NotFoundHttpException('OPD data not found.');
        }
        $opd->delete();
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, string | null $id): void
    {
        $validator = $this->validate($request);
        $this->checkValidationOPDService->checkValidation($validator);
        $opd = OPD::findOrFail($id);
        if (! $opd) {
            throw new NotFoundHttpException('OPD data not found.');
        }
        $opd->update($request->all());
    }

    public function show()
    {
        $doctor = $this->getUserByRole('doctor')->makeHidden(['email', 'phone', 'image', 'role']);
        return ['doctor' => $doctor];
    }

    /**
     * @deprecated This method is not used.
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        // write code here
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $validator = $this->validate($request);
        $this->checkValidationOPDService->checkValidation($validator);
        OPD::create(array_merge($request->all(), ['opd_number' => AutoIdGenerate::generateId(ServiceType::OPD)]));
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request = null): mixed
    {
        $opd = OPD::query();
        if ($request->has('search')) {
            $opd = $this->search($request->input('search'), $opd);
        }
        if ($request->has('sort_by')) {
            $sortBy    = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            if ($sortBy === 'status') {
                $customOrder = ['Draft', 'Pending', 'Approved', 'Active', 'Completed', 'Resolved', 'Unresolved', 'Cancelled', 'Inactive'];
                $orderString = "FIELD(status, '" . implode("','", $customOrder) . "')" . ($sortOrder === 'desc' ? ' DESC' : '');
                $opd         = $opd->orderByRaw($orderString);
            } else {
                $opd = $opd->orderBy($sortBy, $sortOrder);
            }
        }
        return $opd->select('id', 'opd_number', 'status', 'visit_date')->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of search
     * @param string $search
     * @param mixed $user
     */
    public function search(string $searchText, $data)
    {
        // return $data->where('opd_number', 'like', '%' . $searchText . '%')
        //     ->orWhere('status', 'like', '%' . $searchText . '%')->orWhereHas('patient_id', function ($query) use ($searchText) {
        //         $query->where("first_name", 'like', '%' . $searchText . '%');
        //     })->orWhereHas("appointment_id", function ($query) use ($searchText) {
        //         $query->where("type", 'like', '%' . $searchText . '%');
        //     });

        return $data->where(function ($q) use ($searchText) {
            $q->where('opd_number', 'like', '%' . $searchText . '%')
                ->orWhere('status', 'like', '%' . $searchText . '%')
                ->orWhereHas('patient', function ($subQuery) use ($searchText) {
                    $subQuery->where('first_name', 'like', '%' . $searchText . '%');
                })
                ->orWhereHas('appointment', function ($subQuery) use ($searchText) {
                    $subQuery->where('type', 'like', '%' . $searchText . '%');
                });
        });
    }

    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     */
    public function filterByDateRange(string $searchText, $data)
    {
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
     * Summary of getList
     * @return array{appointmentList: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appointments>, patientList: \Illuminate\Database\Eloquent\Collection<int, \App\Models\Patient>, userList: mixed}
     */
    public function getList()
    {
        $patientList       = $this->patientService->getPatientList();
        $userList          = $this->userService->getUsersList('Doctor');
        $appointmentList   = $this->appointmentService->getAppointmentList();
        $frontDeskUserList = $this->userService->getUsersList('Front Desk User');
        $allUserList       = $this->userService->getAlluser();
        return ['patientList' => $patientList, 'appointmentList' => $appointmentList, 'userList' => $userList, 'frontDeskUserList' => $frontDeskUserList, 'allUserList' => $allUserList];
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create  opd to ipd record within a secure transaction')]
    public function opdToIpd(string $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|uuid|exists:patients,id',
        ]);
        $this->checkValidationOPDService->checkValidation($validator);
        $patient = Patient::where('id', $id)->first();
        $ipd = IPD::create(
            [
                'dob' => $patient->dob,//
                'age' => $patient->age,//
                'city' => $patient->city,//
                'email' => $patient->email,//
                'state' => $patient->state,//
                'patient_id' => $patient->id,//
                'gender' => $patient->gender,//
                'country' => $patient->country,//
                'pincode' => $patient->pincode,//
                'address' => $patient->address,//
                'phone_no' => $patient->phone_no,//
                'last_name' => $patient->last_name,//
                'first_name' => $patient->first_name,//
                'opd_number' => $patient->opd_number,//
                'blood_group' => $patient->blood_group,//
                'marital_status' => $patient->marital_status,//
                'patient_number' => $patient->patient_number,//
                'dietary_preference' => $patient->dietary_preference,//
                'insurance_provider' => $patient->insurance_provider,//
                'relation_to_patient' => $patient->relation_to_patient,//
                'insurance_policy_no' => $patient->insurance_policy_no,//
                'emergency_contact_name' => $patient->emergency_contact_name,//
                'ipd_number' => AutoIdGenerate::generateId(ServiceType::IPD),//
                'attendant_with_patient_name' => $patient->attendant_with_patient_name,//
                'emergency_contact_phone_number' => $patient->emergency_contact_phone_number,//
                'attendant_with_patient_phone_no' => $patient->attendant_with_patient_phone_no,//

                //admission
                'admission_date' => null,//
                'admission_time' => null,//
                'admission_type' => AddmissionTypeEnum::Elective->value,//

                // You can assign default doctor/nurse values or fetch them dynamically
                'doctor_id' => null,//
                'doctor_type' => '',//
                'doctor_name' => '',//
                'doctor_email' => '',//


                //nurse 
                'nurse_id' => null,//
                'nurse_type' => '',//
                'nurse_name' => '',//
                'nurse_email' => '',//
                'nurse_phone' => '',//

                //ward
                'name' => '', // Default department name
                'code' => '',//


                //department
                'admitting_department_id' => null,//


                //referred by
                'referred_by_name' => '',//
                'referred_by_phone_no' => '',//
                'referred_by_email' => '',//
                'referred_by_hospital_name' => '',//

                //referral source
                'referral_source' => ReferralSourceEnum::OPD->value,

                //general
                'complaint' => '',//
                'intial_diagnosis' => '',


                //admission notes
                'front_desk_user_id' => '',//
                'admission_status' => '',//
            ]
        );

        return $ipd->id;
    }
}
