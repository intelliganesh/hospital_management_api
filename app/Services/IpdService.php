<?php

namespace App\Services;

use App\Models\IPD;
use App\Models\Consultations;
use App\Models\Patient;
use App\Models\User;
use App\Models\IpdStaffs;
use App\Models\Ward;
use App\Models\Master\Rooms;
use App\Models\Bed;
use App\Traits\IpdEnrollmentValidation;
use App\Services\InvoiceService;
use App\Contracts\FilterContract;
use App\Services\CheckValidation;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use App\Enums\ServiceType;
use AutoIdGenerate;
use App\Models\Invoice;
use App\Models\Receipt;

class IpdService implements FilterContract
{
    use IpdEnrollmentValidation;

    private $columns;
    private $filters;
    private $checkValidationService;
    private $invoiceService;
    private $invoiceColumns;

    public function __construct(CheckValidation $checkValidationService,InvoiceService $invoiceService)
    {
        $this->checkValidationService = $checkValidationService;
        $this->invoiceService = $invoiceService;
        $this->invoiceColumns         = Invoice::$columns;
        $this->columns = IPD::$columns ?? ['id', 'ipd_number', 'patient_id', 'consultation_id', 'admission_date_time', 'ward_id', 'room_id', 'bed_id', 'advance_amount'];
    }

