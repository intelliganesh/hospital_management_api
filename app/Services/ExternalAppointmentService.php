<?php
namespace App\Services;

use App\Models\Appointments;
use App\Models\Consultations;
use App\Models\ExternalAppointment;
use App\Models\MeetingLog;
use App\Models\Patient;
use App\Models\User;
use App\Services\AppointmentsService;
use App\Services\CheckValidation;
use App\Services\DailyService;
use App\Services\InvoiceService;
use App\Services\PatientService;
use App\Services\PaymentService;
use App\Services\Users\UserService;
use App\Services\WhatsappService;
use App\Traits\ExternalAppointmentValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\DB;


class ExternalAppointmentService
{
    use ExternalAppointmentValidationTrait;

    private $checkValidationService;
    private $patientService;
    private $appointmentsService;
    private $userService;
    private $invoiceService;
    private $invoiceColumns;
    private $paymentService;
    private $dailyService;
    private $whatsapp;
    private $emailService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\PatientService $patientService
     * @param \App\Services\AppointmentsService $appointmentsService
     * @param \App\Services\Users\UserService $userService
     * @param \App\Services\InvoiceService $invoiceService
     * @param \App\Services\PaymentService $paymentService
     * @param \App\Services\DailyService $dailyService
     * @param \App\Services\WhatsappService $whatsapp
     * @param \App\Services\EmailService $emailService
     */
    public function __construct(
        CheckValidation $checkValidationService,
        PatientService $patientService,
        AppointmentsService $appointmentsService,
        UserService $userService,
        InvoiceService $invoiceService,
        PaymentService $paymentService,
        DailyService $dailyService,
        WhatsappService $whatsapp,
        EmailService $emailService
    ) {
        $this->checkValidationService = $checkValidationService;
        $this->patientService         = $patientService;
        $this->appointmentsService    = $appointmentsService;
        $this->userService            = $userService;
        $this->invoiceService         = $invoiceService;
        $this->paymentService         = $paymentService;
        $this->invoiceColumns         = Invoice::$columns;
        $this->dailyService           = $dailyService;
        $this->whatsapp               = $whatsapp;
        $this->emailService           = $emailService;
    }

    /**
     * Create a new external appointment
     */
    public function create(Request $request): ExternalAppointment
    {
        // Validate the request
        $this->checkValidationService->checkValidation($this->validate($request));

        $data = $request->only([
            'name',
            'age',
            'phone',
            'gender',
            'email',
            'citizenship',
            'place_of_living',
            'doctor_id',
            'appointment_datetime',
            'alternate_date',
            'appointment_type',
            'symptoms',
            'amount',
            'meeting_link',
            'payment_type',
            'payment_info',
            'visit_type',
            'transaction_id',
            'payment_date',
            'payment_screenshot',
            'daily_meeting_info',
        ]);

        // Set default status if not provided
        $data['status'] = $request->get('status', 'Pending');

        // Generate appointment_reference_number (EXT + incremental)
        $data['appointment_reference_number'] = $this->generateAppointmentReferenceNumber();

        return ExternalAppointment::create($data);
    }

    /**
     * Generate incremental appointment reference number with EXT prefix
     */
    private function generateAppointmentReferenceNumber(): string
    {
        $last = ExternalAppointment::orderByDesc('created_at')->first();
        if ($last && preg_match('/EXT(\d{3,})/', $last->appointment_reference_number, $matches)) {
            $num = intval($matches[1]) + 1;
        } else {
            $num = 1;
        }
        return 'EXT' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get all external appointments with pagination
     */
    public function list(Request $request): LengthAwarePaginator
    {
        $query = ExternalAppointment::with('doctor');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }

        // Filter by doctor
        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->byDoctor($request->doctor_id);
        }

        // Filter by appointment type
        if ($request->has('appointment_type') && $request->appointment_type) {
            $query->where('appointment_type', $request->appointment_type);
        }

