<?php
namespace App\Http\Controllers;

use App\Services\IPDDownloadService;
use App\Services\IpdService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Management",
 *     description="API endpoints for managing IPD (In-Patient Department) admissions and patient enrollment"
 * )
 */
class IpdController extends Controller
{
    use ResponseTrait;

    private $ipdService;
    private $ipdDownloadService;

    public function __construct(IpdService $ipdService, IPDDownloadService $ipdDownloadService)
    {
        $this->ipdService         = $ipdService;
        $this->ipdDownloadService = $ipdDownloadService;
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_add",
     *     tags={"IPD Management"},
     *     summary="Add patient to IPD from consultation",
     *     description="Enroll a patient from consultation to IPD with doctor, nurse assignments and ward/room/bed allocation",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_id","admission_date_time","patient_first_name","patient_last_name", "patient_gender","patient_attendant_name","patient_attendant_phone"},
     *             @OA\Property(property="consultation_id", type="string", format="uuid", description="Consultation ID", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="consultant_doctor_id", type="string", format="uuid", description="Consultant Doctor ID", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="patient_id", type="string", format="uuid", description="Patient ID (required for existion patient)", example="550e8400-e29b-41d4-a716-446655440001"),
     *             @OA\Property(property="patient_first_name", type="string", description="Patient first name (required for new patient)", example="John"),
     *             @OA\Property(property="patient_last_name", type="string", description="Patient last name (required for new patient)", example="Doe"),
     *             @OA\Property(property="patient_gender", type="string", description="Patient gender (required for new patient)", example="Male"),
     *             @OA\Property(property="patient_attendant_name", type="string", description="Patient attendant name (required for new patient)", example="Jane Doe"),
     *             @OA\Property(property="patient_attendant_phone", type="string", description="Patient attendant phone (required for new patient)", example="1234567890"),
     *             @OA\Property(property="admission_date_time", type="string", format="date-time", description="Admission date and time (required, format: Y-m-d H:i:s)", example="2025-12-31 14:30:00"),
     *             @OA\Property(property="ward_id", type="integer", description="Ward ID (required)", example=1),
     *             @OA\Property(property="room_id", type="integer", description="Room ID (required)", example=5),
     *             @OA\Property(property="bed_id", type="integer", description="Bed ID (required)", example=12),
     *             @OA\Property(property="advance_amount", type="number", format="float", description="Advance amount (required, min: 0)", example=5000.00),
     *             @OA\Property(property="currency", type="string", description="Currency (optional, default: ₹)", example="₹"),
     *             @OA\Property(property="payment_type", type="string", description="Payment type (optional, default: Cash)", example="Cash"),
     *             @OA\Property(property="transaction_id", type="string", description="Transaction ID (optional)", example="TXN123456789"),
     *             @OA\Property(property="payment_date", type="string", format="date-time", description="Payment date (optional, format: Y-m-d H:i:s)", example="2025-12-31 14:30:00"),
     *             @OA\Property(property="notes", type="string", description="Additional notes (optional)", example="Patient requires special care"),
     *             @OA\Property(property="status", type="string", description="Status (required, values: Admitted, Discharged, Expired, Under Treatment)", example="Admitted"),
     *             @OA\Property(
     *                 property="consultant_doctor",
     *                 type="array",
     *                 description="Array of consultant doctor user IDs (required, min 1)",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     *             @OA\Property(
     *                 property="duty_doctor",
     *                 type="array",
     *                 description="Array of duty doctor user IDs (optional)",
     *                 @OA\Items(type="integer", example=2)
     *             ),
     *             @OA\Property(
     *                 property="nurse",
     *                 type="array",
     *                 description="Array of nurse user IDs (optional)",
     *                 @OA\Items(type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient successfully enrolled to IPD",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Patient successfully enrolled to IPD"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="patient_id", type="string", format="uuid"),
     *                 @OA\Property(property="consultation_id", type="string", format="uuid"),
     *                 @OA\Property(property="ipd_number", type="string"),
     *                 @OA\Property(property="admission_date_time", type="string", format="date-time"),
     *                 @OA\Property(property="ward_id", type="integer"),
     *                 @OA\Property(property="room_id", type="integer"),
     *                 @OA\Property(property="bed_id", type="integer"),
     *                 @OA\Property(property="advance_amount", type="number", format="float")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation or patient not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $ipd = $this->ipdService->create($request);
            return $this->successResponse($ipd, 'IPD enrollment created successfully');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_list",
     *     tags={"IPD Management"},
     *     summary="Get all IPD records",
     *     description="Retrieve a paginated list of all IPD admissions with optional filtering and sorting",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search by 'ipd_number','patient_number','patient_name','patient_phone','patient_email','patient_address','ward_number','ward_type',','room_type','room_number','status','bed_number','admission_date','consultant_doctor','duty_doctor','nurse'",
     *         @OA\Schema(
     *             type="string",
     *             example="John Doe"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example="admission_date_time"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Sort direction (asc or desc)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="desc"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Records per page",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of IPD records retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD records retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", format="uuid"),
     *                         @OA\Property(property="ipd_number", type="string"),
     *                         @OA\Property(property="patient_id", type="string", format="uuid"),
     *                         @OA\Property(property="consultation_id", type="string", format="uuid"),
     *                         @OA\Property(property="admission_date_time", type="string", format="date-time"),
     *                         @OA\Property(property="ward_id", type="integer"),
     *                         @OA\Property(property="room_id", type="integer"),
     *                         @OA\Property(property="bed_id", type="integer"),
     *                         @OA\Property(property="advance_amount", type="number", format="float")
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="last_page", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->ipdService->all($request), 'IPD records retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_details/{id}",
     *     tags={"IPD Management"},
     *     summary="Get IPD record details",
     *     description="Retrieve detailed information about a specific IPD admission including patient, consultation, and staff assignments",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD record details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD record retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="ipd_number", type="string"),
     *                 @OA\Property(property="patient_id", type="string", format="uuid"),
     *                 @OA\Property(property="consultation_id", type="string", format="uuid"),
     *                 @OA\Property(property="admission_date_time", type="string", format="date-time"),
     *                 @OA\Property(property="discharge_date_time", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="ward_id", type="integer"),
     *                 @OA\Property(property="room_id", type="integer"),
     *                 @OA\Property(property="bed_id", type="integer"),
     *                 @OA\Property(property="advance_amount", type="number", format="float"),
     *                 @OA\Property(property="patient", type="object"),
     *                 @OA\Property(property="consultation", type="object"),
     *                 @OA\Property(property="staffs", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->ipdService->get($id), 'IPD record retrieved successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_update/{id}",
     *     tags={"IPD Management"},
     *     summary="Update IPD record",
     *     description="Update IPD admission details including ward, room, bed allocation and staff assignments",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="consultant_doctor_id", type="string", format="uuid", description="Consultant Doctor ID", example="550e8400-e29b-41d4-a716-446655440000"),
     *             @OA\Property(property="ward_id", type="integer", description="Ward ID (optional)"),
     *             @OA\Property(property="room_id", type="integer", description="Room ID (optional)"),
     *             @OA\Property(property="bed_id", type="integer", description="Bed ID (optional)"),
     *             @OA\Property(property="advance_amount", type="number", format="float", description="Advance amount (optional)"),
     *             @OA\Property(property="payment_date", type="string", format="date-time", description="Payment date (optional, format: Y-m-d H:i:s)", example="2025-12-31 14:30:00"),
     *             @OA\Property(property="transaction_id", type="string", description="Transaction ID (optional)", example="TXN123456789"),
     *             @OA\Property(property="notes", type="string", description="Additional notes (optional)", example="Patient requires special care"),
     *             @OA\Property(property="currency", type="string", description="Currency (optional, default: ₹)", example="₹"),
     *             @OA\Property(property="payment_type", type="string", description="Payment type (optional, default: Cash)", example="Cash"),
     *             @OA\Property(property="admission_date_time", type="string", format="date-time", description="Admission date and time (optional, format: Y-m-d H:i:s)", example="2025-12-31 14:30:00"),
     *             @OA\Property(property="discharge_date_time", type="string", format="date-time", description="Discharge date and time (optional, format: Y-m-d H:i:s)", example="2025-12-31 14:30:00"),
     *             @OA\Property(property="status", type="string", description="Status (required, values: Admitted, Discharged, Expired, Under Treatment)", example="Admitted"),
     *             @OA\Property(
     *                 property="consultant_doctor",
     *                 type="array",
     *                 description="Array of consultant doctor user IDs (optional)",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     *             @OA\Property(
     *                 property="duty_doctor",
     *                 type="array",
     *                 description="Array of duty doctor user IDs (optional)",
     *                 @OA\Items(type="integer", example=2)
     *             ),
     *             @OA\Property(
     *                 property="nurse",
     *                 type="array",
     *                 description="Array of nurse user IDs (optional)",
     *                 @OA\Items(type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD record updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD record updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $ipd = $this->ipdService->update($request, $id);
            return $this->successResponse($ipd, 'IPD record updated successfully');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/ipd_delete/{id}",
     *     tags={"IPD Management"},
     *     summary="Delete IPD record",
     *     description="Delete an IPD admission record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD record deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD record deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->ipdService->delete($id);
            return $this->successResponse(null, 'IPD record deleted successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_generate_pdf/{id}",
     *     tags={"IPD Management"},
     *     summary="Generate IPD document as PDF",
     *     description="Generate various IPD documents as PDF (preliminary_notes, anaesthesia_consent_form, doctor_notes, nurse_notes, discharge_summary, surgery_consent_form, pre_anaesthesia_assessment,department_of_anaesthesia, pre_operative_checklist, anaesthesia_recovery_room_observation, surgery_report,billing_invoice)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *      @OA\Parameter(
     *         name="ipd_surgery_id",
     *         in="query",
     *         required=false,
     *         description="IPD surgery ID (required for surgery_report,pre_operative_checklist,surgery_consent_form,anaesthesia_consent_form,billing_invoice,anaesthesia_recovery_room_observation type,anaesthesia_record)",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Document type to download",
     *         @OA\Schema(
     *             type="string",
     *             enum={"preliminary_notes", "doctor_notes", "nurse_notes", "discharge_summary", "surgery_consent_form","anaesthesia_consent_form", "pre_anaesthesia_assessment","anaesthesia_record", "department_of_anaesthesia","pre_operative_checklist", "anaesthesia_recovery_room_observation", "surgery_report","billing_invoice"},
     *             example="preliminary_notes"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file generated successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid document type"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function generatePdf(Request $request, string $id)
    {
        try {
            $type       = $request->query('type');
            $surgery_id = $request->query('ipd_surgery_id') ?? null;

            if (! $type) {
                return $this->errorResponse(null, 'Document type is required', 400);
            }

            $validTypes = [
                'preliminary_notes',
                'doctor_notes',
                'nurse_notes',
                'discharge_summary',
                'surgery_consent_form',
                'anaesthesia_consent_form',
                'pre_anaesthesia_assessment',
                'department_of_anaesthesia',
                'pre_operative_checklist',
                'anaesthesia_recovery_room_observation',
                'surgery_report',
                'anaesthesia_record',
                'billing_invoice',
            ];

            if (! in_array($type, $validTypes)) {
                return $this->errorResponse(null, 'Invalid document type. Valid types: ' . implode(', ', $validTypes), 400);
            }

            return $this->successResponse(['url' => $this->ipdDownloadService->generatePdf($id, $type, $surgery_id)], 'PDF file generated successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_download_pdf/{id}",
     *     tags={"IPD Management"},
     *     summary="Download IPD document as PDF",
     *     description="Download various IPD documents as PDF (preliminary_notes,anaesthesia_consent_form, doctor_notes, nurse_notes, discharge_summary, consent_form, pre_anaesthesia_assessment,department_of_anaesthesia, pre_operative_checklist, anaesthesia_record, anaesthesia_recovery_room_observation, surgery_report, billing_invoice)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="ipd_surgery_id",
     *         in="query",
     *         required=false,
     *         description="IPD surgery ID (required for surgery_report,pre_operative_checklist,billing_invoice,surgery_consent_form,anaesthesia_consent_form,anaesthesia_recovery_room_observation type,anaesthesia_record)",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Document type to download",
     *         @OA\Schema(
     *             type="string",
     *             enum={"preliminary_notes", "doctor_notes", "nurse_notes", "discharge_summary", "surgery_consent_form", "anaesthesia_consent_form","pre_anaesthesia_assessment", "department_of_anaesthesia","anaesthesia_record","pre_operative_checklist", "anaesthesia_recovery_room_observation", "surgery_report","billing_invoice"},
     *             example="preliminary_notes"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file downloaded successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid document type"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function downloadPdf(Request $request, string $id)
    {
        try {
            $type       = $request->query('type');
            $surgery_id = $request->query('ipd_surgery_id') ?? null;

            if (! $type) {
                return $this->errorResponse(null, 'Document type is required', 400);
            }

            $validTypes = [
                'preliminary_notes',
                'doctor_notes',
                'nurse_notes',
                'discharge_summary',
                'surgery_consent_form',
                'anaesthesia_consent_form',
                'pre_anaesthesia_assessment',
                'department_of_anaesthesia',
                'pre_operative_checklist',
                'anaesthesia_recovery_room_observation',
                'surgery_report',
                'anaesthesia_record',
                'billing_invoice',
            ];

            if (! in_array($type, $validTypes)) {
                return $this->errorResponse(null, 'Invalid document type. Valid types: ' . implode(', ', $validTypes), 400);
            }

            return $this->successResponse(['url' => $this->ipdDownloadService->downloadPdf($id, $type, $surgery_id)], 'PDF file generated successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_download_empty_pdf/{id}",
     *     tags={"IPD Management"},
     *     summary="Download EmptyIPD document as PDF",
     *     description="Download various EmptyIPD documents as PDF (preliminary_notes,anaesthesia_consent_form, doctor_notes, nurse_notes, discharge_summary, consent_form, pre_anaesthesia_assessment,department_of_anaesthesia, pre_operative_checklist, anaesthesia_record, anaesthesia_recovery_room_observation, surgery_report, billing_invoice)",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=true,
     *         description="Document type to download",
     *         @OA\Schema(
     *             type="string",
     *             enum={"preliminary_notes", "doctor_notes", "nurse_notes", "discharge_summary", "surgery_consent_form", "anaesthesia_consent_form","pre_anaesthesia_assessment", "department_of_anaesthesia","anaesthesia_record","pre_operative_checklist", "anaesthesia_recovery_room_observation", "surgery_report","billing_invoice"},
     *             example="preliminary_notes"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file downloaded successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid document type"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function downloadEmptyPdf(Request $request, string $id)
    {
        try {
            $type       = $request->query('type');

            if (! $type) {
                return $this->errorResponse(null, 'Document type is required', 400);
            }

            $validTypes = [
                'preliminary_notes',
                'doctor_notes',
                'nurse_notes',
                'discharge_summary',
                'surgery_consent_form',
                'anaesthesia_consent_form',
                'pre_anaesthesia_assessment',
                'department_of_anaesthesia',
                'pre_operative_checklist',
                'anaesthesia_recovery_room_observation',
                'surgery_report',
                'anaesthesia_record',
                'billing_invoice',
            ];

            if (! in_array($type, $validTypes)) {
                return $this->errorResponse(null, 'Invalid document type. Valid types: ' . implode(', ', $validTypes), 400);
            }

            return $this->successResponse(['url' => $this->ipdDownloadService->downloademptyPdf($id, $type)], 'Empty PDF file generated successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/ipd_prefilled_uploaded_pdf/{id}",
     *     tags={"IPD Management"},
     *     summary="Download Prefilled Uploaded IPD document as PDF",
     *     description="Download various Prefilled Uploaded IPD documents",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file downloaded successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid document type"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function downloadPrefilledUploadPdf(Request $request, string $id)
    {
        try {
            $type = $request->query('type', 'all');
            return $this->successResponse(['url' => $this->ipdDownloadService->downloadprefilledUploadPdf($id, $type)], 'PDF file list');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    

    /**
     * @OA\Get(
     *     path="/api/patient_preoperative_checklist/{id}",
     *     tags={"IPD Management"},
     *     summary="Download Pre Operative Checklist document as PDF",
     *     description="Download Pre Operative Checklist document as PDF",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Patient ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file downloaded successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid document type"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function download_patiient_preoperative_checklist(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->ipdDownloadService->preOperative_checklist($id)]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