    /**
     * Create IPD enrollment for a patient
     */
    public function create(Request $request)
    {
        // Validate request
        $this->checkValidationService->checkValidation($this->validate($request));

        // Verify consultation exists and has advice_admission = 1 (only if provided)
        $consultation = null;
        if($request->has('consultation_id') && !empty($request->consultation_id)){
            $consultation = Consultations::where('id', $request->consultation_id)
                ->where('advice_admition', 1)
                ->firstOrFail();
        }

        if(isset($request->patient_id)){
            // Verify patient exists
            $patient = Patient::findOrFail($request->patient_id);
        }else{
            $patient=Patient::create([
                'gender' => $request->patient_gender,
                'phone_no' => $request->patient_attendant_phone,
                'last_name' => $request->patient_last_name,
                'first_name' => $request->patient_first_name,
                'attendant_with_patient_name' => $request->patient_attendant_name,
                'attendant_with_patient_phone_no' => $request->patient_attendant_phone,
                'patient_number' => AutoIdGenerate::generateId(ServiceType::Patient)
            ]);
        }
        $ward = $request->has('ward_id') && !empty($request->ward_id) ? Ward::findorFail($request->ward_id) : null;
        $room = $request->has('room_id') && !empty($request->room_id) ? Rooms::findorFail($request->room_id) : null;
        $bed = $request->has('bed_id') && !empty($request->bed_id) ? Bed::findorFail($request->bed_id) : null;
      
        $doctor=User::find($request->consultant_doctor_id);

        // Create IPD record
        $ipdData = [
            'patient_id' => $patient->id,
            'consultation_id' => $request->consultation_id ?? null,
            'admission_date_time' => Carbon::createFromFormat('Y-m-d H:i:s', $request->admission_date_time),
            'ward_id' => $request->ward_id ?? null,
            'room_id' => $request->room_id ?? null,
            'bed_id' => $request->bed_id ?? null,
            'advance_amount' => $request->advance_amount ?? null,
            'doctor_id'=>$doctor->id,
            'doctor_name'=>$doctor->name ?? null,
            'doctor_email'=>$doctor->email ?? null,
            'doctor_phone'=>$doctor->phone ?? null,
            'patient_number'=>$patient->patient_number,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => $patient->phone_no,
            'patient_age' => $patient->age ?? null,
            'patient_attendant_name' => $patient->attendant_with_patient_name ?? null,
            'patient_attendant_phone' => $patient->attendant_with_patient_phone_no ?? null,
            'patient_address' => $patient->address.' '.$patient->city.' '.$patient->state.' '.$patient->country.' '.$patient->pincode,
            'ward_number'=>$ward->ward_number ?? null,
            'ward_type'=>$ward->type ?? null,
            'room_type'=>$room->room_type ?? null,
            'room_number'=>$room->room_number ?? null,
            'bed_number'=>$bed->bed_number ?? null,
            'ipd_number'=>AutoIdGenerate::generateId(ServiceType::IPD)
        ];

        $ipd = IPD::create($ipdData);
        if(!is_null($consultation)) {
        $this->invoiceService->create(
                    new Request(array_merge(
                        $consultation->only($this->invoiceColumns),
                        [
                            'collected_amount' => $request->advance_amount ?? 0,
                            'balanced_amount'  => 0,
                            'ipd_id'  => $ipd->id,
                            'currency'         => $request->currency ?? '₹',
                        ]
                    ))
                );
        }else{
           $this->invoiceService->create(
                    new Request(
                        [
                            'collected_amount' => $request->advance_amount ?? 0,
                            'balanced_amount'  => 0,
                            'ipd_id'  => $ipd->id,
                            'currency'         => $request->currency ?? '₹',
                            'patient_id' => $ipd->patient_id,
                            'doctor_id' =>$ipd->doctor_id,
                            'patient_name'=>$ipd->patient_name,
                            'patient_email'=>$ipd->patient_email,
                            'patient_phone'=>$ipd->patient_phone,
                            "patient_number"=>$ipd->patient_number,
                            'doctor_name'=>$ipd->doctor_name,
                            'doctor_email'=>$ipd->doctor_email,
                            'doctor_phone'=>$ipd->doctor_phone,
                        ]
                    ));
        }
        $invoice = Invoice::where('ipd_id', $ipd->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

        $receiptData = [
            'invoice_id'     => $invoice->id,
            'currency'       => $request->currency ?? '₹',
            'amount'         => $request->advance_amount ?? 0,
            'date'           => $request->payment_date ?? \Carbon\Carbon::now(),
            'payment_type'   => $request->payment_type ?? 'Cash',
            'transaction_id' => $request->transaction_id ?? '',
            'status'         => 'Completed',
            'notes'          => $request->notes ?? 'Advance payment received during IPD enrollment',
        ];

        $receipt = Receipt::create($receiptData);

        // Assign consultant doctors
        if ($request->has('consultant_doctor') && is_array($request->consultant_doctor)) {
            foreach ($request->consultant_doctor as $doctor) {
                $doctorData=User::find($doctor);
                IpdStaffs::create([
                    'ipd_id' => $ipd->id,
                    'user_id' => $doctor,
                    'user_name'=>$doctorData->name ?? null,
                    'user_phone'=>$doctorData->phone ?? null,
                    'user_role' => 'consultant_doctor',
                    // 'shift' => $doctor['shift'],
                    'assigned_date' => now(),
                ]);
            }
        }

        // Assign duty doctors
        if ($request->has('duty_doctor') && is_array($request->duty_doctor)) {
            foreach ($request->duty_doctor as $doctor) {
                $doctorData=User::find($doctor);
                IpdStaffs::create([
                    'ipd_id' => $ipd->id,
                    'user_id' => $doctor,
                    'user_name'=>$doctorData->name ?? null,
                    'user_phone'=>$doctorData->phone ?? null,
                    'user_role' => 'duty_doctor',
                    // 'shift' => $doctor['shift'],
                    'assigned_date' => now(),
                ]);
            }
        }

        // Assign nurses
        if ($request->has('nurse') && is_array($request->nurse)) {
            foreach ($request->nurse as $nurse) {
                $nurseData=User::find($nurse);
                IpdStaffs::create([
                    'ipd_id' => $ipd->id,
                    'user_id' => $nurse,
                    'user_name'=>$nurseData->name ?? null,
                    'user_phone'=>$nurseData->phone ?? null,
                    'user_role' => 'nurse',
                    // 'shift' => $nurse['shift'],
                    'assigned_date' => now(),
                ]);
            }
        }

        return $ipd->load(['patient', 'consultation', 'staffs','preliminaryNotes']);
    }

    /**
     * Get all IPD records with pagination
     */
    public function all(?Request $request)
    {
        $query = IPD::with(['patient', 'consultation', 'staffs'])
            ->orderByRaw("CASE WHEN discharge_date_time IS NULL THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ipd_number', 'like', "%{$search}%")
                    ->orWhere('consultation_id', 'like', "%{$search}%")
                    ->orWhere('ward_number', 'like', "%{$search}%")
                    ->orWhere('ward_type', 'like', "%{$search}%")
                    ->orWhere('room_type', 'like', "%{$search}%")
                    ->orWhere('room_number', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone_no', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('state', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%");
                    })
                    ->orWhereHas('consultation', function ($subQuery) use ($search) {
                        $subQuery->where('appointment_id', 'like', "%{$search}%");
                    })
                    ->orWhereHas('staffs', function ($subQuery) use ($search) {
                        $subQuery->where('user_id', 'like', "%{$search}%")
                            ->orWhere('user_role', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('multiple_filter')) {
            $query = $this->filterMultipleFields($request->multiple_filter, $query);
        }

        if ($request->has('sort_by') && !empty($request->sort_by)) {
            $sortOrder = $request->has('sort_order') && $request->sort_order === 'asc' ? 'asc' : 'desc';
            if($request->sort_by === 'admission_date'){
                $query->orderBy('admission_date_time', $sortOrder);
            }else{
                $query->orderBy($request->sort_by, $sortOrder);
            }
        } else {
            // Default: Show Admitted → Under Treatment → Discharged → Expired
            $query->orderByRaw("CASE WHEN status = 'Admitted' THEN 0 WHEN status = 'Under Treatment' THEN 1 WHEN status = 'Discharged' THEN 2 WHEN status = 'Expired' THEN 3 ELSE 4 END")
                  ->orderBy('created_at', 'desc');
        }

        $perPage = $request->has('per_page') ? (int)$request->per_page : 10;
        $page = $request->has('page') ? (int)$request->page : 1;

        Paginator::useBootstrap();
        $ipds = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $ipds->items(),
            'pagination' => [
                'total' => $ipds->total(),
                'count' => $ipds->count(),
                'per_page' => $ipds->perPage(),
                'current_page' => $ipds->currentPage(),
                'total_pages' => $ipds->lastPage(),
                'links' => [
                    'next' => $ipds->nextPageUrl(),
                ]
            ]
        ];
    }

    /**
     * Get a single IPD record by ID
     */
    public function get(string $id)
    {
        return IPD::with(['patient', 'consultation', 'staffs'])
            ->findOrFail($id);
    }

    /**
     * Update IPD record and staff assignments
     */
    public function update(Request $request, string $id)
    {
        $ipd = IPD::findOrFail($id);

        // Validate request
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));

        // Update IPD record
        $updateData = [];
        if ($request->has('ward_id')) {
            $ward = Ward::findorFail($request->ward_id);
            $updateData['ward_id'] = $request->ward_id;
            $updateData['ward_name'] = $ward->ward_name;
        }
        if ($request->has('room_id')) {
            $room = Rooms::findorFail($request->room_id);
            $updateData['room_id'] = $request->room_id;
            $updateData['room_number'] = $room->room_number;
        }
        if ($request->has('bed_id')) {
            $bed = Bed::findorFail($request->bed_id);
            $updateData['bed_id'] = $request->bed_id;
            $updateData['bed_number'] = $bed->bed_number;
        }
        if ($request->has('advance_amount')) {
            $updateData['advance_amount'] = $request->advance_amount;
        }
        if ($request->has('admission_date_time')) {
            $updateData['admission_date_time'] = $request->admission_date_time;
        }
        if ($request->has('discharge_date_time')) {
            $updateData['discharge_date_time'] = $request->discharge_date_time;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }

        // Update patient information in IPD record if provided
        $ipdPatientUpdateData = [];
        
        if ($request->has('patient_first_name') || $request->has('patient_last_name')) {
            $patient = $ipd->patient;
            $firstName = $request->has('patient_first_name') ? $request->patient_first_name : $patient->first_name;
            $lastName = $request->has('patient_last_name') ? $request->patient_last_name : $patient->last_name;
            $ipdPatientUpdateData['patient_name'] = $firstName . ' ' . $lastName;
        }
        if ($request->has('patient_email')) {
            $ipdPatientUpdateData['patient_email'] = $request->patient_email;
        }
        if ($request->has('patient_phone')) {
            $ipdPatientUpdateData['patient_phone'] = $request->patient_phone;
        }
        if ($request->has('patient_attendant_name')) {
            $ipdPatientUpdateData['patient_attendant_name'] = $request->patient_attendant_name;
        }
        if ($request->has('patient_attendant_phone')) {
            $ipdPatientUpdateData['patient_attendant_phone'] = $request->patient_attendant_phone;
        }
        if ($request->has('patient_address')) {
            $ipdPatientUpdateData['patient_address'] = $request->patient_address;
        }
        if ($request->has('patient_age')) {
            $ipdPatientUpdateData['patient_age'] = $request->patient_age;
        }

        if (!empty($ipdPatientUpdateData)) {
            $updateData = array_merge($updateData, $ipdPatientUpdateData);
        }

        if (!empty($updateData)) {
            $ipd->update($updateData);
        }


        // Update staff assignments if provided
        $staffRoles = ['consultant_doctor', 'duty_doctor', 'nurse'];
        foreach ($staffRoles as $role) {
            if ($request->has($role) && is_array($request->$role)) {
                // Delete existing staff of this role
                IpdStaffs::where('ipd_id', $id)
                    ->where('user_role', $role)
                    ->delete();

                // Add new staff
                foreach ($request->$role as $staff) {
                    $staffData = [
                        'ipd_id' => $id,
                        'user_role' => $role,
                        'assigned_date' => now(),
                    ];

                    if (is_array($staff)) {
                        $staffData['user_id'] = $staff['user_id'] ?? $staff;
                        $staffData['user_name'] =$staff['user_name'] ?? null;
                        $staffData['user_phone'] =$staff['user_phone'] ?? null;
                        if (isset($staff['shift'])) {
                            $staffData['shift'] = $staff['shift'];
                        }
                    } else {

                        $staffdetails=User::find($staff);
                        $staffData['user_id'] = $staff;
                        $staffData['user_name'] =$staffdetails->name ?? null;
                        $staffData['user_phone'] =$staffdetails->phone ?? null;
                    }

                    IpdStaffs::create($staffData);
                }
            }
        }

        return $ipd->load(['patient', 'consultation', 'staffs']);
    }