        // Search by name, phone, or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has("from_date") && $request->has("to_date")) {
            $query = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $query);
        } else {
            // Date filter: only appointments from today onwards
            // $today = now()->toDateString();
            // $query->whereDate('appointment_datetime', '>=', $today);
        }

        // Sort by appointment_datetime (date part) ascending by default
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(env('PAGINATION', 25));
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
        $data->whereDate('appointment_datetime', '>=', $dates[0])
     ->whereDate('appointment_datetime', '<=', $dates[1]);
        return $data;
    }

    /**
     * Get external appointment by ID
     */
    public function getById(string $id): ExternalAppointment
    {
        return ExternalAppointment::with('doctor', 'consultation')->findOrFail($id);
    }

    /**
     * Update external appointment
     */
    public function update(Request $request, string $id): ExternalAppointment
    {
        $appointment = ExternalAppointment::findOrFail($id);

        // Validate the request for update
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));

        // Store previous status before updating
        $previousStatus = $appointment->status;
        $previousAppointmentDatetime = $appointment->appointment_datetime;

        

        $appointment->update($request->only([
            'name',
            'age',
            'citizenship',
            'place_of_living',
            'phone',
            'gender',
            'email',
            'doctor_id',
            'appointment_datetime',
            'alternate_date',
            'appointment_type',
            'symptoms',
            'status',
            'amount',
            'meeting_link',
            'meeting_link_type',
            'payment_type',
            'payment_info',
            'visit_type',
            'transaction_id',
            'payment_date',
            'payment_screenshot',
            'daily_meeting_info',
            'currency',
        ]));

        $status = $request->get('status', $appointment->status);

        if ($status) {
            // Create consultation and appointment when payment is made
            if ($previousStatus !== 'Paid' && $status === 'Paid') {
                try {
                    $this->createConsultation($appointment);
                    if (strtoupper($appointment->appointment_type) === 'ONLINE') {
                        $this->createMeetingLink($appointment->id);
                    }
                    Log::info('Consultation created successfully from update', ['appointment_id' => $appointment->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to create consultation in update', [
                        'appointment_id' => $appointment->id,
                        'error'          => $e->getMessage(),
                    ]);
                    throw new \Exception('Failed to create consultation: ' . $e->getMessage());
                }
            }
        }

        if($previousAppointmentDatetime!=$appointment->appointment_datetime){
            $this->updateConsultation($appointment);
            if (strtoupper($appointment->appointment_type) === 'ONLINE') {
                $this->createMeetingLink($appointment->id);
            }
            Log::info('Consultation updated successfully from update', ['appointment_id' => $appointment->id]);
        }

        

        $appointment->save();
        return $appointment;
    }

    /**
     * Delete external appointment
     */
    public function delete(string $id): bool
    {
        return ExternalAppointment::destroy($id) > 0;
    }

    /**
     * Get appointments by doctor
     */
    public function getByDoctor(string $doctorId, Request $request): LengthAwarePaginator
    {
        $query = ExternalAppointment::where('doctor_id', $doctorId);

        if ($request->has('status') && $request->status) {
            $query->byStatus($request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sortBy    = $request->get('sort_by', 'appointment_datetime');
        $sortOrder = $request->get('sort_order', 'asc');
        return $query->orderBy($sortBy, $sortOrder)->paginate(env('PAGINATION', 15));
    }

    /**
     * Change appointment status and generate meeting/payment links when confirmed
     * Call createConsultation when status changes to 'Paid'
     */
    public function changeStatus(string $id, string $status): ExternalAppointment
    {
        $appointment = ExternalAppointment::findOrFail($id);

        // Store previous status before updating
        $previousStatus = $appointment->status;

        // $appointment = $this->generateLinksIfNeeded($appointment, $previousStatus, $status);
        $appointment->status = $status;
        $appointment->save();

        Log::info('Appointment status changed', [
            'appointment_id'  => $appointment->id,
            'previous_status' => $previousStatus,
            'new_status'      => $status,
        ]);

        // Create consultation and appointment when payment is made
        if ($previousStatus !== 'Paid' && $status === 'Paid') {
            try {
                $this->createConsultation($appointment);
                Log::info('Consultation created successfully from changeStatus', ['appointment_id' => $appointment->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create consultation in changeStatus', [
                    'appointment_id' => $appointment->id,
                    'error'          => $e->getMessage(),
                ]);
                throw new \Exception('Failed to create consultation: ' . $e->getMessage());
            }
        }

        return $appointment;
    }

    /**
     * Get upcoming appointments
     */
    public function getUpcoming(Request $request): LengthAwarePaginator
    {
        $query = ExternalAppointment::where('appointment_datetime', '>', now())
            ->where('status', '!=', 'Cancelled');

        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->byDoctor($request->doctor_id);
        }

        $sortBy    = $request->get('sort_by', 'appointment_datetime');
        $sortOrder = $request->get('sort_order', 'asc');
        return $query->orderBy($sortBy, $sortOrder)->paginate(env('PAGINATION', 15));
    }

    /**
     * Get appointment statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_appointments'           => ExternalAppointment::count(),
            'pending_appointments'         => ExternalAppointment::byStatus('Pending')->count(),
            'confirmed_appointments'       => ExternalAppointment::byStatus('Confirmed')->count(),
            'payment_pending_appointments' => ExternalAppointment::byStatus('Payment Pending')->count(),
            'paid_appointments'            => ExternalAppointment::byStatus('Paid')->count(),
            'completed_appointments'       => ExternalAppointment::byStatus('Completed')->count(),
            'cancelled_appointments'       => ExternalAppointment::byStatus('Cancelled')->count(),
            'upcoming_appointments'        => ExternalAppointment::where('appointment_datetime', '>', now())->count(),
        ];
    }

    /**
     * Get list of all active doctors with pagination
     */
    public function getDoctorList(Request $request): LengthAwarePaginator
    {
        $sortBy    = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');

        // Get active doctors with role 'Doctor'
        $doctors = User::role('Doctor')
            ->where('status', 'active')
            ->select('id', 'name', 'email', 'phone', 'designation', 'qualification', 'department_name', 'image', 'available_days',
                'slot_duration',
                'leave_date')
            ->orderBy($sortBy, $sortOrder)
            ->paginate(env('PAGINATION', 15));

        foreach ($doctors as $doctor) {
            $external_appointment          = ExternalAppointment::where('doctor_id', $doctor->id)->where('appointment_datetime', '>', now())->pluck('appointment_datetime');
            $doctor->upcoming_appointments = $external_appointment;

            if (is_null($doctor->available_days)) {
                continue;
            }

            $available_days = is_array($doctor->available_days)
                ? $doctor->available_days
                : json_decode($doctor->available_days, true);

            $result = [];

            foreach ($available_days as $day => $sessions) {

                foreach ($sessions as $period => $timeRange) {

                    if (! is_array($timeRange) || count($timeRange) < 2) {
                        continue;
                    }

                    $start = \Carbon\Carbon::createFromFormat('H:i', $timeRange[0]);
                    $end   = \Carbon\Carbon::createFromFormat('H:i', $timeRange[1]);

                    $slots = [];

                    while (
                        $start->copy()->addMinutes((int) $doctor->slot_duration) <= $end
                    ) {

                        $slots[] = $start->format('H:i');

                        $start->addMinutes((int) $doctor->slot_duration);
                    }

                    $result[$day][$period] = $slots;
                }
            }

            $doctor->available_days = $result;

        }

        return $doctors;
    }

    /**
     * Convert enum value to enum backed value for validation
     * Converts any format to the enum backed value expected by validation
     * E.g., "FirstVisit" or "First Visit" -> "First Visit"
     */
    private function convertVisitTypeToBackedValue(?string $visitTypeValue): ?string
    {
        if (empty($visitTypeValue)) {
            return null;
        }

        // Map all possible inputs to backed values
        $mappings = [
            // From case names
            'FollowUp'               => 'Follow-up',
            'FirstVisit'             => 'First Visit',
            'PostSurgeryFollowUp'    => 'Post Surgery Follow up',
            // From backed values (passthrough)
            'Follow-up'              => 'Follow-up',
            'First Visit'            => 'First Visit',
            'Post Surgery Follow up' => 'Post Surgery Follow up',
        ];

        if (isset($mappings[$visitTypeValue])) {
            Log::info('Visit type converted', [
                'input'  => $visitTypeValue,
                'output' => $mappings[$visitTypeValue],
            ]);
            return $mappings[$visitTypeValue];
        }

        Log::warning('Visit type not found in mapping, using as-is', ['visit_type' => $visitTypeValue]);
        return $visitTypeValue;
    }

    /**
     * Create patient and appointment when external appointment is paid
     * Validates phone/email are not null, fetches or creates patient, and creates appointment
     */
    private function createConsultation(ExternalAppointment $externalAppointment): void
    {
        try {
            Log::info('Starting createConsultation for ExternalAppointment', [
                'external_appointment_id' => $externalAppointment->id,
                'name'                    => $externalAppointment->name,
                'email'                   => $externalAppointment->email,
                'phone'                   => $externalAppointment->phone,
            ]);

            // Validate phone and email are not null
            if (empty($externalAppointment->phone) || empty($externalAppointment->email)) {
                Log::warning('Phone or email is empty', [
                    'external_appointment_id' => $externalAppointment->id,
                    'phone'                   => $externalAppointment->phone,
                    'email'                   => $externalAppointment->email,
                ]);
                throw new \Exception('Phone and email are required to create consultation');
            }

            // Fetch or create patient using phone and email
            $patient = Patient::where('phone_no', $externalAppointment->phone)
                ->orWhere('email', $externalAppointment->email)
                ->first();

            Log::info('Patient lookup result', [
                'patient_found' => ! is_null($patient),
                'patient_id'    => $patient?->id,
            ]);

            // If patient doesn't exist, create a new one
            if (! $patient) {
                Log::info('Creating new patient');
                $patientData = new Request([
                    'first_name'      => $externalAppointment->name,
                    'last_name'       => '',
                    'phone_no'        => $externalAppointment->phone,
                    'email'           => $externalAppointment->email,
                    'gender'          => $externalAppointment->gender ?? 'Other',
                    'dob'             => null,
                    'age'             => $externalAppointment->age ?? null,
                    'country'         => $externalAppointment->citizenship,
                    'place_of_origin' => $externalAppointment->place_of_origin,
                ]);

                $this->patientService->createPatient($patientData);
                $patient = Patient::where('phone_no', $externalAppointment->phone)
                    ->orWhere('email', $externalAppointment->email)
                    ->first();

                Log::info('New patient created', ['patient_id' => $patient?->id]);
            } else {
                $patient->first_name = $externalAppointment->name;
                $patient->age        = $externalAppointment->age ?? null;
                $patient->save();
            }

            // Prepare data for creating appointment in appointments table
            $appointmentData = new Request([
                'patient_id'              => $patient->id,
                'doctor_id'               => $externalAppointment->doctor_id,
                'front_desk_user_id'      => auth()->check() ? auth()->id() : 1,
                'appointment_time'        => date('H:i:s', strtotime($externalAppointment->appointment_datetime)),
                'appointment_date'        => date('Y-m-d', strtotime($externalAppointment->appointment_datetime)),
                'complaint'               => $externalAppointment->symptoms ?? 'External Appointment',
                'type'                    => $this->convertVisitTypeToBackedValue($externalAppointment->visit_type),
                'appointment_fees'        => $externalAppointment->amount ?? 0,
                'consultation_amount'     => $externalAppointment->amount ?? 0,
                'payment_status'          => 'Completed',
                'status'                  => 'Pending',
                'currency'                => $externalAppointment->currency ?? '₹',
                'payment_type'            => $externalAppointment->payment_type,
                'transaction_id'          => $externalAppointment->transaction_id ?? '',
                'notes'                   => $externalAppointment->payment_info ?? '',
                'payment_date'            => $externalAppointment->payment_date ?? \Carbon\Carbon::now(),
                'external_appointment_id' => $externalAppointment->id,

            ]);

            Log::info('Creating appointment with data', $appointmentData->all());

            // Create appointment in appointments table
            $this->appointmentsService->create($appointmentData);

            // Fetch the created appointment since create() is void
            $consultations = Consultations::where('patient_id', $patient->id)
                ->where('external_appointment_id', $externalAppointment->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $consultations) {
                Log::error('Failed to retrieve created appointment', [
                    'external_appointment_id' => $externalAppointment->id,
                    'patient_id'              => $patient->id,
                ]);
                throw new \Exception('Failed to create appointment: Could not retrieve created appointment');
            }

            Log::info('Appointment created successfully', [
                'consultation_id' => $consultations->id,
            ]);

            try {
                // Create invoice directly using the model instead of service to bypass AutoIdGenerate issues
                $this->invoiceService->create(
                    new Request(array_merge(
                        $consultations->only($this->invoiceColumns),
                        [
                            'collected_amount' => $externalAppointment->amount ?? 0,
                            'balanced_amount'  => 0,
                            'consultation_id'  => $consultations->id,
                            'currency'         => $externalAppointment->currency ?? '₹',
                        ]
                    ))
                );
                // Fetch the created invoice since create() is void
                $invoice = Invoice::where('consultation_id', $consultations->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (! $invoice) {
                    Log::error('Invoice creation returned null');
                    throw new \Exception('Failed to create invoice');
                }

                Log::info('Invoice created successfully', [
                    'invoice_id'      => $invoice->id,
                    'consultation_id' => $consultations->id,
                ]);
            } catch (\Exception $invoiceError) {
                Log::error('Error creating invoice', [
                    'error'           => $invoiceError->getMessage(),
                    'consultation_id' => $consultations->id,
                ]);
                throw new \Exception('Failed to create invoice: ' . $invoiceError->getMessage());
            }

            // Create receipt
            Log::info('Creating receipt');
            $receiptData = [
                'invoice_id'     => $invoice->id,
                'currency'       => $externalAppointment->currency ?? '₹',
                'amount'         => $appointmentData->get('appointment_fees') ?? $externalAppointment->amount ?? 0,
                'date'           => $appointmentData->get('payment_date') ?? \Carbon\Carbon::now(),
                'payment_type'   => $appointmentData->get('payment_type'),
                'transaction_id' => $appointmentData->get('transaction_id') ?? '',
                'status'         => 'Completed',
                'notes'          => $appointmentData->get('notes') ?? '',
            ];

            $receipt = Receipt::create($receiptData);

            if (! $receipt) {
                Log::error('Receipt creation returned null');
                throw new \Exception('Failed to create receipt');
            }

            Log::info('Receipt created successfully', [
                'receipt_id'              => $receipt->id,
                'external_appointment_id' => $externalAppointment->id,
            ]);

            $this->paymentService->updateByColumns([
                'consultation_id'       => $consultations->id,
                'doctor_id'             => $consultations->doctor_id,
                'patient_id'            => $consultations->patient_id,
                'doctor_name'           => $consultations->doctor_name,
                'doctor_email'          => $consultations->doctor_email,
                'patient_email'         => $consultations->patient_email,
                'doctor_phone'          => $consultations->doctor_phone,
                'amount_for'            => 'Consultation Cost',
                'appointment_id'        => $consultations->appointment_id,
                'patient_phone'         => $consultations->patient_phone,
                'amount'                => $consultations->consultation_amount,
                'front_desk_user_id'    => $consultations->front_desk_user_id,
                'patient_number'        => $consultations->patient_number,
                'front_desk_user_name'  => $consultations->front_desk_user_name,
                'front_desk_user_email' => $consultations->front_desk_user_email,
                'front_desk_user_phone' => $consultations->front_desk_user_phone,
                'patient_name'          => $consultations->patient_name,
                'currency'              => $externalAppointment->currency ?? '₹',
            ], ['consultation_id' => $consultations->id, 'amount_for' => 'Consultation Cost']);

        } catch (\Exception $e) {
            Log::error('Error in createConsultation', [
                'external_appointment_id' => $externalAppointment->id,
                'error'                   => $e->getMessage(),
                'trace'                   => $e->getTraceAsString(),
            ]);
            throw $e; // Re-throw to be caught by caller
        }
    }
    
    /**
     * Update patient and appointment when external appointment is updated
     * Validates phone/email are not null, fetches or creates patient, and updates appointment
     */
    private function updateConsultation(ExternalAppointment $externalAppointment): void{
        $consultation = Consultations::where('external_appointment_id', $externalAppointment->id)->first();
        // $consultation->appointment_date = date('Y-m-d', strtotime($externalAppointment->appointment_datetime));
        // $consultation->appointment_time = date('H:i:s', strtotime($externalAppointment->appointment_datetime));
        // $consultation->save();
        $appointment = Appointments::where('id', $consultation->appointment_id)->first();
        $appointment->appointment_date = date('Y-m-d', strtotime($externalAppointment->appointment_datetime));
        $appointment->appointment_time = date('H:i:s', strtotime($externalAppointment->appointment_datetime));
        $appointment->save();   
    }

    /**
     * Get a single doctor's detailed information
     */
    public function getDoctorDetail(string $doctorId): User
    {
        $doctor = User::role('Doctor')
            ->where('status', 'active')
            ->findOrFail($doctorId);

        return $doctor;
    }

    private function generateLinksIfNeeded(ExternalAppointment $appointment, string $previousStatus, string $newStatus): ExternalAppointment
    {
        // Generate meeting and payment links only when status changes to "Confirmed"
        if ($previousStatus !== 'Confirmed' && $newStatus === 'Confirmed') {
            // Generate meeting link for online consultations
            if ($appointment->appointment_type === 'Online' && ! $appointment->meeting_link) {
                $appointment->meeting_link = 'https://meet.jit.si/consultation-' . $appointment->id;
            }

            // Generate payment link for online payments
            if ($appointment->payment_type === 'Online' && ! $appointment->payment_info) {
                $appointment->payment_info = 'https://payment-gateway.com/pay/' . $appointment->id;
            }
        }

        return $appointment;
    }

    public function createMeetingLink($appointmentId): array
    {
        $external_appointment = ExternalAppointment::findOrFail($appointmentId);

        $consultation = Consultations::where('external_appointment_id', $external_appointment->id)->first();

        if ($consultation) {
            $roomResponse = $this->dailyService->createRoom($consultation->appointment_number, $external_appointment->appointment_datetime);
            if (! $roomResponse['success']) {

                \Log::error('Room creation failed', $roomResponse);

                return [
                    'success' => false,
                    'message' => 'Unable to create meeting room',
                ];
            }

            $room = $roomResponse['data'];

            $doctor  = $this->dailyService->createDoctorToken($room['name']);
            $patient = $this->dailyService->createPatientToken($room['name']);

            $external_appointment->meeting_link       = $room['url'] . '?t=' . $doctor['token'];
            $external_appointment->meeting_link_type  = "auto";
            $external_appointment->daily_meeting_info = json_encode([
                'guest_access_code' => 'GUEST-' . Str::random(8),
                'room_name'         => $room['name'],
                'room_url'          => $room['url'],
                'doctor_token'      => $doctor['token'],
                'doctor_url'        => $room['url'] . '?t=' . $doctor['token'],
                'patient_token'     => $patient['token'],
                'patient_url'       => $room['url'] . '?t=' . $patient['token'],
            ]);
            $external_appointment->save();
            $this->sendMeetingLink($external_appointment->id);

        } else {
            Log::warning('No consultation found for external appointment', ['external_appointment_id' => $external_appointment->id]);
            throw new \Exception('No consultation found for this appointment. Meeting link cannot be generated.');
        }

        return [
            'meeting_link'       => $external_appointment->meeting_link,
            'daily_meeting_info' => json_decode($external_appointment->daily_meeting_info, true),
        ];
    }

    public function sendMeetingLink($externalAppointmentId, $to = ''): void
    {
        $external_appointment = ExternalAppointment::findOrFail($externalAppointmentId);
        $daily_meeting_info   = is_array($external_appointment->daily_meeting_info)
            ? $external_appointment->daily_meeting_info
            : json_decode($external_appointment->daily_meeting_info ?? '[]', true);

        $patient_parameter = [
            'patient_name' => $external_appointment->name,
            'doctor_name'  => $external_appointment->doctor->name,
            'date'         => $external_appointment->appointment_datetime->format('d-m-Y'),
            'time'         => $external_appointment->appointment_datetime->format('h:i A'),
            'meeting_link' => ($external_appointment->meeting_link_type == "auto") ? ($daily_meeting_info['patient_url'] ?? $external_appointment->meeting_link) : $external_appointment->meeting_link,
        ];

        $doctor_parameter = [
            'doctor_name'          => $external_appointment->doctor->name,
            'patient_name'         => $external_appointment->name,
            'patient_age'          => $external_appointment->age ?: 'N/A',
            'patient_gender'       => $external_appointment->gender ?: 'N/A',
            'country_of_residence' => $external_appointment->citizenship ?: 'N/A',
            'visit_type'           => $external_appointment->visit_type ?: 'N/A',
            'reason_for_visit'     => $external_appointment->symptoms ?: 'N/A',
            'date'                 => $external_appointment->appointment_datetime->format('d-m-Y'),
            'time'                 => $external_appointment->appointment_datetime->format('h:i A'),
            'meeting_link'         => ($external_appointment->meeting_link_type == "auto") ? ($daily_meeting_info['doctor_url'] ?? $external_appointment->meeting_link) : $external_appointment->meeting_link,
        ];

        $phone = $external_appointment->doctor->phone
            ? substr(preg_replace('/\D/', '', $external_appointment->doctor->phone), -10)
            : null;
        Log::info("phone" . $phone);
        $whatsappNotificationEnabled=SystemSettings::where('whatsapp_notification',true)->first();
        $emailNotificationEnabled=SystemSettings::where('email_notification',true)->first();
       
        $sendPatient = ($to === "patient" || empty($to));
        $sendDoctor  = ($to === "doctor" || empty($to));

        if ($sendPatient) {
            if ($whatsappNotificationEnabled && $whatsappNotificationEnabled->value == 1) {
                $response = $this->whatsapp->sendMessage(
                    'patient_appointment_confirmation',
                    $external_appointment->phone,
                    $patient_parameter
                );

                Log::info('WhatsApp template sent successfully (FOR PATIENT)', [
                    'response' => $response
                ]);
            }

            if ($emailNotificationEnabled && $emailNotificationEnabled->value == 1) {
                $this->sendPatientMeetingLinkEmail(
                    $external_appointment,
                    $patient_parameter,
                    $response ?? null
                );
            }
        }

        if ($sendDoctor) {
            if (
                $whatsappNotificationEnabled &&
                $whatsappNotificationEnabled->value == 1 &&
                !is_null($phone)
            ) {
                // $response = $this->whatsapp->sendMessage(
                //     'doctor_appointment_confirmation',
                //     $phone,
                //     $doctor_parameter
                // );

                // Log::info('WhatsApp template sent successfully (FOR DOCTOR)', [
                //     'response' => $response
                // ]);
            }

            if ($emailNotificationEnabled && $emailNotificationEnabled->value == 1) {
                $this->sendDoctorMeetingLinkEmail(
                    $external_appointment,
                    $doctor_parameter,
                    $response ?? null
                );
            }
        }
    }

    private function sendPatientMeetingLinkEmail(ExternalAppointment $externalAppointment, array $patientParameter, array $whatsappResponse): void
    {
        if (($whatsappResponse['success'] ?? false) !== true || empty($externalAppointment->email)) {
            return;
        }

        $body = "
            <p>Hello {$patientParameter['patient_name']},</p>

            <p>Your appointment with Dr. {$patientParameter['doctor_name']} is confirmed.</p>

            <p><strong>Date:</strong> {$patientParameter['date']}</p>
            <p><strong>Time:</strong> {$patientParameter['time']} (Indian Standard Time)</p>

            <p>
                <a href=\"{$patientParameter['meeting_link']}\">Join Meeting</a>
            </p>
            ";

        try {
            $this->emailService->sendMail(
                $externalAppointment->email,
                'Appointment Confirmed',
                $body
            );

            Log::info('Meeting link email queued successfully(FOR PATIENT)', [
                'external_appointment_id' => $externalAppointment->id,
                'email'                   => $externalAppointment->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to queue meeting link email(FOR PATIENT)', [
                'external_appointment_id' => $externalAppointment->id,
                'email'                   => $externalAppointment->email,
                'error'                   => $e->getMessage(),
            ]);
        }
    }

    private function sendDoctorMeetingLinkEmail(ExternalAppointment $externalAppointment, array $doctorParameter, array $whatsappResponse): void
    {
        if (($whatsappResponse['success'] ?? false) !== true || empty($externalAppointment->email)) {
            return;
        }

        $body = "
        <p>Hello Dr. {$doctorParameter['doctor_name']},</p>

        <p>This is to inform you that an appointment has been scheduled with the following patient:</p>

        <p>
            <strong>Name:</strong> {$doctorParameter['patient_name']}<br>
            <strong>Age/Gender:</strong> {$doctorParameter['patient_age']}/{$doctorParameter['patient_gender']}<br>
            <strong>Country of Residence:</strong> {$doctorParameter['country_of_residence']}<br>
            <strong>Visit Type:</strong> {$doctorParameter['visit_type']}<br>
            <strong>Reason for Visit:</strong> {$doctorParameter['reason_for_visit']}
        </p>

        <p>
            <strong>Date:</strong> {$doctorParameter['date']}<br>
            <strong>Time:</strong> {$doctorParameter['time']} (Indian Standard Time)
        </p>

        <p>Please join the meeting using the following link:</p>

        <p>
            <a href=\"{$doctorParameter['meeting_link']}\">Join Meeting</a>
        </p>

        <p>Thank you.</p>

        <p><strong>Acharya Sushrutha Healthcare</strong></p>
        ";

        try {
            $this->emailService->sendMail(
                $externalAppointment->email,
                'Appointment Confirmed',
                $body
            );

            Log::info('Meeting link email queued successfully(FOR DOCTOR)', [
                'external_appointment_id' => $externalAppointment->id,
                'email'                   => $externalAppointment->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to queue meeting link email(FOR DOCTOR)', [
                'external_appointment_id' => $externalAppointment->id,
                'email'                   => $externalAppointment->email,
                'error'                   => $e->getMessage(),
            ]);
        }
    }

    public function logMeetingInfo($externalAppointmentId, $roomName, $meetingStatus)
    {
        return MeetingLog::create([
            'external_appointment_id' => $externalAppointmentId,
            'room_name'               => $roomName,
            'meeting_status'          => $meetingStatus,
        ]);
    }

}
