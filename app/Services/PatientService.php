<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Enums\ServiceType;
use App\Models\Appointments;
use App\Models\Consultations;
use App\Models\Examination;
use App\Models\Patient;
use App\Models\PatientAddressProof;
use App\Models\PatientAttendantAddressProof;
use App\Models\PatientFistula;
use App\Models\SystemSettings;
use App\Models\Vital;
use App\Traits\FieldValuesTrait;
use App\Traits\PatientsValidationTrait;
use AutoIdGenerate;
use Barryvdh\DomPDF\Facade\Pdf;
use FormateDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// use Throwable;
// use Illuminate\Http\Response;
// use App\Enums\Consultation\TypeEnum;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class PatientService implements CRUDContract, FilterContract
{
    use FieldValuesTrait;
    use PatientsValidationTrait;

    private $filter;
    private $columns;
    private $checkValidationService;
    private $patientAddressProofService;
    private $patientAttendantAddressProofService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\PatientAddressProofService $patientAddressProofService
     */
    public function __construct(CheckValidation $checkValidationService, PatientAddressProofService $patientAddressProofService, PatientAttendantAddressProofService $patientAttendantAddressProofService)
    {
        $this->filter                              = Patient::$filter;
        $this->columns                             = Patient::$columns;
        $this->checkValidationService              = $checkValidationService;
        $this->patientAddressProofService          = $patientAddressProofService;
        $this->patientAttendantAddressProofService = $patientAttendantAddressProofService;
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

        // $data->where(function ($query) use ($searchText) {
        //     $query->where('patient_number', 'like', '%' . $searchText . '%')
        //         ->orWhere('first_name', 'like', '%' . $searchText . '%')
        //         ->orWhere('last_name', 'like', '%' . $searchText . '%')
        //         ->orWhere('dietary_preference', 'like', '%' . $searchText . '%')
        //         ->orWhere('phone_no', 'like', '%' . $searchText . '%')->orWhere('status', 'like', '%' . $searchText . '%');
        // });
        // return $data;
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {

        foreach ($this->filter as $value) {
            if (isset($request[$value]) && $request[$value] != null && $request[$value] != '') {
                $data->where($value, $request[$value]);
            }
        }

        return $data;

        // if (isset($request['patient_number']) && $request['patient_number'] != null && $request['patient_number'] != '') {
        //     $data->where('patient_number', $request['patient_number']);
        // }

        // if (isset($request['first_name']) && $request['first_name'] != null && $request['first_name'] != '') {
        //     $data->where('first_name', $request['first_name']);
        // }

        // if (isset($request['last_name']) && $request['last_name'] != null && $request['last_name'] != '') {
        //     $data->where('last_name', $request['last_name']);
        // }

        // if (isset($request['phone_no']) && $request['phone_no'] != null && $request['phone_no'] != '') {
        //     $data->where('phone_no', $request['phone_no']);
        // }

        // if (isset($request['dietary_preference']) && $request['dietary_preference'] != null && $request['dietary_preference'] != '') {
        //     $data->where('dietary_preference', $request['dietary_preference']);
        // }

        // return $data;

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
     * @deprecated message
     */
    public function create(Request $request): void
    {

    }
    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create patient record within a secure transaction')]
    public function createPatient(Request $request): mixed
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $validateData = [
            'patient_number' => AutoIdGenerate::generateId(ServiceType::Patient),
            'dob'            => ! empty($request->dob) ? FormateDate::getFormateDate($request->dob) : null,
        ];
        // Handle timestamps manually if provided
        $data = $request->except("dob");
        if (! isset($data['created_at'])) {
            $data['created_at'] = now();
        }
        $data['updated_at'] = $data['created_at'];

        $patient = Patient::create(array_merge($data, $validateData));

        if ($request->id_edited && ! empty($request->id_value) && isset($request->id_value)) {
            $addressProof = [
                'patient_id'       => $patient->id,
                'id_type'          => $request->id_type,
                'consent'          => $request->consent,
                'id_number'        => $request->id_value,
                'id_proof_for_pan' => $request->id_proof_for_pan,
            ];
            $this->patientAddressProofService->createAndGet(new Request($addressProof));
        }

        if ($request->attendant_id_edited && ! empty($request->attendant_id_value) && isset($request->attendant_id_value)) {
            $addressProof = [
                'patient_id'       => $patient->id,
                'id_type'          => $request->attendant_id_type,
                'consent'          => $request->attendant_consent,
                'id_number'        => $request->attendant_id_value,
                'id_proof_for_pan' => $request->attendant_id_proof_for_pan,
            ];

            $this->patientAttendantAddressProofService->createAndGet(new Request($addressProof));
        }

        $patientAddressProof          = PatientAddressProof::where('patient_id', $patient->id)->first();
        $patientAttendantAddressProof = PatientAttendantAddressProof::where('patient_id', $patient->id)->first();

        return ['patient_id' => $patient->id, 'id' => $patientAddressProof ? $patientAddressProof->id : null, 'attendant_id' => $patientAttendantAddressProof ? $patientAttendantAddressProof->id : null];
    }

    /**
     * Summary of update
     * @deprecated message
     */
    public function update(Request $request, string | null $id): void
    {
        // $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        // $patient = Patient::findOrFail($id);
        // $validateData = [
        //     "referred_to" => $request->doctor_id,
        //     'dob' => $request->dob ? FormateDate::getFormateDate($request->dob) : null,
        // ];
        // $patient->update(array_merge($request->except("dob", "doctor_id"), $validateData));
    }

    /**
     * Summary of updatePatient
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return array{id: mixed, patient_id: mixed|array{patient_id: mixed}}
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update patient record within a secure transaction')]
    public function updatePatient(Request $request, string | null $id): mixed
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $patient      = Patient::findOrFail($id);
        $validateData = [
            'dob' => ! empty($request->dob) ? FormateDate::getFormateDate($request->dob) : null,
        ];

        // Handle timestamps manually if provided
        $data               = $request->except("dob", "doctor_id");
        $data['updated_at'] = now(); // On update, set updated_at to current time

        $patient->update(array_merge($data, $validateData));
        // $patient = Patient::create(array_merge($request->except("dob"), $validateData));

        if ($request->id_edited && ! empty($request->id_value) && isset($request->id_value)) {
            $addressProof = [
                'patient_id'       => $id,
                'id_type'          => $request->id_type,
                'consent'          => $request->consent,
                'id_number'        => $request->id_value,
                'id_proof_for_pan' => $request->id_proof_for_pan,
            ];
            $this->patientAddressProofService->createAndGet(new Request($addressProof));
        }

        if ($request->attendant_id_edited && ! empty($request->attendant_id_value) && isset($request->attendant_id_value)) {
            $addressProof = [
                'patient_id'       => $id,
                'id_type'          => $request->attendant_id_type,
                'consent'          => $request->attendant_consent,
                'id_number'        => $request->attendant_id_value,
                'id_proof_for_pan' => $request->attendant_id_proof_for_pan,
            ];

            $this->patientAttendantAddressProofService->createAndGet(new Request($addressProof));
        }

        $patientAddressProof          = PatientAddressProof::where('patient_id', $id)->first();
        $patientAttendantAddressProof = PatientAttendantAddressProof::where('patient_id', $id)->first();

        if (isset($request->update_patient_info) && $request->update_patient_info == true) {
            $data = ['patient_email' => $patient->email,
                'patient_phone'          => $patient->phone_no,
                'patient_number'         => $patient->patient_number,
                'patient_name'           => $patient->first_name . ' ' . $patient->last_name];
            \App\Models\Appointments::where('patient_id', $id)->update($data);
            \App\Models\Consultations::where('patient_id', $id)->update($data);
            \App\Models\Invoice::where('patient_id', $id)->update($data);
            \App\Models\IPD::where('patient_id', $id)->where('status', 'Admitted')->update($data);
        }

        return ['patient_id' => $id, 'id' => $patientAddressProof ? $patientAddressProof->id : null, 'attendant_id' => $patientAttendantAddressProof ? $patientAttendantAddressProof->id : null];
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        //code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Patient
     */
    public function get(string $id): Patient
    {
        $patient = Patient::with([
            'appointments',
            'consultation' => function ($q) {
                $q->where('removed', 'Active');
            },
            'consultation.proctology',
            'consultation.nonProctology',
            'consultation.allopathy',
        ])->findOrFail($id);
        $govIdProof                            = $this->patientAddressProofService->getDataByDynamicColumnsName('patient_id', $id);
        $patient['id_type']                    = $govIdProof->id_type ?? "";
        $patient['id_number_masked']           = $govIdProof->id_number_masked ?? "";
        $patient['consent']                    = $govIdProof->consent ?? "";
        $patient['image']                      = $govIdProof->image ?? "";
        $attendantGovIdProof                   = $this->patientAttendantAddressProofService->getDataByDynamicColumnsName('patient_id', $id);
        $patient['attendant_id_type']          = $attendantGovIdProof->id_type ?? "";
        $patient['attendant_id_number_masked'] = $attendantGovIdProof->id_number_masked ?? "";
        $patient['attendant_consent']          = $attendantGovIdProof->consent ?? "";
        $patient['attendant_image']            = $attendantGovIdProof->image ?? "";
        $patient['patient_document']           = $patient->getDocuments();
        $patient['fistula_info']               = PatientFistula::where('patient_id', $patient->id)->orderBy('updated_at', 'desc')->first();

        // $patient['id_proof_for_pan'] = $govIdProof->id_proof_for_pan ?? "";
        return $patient;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $patient = Patient::query();
        if ($request->has('search')) {
            $searchValue = $request->search;
            $patient     = $this->search($searchValue, $patient);
        }
        if ($request->has('sort_by')) {
            $sortBy    = $request->sort_by ?? 'first_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $patient   = $patient->orderBy($sortBy, $sortOrder);
        }
        if ($request->has('multiple_filter')) {
            $patient = $this->filterMultipleFields($request->multiple_filter, $patient);
        }
        return ['patient' => $patient->select($this->columns)->paginate(env('PAGINATION', 25)), 'active_patient' => Patient::where('status', 'Active')->count()];
    }

    /**
     * Summary of download
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return string
     */
    public function download(string $id)
    {
        $user    = Auth::user();
        $patient = Patient::findOrFail($id);
        // if (!$patient) {
        //     throw new NotFoundHttpException('Patient data not found.');
        // }
        $examination  = Examination::where('patient_id', $id)->first();
        $appointment  = Appointments::where('patient_id', $id)->first();
        $consultation = Consultations::where('patient_id', $id)->first();
        $vital        = null;
        if (! empty($consultation)) {
            $vital = Vital::where('consultation_id', $consultation->id)->first();
        }
        $patient['referred_to'] = $appointment?->doctor_name;
        $patient['examination'] = $examination?->examination_overview;
        $patient['bp']          = $vital?->bp;
        $patient['rs']          = $vital?->rs;
        $patient['cvs']         = $vital?->cvs;
        $patient['pulse']       = $vital?->pulse;
        $patient['temperature'] = $vital?->temperature;
        $system                 = SystemSettings::where('id', $user->system_settings_id)->first();
        if (! empty($system)) {
            $patient['letter_header_address'] = $system->billing_letter_header; //letter_header_address
        }

        // $views = [
        //     'templates.downloads.ipd_form_part1',
        //     'templates.downloads.ipd_form_part2',
        //     'templates.downloads.black_page',
        //     'templates.downloads.consent_form',
        //     'templates.downloads.consent_form2',
        //     'templates.downloads.nurses_notes',
        //     'templates.downloads.pre_operative_check_list',
        // ];

        // $pdf = Pdf::loadView($views, $patient);
        $pdf = Pdf::loadView('templates.downloads.master_combined', ['patient' => $patient]);
        // return $pdf->output();

        //   $pdf = Pdf::loadView('templates.downloads.invoice-bill', $this->invoiceData($id));
        // return $pdf->output();
        $fileName = 'patient_' . $id . '_' . time() . '.pdf';
        $filePath = 'pdfs/' . $fileName;

        if (! Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        Storage::disk('public')->put($filePath, $pdf->output());

        $downloadUrl = asset('storage/' . $filePath);
        return $downloadUrl;

        // return response($this->pdfGenerate($views, $patient), 200)
        //     ->header('Content-Type', 'application/pdf')
        //     ->header('Content-Disposition', 'attachment; filename="combined_patient_forms.pdf"');
    }

    /**
     * Summary of anaesthesiaForm
     * @param string $id
     * @return string
     */
    public function anaesthesiaForm(string $id)
    {
        $user    = Auth::user();
        $patient = Patient::findOrFail($id);
        // $views = [
        //     'templates.downloads.anaestesia1',
        // ];

        $system = SystemSettings::where('id', $user->system_settings_id)->first();
        if (! empty($system)) {
            $patient['letter_header_address'] = $system->billing_letter_header;
        }

        $pdf = Pdf::loadView("templates.downloads.anaestesia1", ['patient' => $patient]);
        // return $pdf->output();

        //   $pdf = Pdf::loadView('templates.downloads.invoice-bill', $this->invoiceData($id));
        // return $pdf->output();
        $fileName = 'patient_anaesthesia_' . $id . '_' . time() . '.pdf';
        $filePath = 'pdfs/' . $fileName;

        if (! Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        Storage::disk('public')->put($filePath, $pdf->output());

        $downloadUrl = asset('storage/' . $filePath);
        return $downloadUrl;
        // return response($this->pdfGenerate($views, $patient, 'a4'), 200)
        //     ->header('Content-Type', 'application/pdf')
        //     ->header('Content-Disposition', 'attachment; filename="combined_patient_forms.pdf"');
    }

    private function pdfGenerate($views, $patient, $pageSize = "a4", $orientation = "portrait")
    {
        $html = '';
        // $totalViews = count($views);
        foreach ($views as $index => $view) {
            if (! view()->exists($view)) {
                throw new \InvalidArgumentException("View [{$view}] not found.");
                // Development OPD note
            }

            $pdf = Pdf::loadHTML($html)->setPaper($pageSize, $orientation)->setOptions([
                'isPhpEnabled'         => true,
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
            ]);
            // return $pdf->download('combined_patient_forms.pdf');
            return $pdf->output();
        }
    }

    /**
     * Summary of getPatientList
     * @return \Illuminate\Database\Eloquent\Collection<int, Patient>
     */
    public function getPatientList()
    {
        return Patient::select('id', 'patient_number', 'first_name', 'last_name','phone_no')->get();
    }

    /**
     * Get patient statistics including total count and active patients count
     *
     * @return array
     */
    public function getStatistics()
    {
        $totalPatients  = Patient::count();
        $activePatients = Patient::where('status', 'Active')->count();

        return [
            'total_patients'  => $totalPatients,
            'active_patients' => $activePatients,
        ];
    }

}