    /**
     * Delete IPD record
     */
    public function delete(string $id)
    {
        $ipd = IPD::findOrFail($id);
        
        // Delete associated staff assignments
        IpdStaffs::where('ipd_id', $id)->delete();
        
        // Delete IPD record
        $ipd->delete();
    }

    /**
     * Search implementation for FilterContract
     */
    public function search(string $searchText, $data)
    {
        if (!empty($searchText)) {
            $data->where(function ($query) use ($searchText) {
                $query->where('ipd_number', 'like', "%{$searchText}%")
                    ->orWhere('consultation_id', 'like', "%{$searchText}%");
            });
        }
        return $data;
    }

    /**
     * Filter multiple fields implementation for FilterContract
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['ipd_number']) && $request['ipd_number'] != null && $request['ipd_number'] != '') {
            $data->where('ipd_number', $request['ipd_number']);
        }

        if (isset($request['patient_number']) && $request['patient_number'] != null && $request['patient_number'] != '') {
            $data->where('patient_number', $request['patient_number']);
        }

        if (isset($request['patient_name']) && $request['patient_name'] != null && $request['patient_name'] != '') {
            $data->where('patient_name', 'like', '%' . $request['patient_name'] . '%');
        }

        if (isset($request['patient_phone']) && $request['patient_phone'] != null && $request['patient_phone'] != '') {
            $data->where('patient_phone', $request['patient_phone']);
        }

        if (isset($request['patient_email']) && $request['patient_email'] != null && $request['patient_email'] != '') {
            $data->where('patient_email', $request['patient_email']);
        }

        if (isset($request['patient_address']) && $request['patient_address'] != null && $request['patient_address'] != '') {
            $data->where('patient_address', 'like', '%' . $request['patient_address'] . '%');
        }

        if (isset($request['ward_id']) && $request['ward_id'] != null && $request['ward_id'] != '') {
            $data->where('ward_id', $request['ward_id']);
        }

        if (isset($request['ward_number']) && $request['ward_number'] != null && $request['ward_number'] != '') {
            $data->where('ward_number', $request['ward_number']);
        }

        if (isset($request['ward_type']) && $request['ward_type'] != null && $request['ward_type'] != '') {
            $data->where('ward_type', $request['ward_type']);
        }

        if (isset($request['room_id']) && $request['room_id'] != null && $request['room_id'] != '') {
            $data->where('room_id', $request['room_id']);
        }

        if (isset($request['room_type']) && $request['room_type'] != null && $request['room_type'] != '') {
            $data->where('room_type', $request['room_type']);
        }

        if (isset($request['room_number']) && $request['room_number'] != null && $request['room_number'] != '') {
            $data->where('room_number', $request['room_number']);
        }

        if (isset($request['status']) && $request['status'] != null && $request['status'] != '') {
            $data->where('status', $request['status']);
        }

        if (isset($request['bed_id']) && $request['bed_id'] != null && $request['bed_id'] != '') {
            $data->where('bed_id', $request['bed_id']);
        }

        if (isset($request['bed_number']) && $request['bed_number'] != null && $request['bed_number'] != '') {
            $data->where('bed_number', $request['bed_number']);
        }

        if (isset($request['admission_date']) && $request['admission_date'] != null && $request['admission_date'] != '') {
            $data->whereDate('admission_date_time', $request['admission_date']);
        }

        if (isset($request['consultant_doctor']) && $request['consultant_doctor'] != null && $request['consultant_doctor'] != '') {
            $data->whereHas('staffs', function ($subQuery) use ($request) {
                $subQuery->where('user_role', 'consultant_doctor')
                    ->where('user_id', $request['consultant_doctor']);
            });
        }

        if (isset($request['duty_doctor']) && $request['duty_doctor'] != null && $request['duty_doctor'] != '') {
            $data->whereHas('staffs', function ($subQuery) use ($request) {
                $subQuery->where('user_role', 'duty_doctor')
                    ->where('user_id', $request['duty_doctor']);
            });
        }

        if (isset($request['nurse']) && $request['nurse'] != null && $request['nurse'] != '') {
            $data->whereHas('staffs', function ($subQuery) use ($request) {
                $subQuery->where('user_role', 'nurse')
                    ->where('user_id', $request['nurse']);
            });
        }

        return $data;
    }

    /**
     * Filter by date range implementation for FilterContract
     */
    public function filterByDateRange(string $searchText, $data)
    {
        $dates = explode("|", $searchText);
        if (count($dates) === 2) {
            $data->whereBetween('admission_date_time', [$dates[0], $dates[1]]);
        }
        return $data;
    }

    /**
     * Sort data implementation for FilterContract
     */
    public function sortData(string $searchText, $data)
    {
        // Implementation for sorting
        return $data;
    }
}
