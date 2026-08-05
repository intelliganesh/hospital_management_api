<?php
namespace App\Http\Controllers;

use App\Interceptors\ServiceInterceptor;
use App\Services\ConsultationReportService;
use App\Services\ConsultationService;
use App\Services\InvoiceService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Consultations",
 *     description="API endpoints for managing consultations"
 * )
 */

class ConsultationController extends Controller
{
    use ResponseTrait;

    private $consultantionService;
    private $invoiceService;
    private $consultationReportService;

    /**
     * Summary of __construct
     * @param \App\Services\ConsultationService $consultantionService
     * @param \App\Services\ConsultationReportService $consultationReportService
     * @param \App\Services\InvoiceService $invoiceService
     */
    public function __construct(ConsultationService $consultantionService, InvoiceService $invoiceService, ConsultationReportService $consultationReportService)
    {
        $this->consultantionService      = $consultantionService;
        $this->invoiceService            = $invoiceService;
        $this->consultationReportService = $consultationReportService;
    }

    /**
     * @OA\Get(
     *     path="/api/consultations_statistics",
     *     tags={"Consultations"},
     *     summary="Get consultation statistics",
     *     security={{"bearerAuth": {}}},
     *     description="Returns statistics about consultations including total count, today's count, and completed count",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consultation statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_consultations", type="integer", example=120),
     *                 @OA\Property(property="todays_consultations", type="integer", example=8),
     *                 @OA\Property(property="completed_consultations", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function getStatistics()
    {
        try {
            $statistics = $this->consultantionService->getStatistics();
            return $this->successResponse($statistics, 'Consultation statistics retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultations_list",
     *     tags={"Consultations"},
     *     summary="Get all consultations",
     *     security={{"bearerAuth": {}}},
     *     description="Get all consultations, appointment type, next visit date",
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example="vishnu"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (type, status, appointment_time, etc.)",
     *         @OA\Schema(
     *             type="string",
     *             example="name"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort direction (asc or desc)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="asc"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="Page number for pagination",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Number of items per page",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultations retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="appointment_type", type="string", example="Appointment type"),
     *                     @OA\Property(property="patient_name", type="string", example="Chetan"),
     *                     @OA\Property(property="doctor_name", type="string", example="MP Vishnu Prakash"),
     *                     @OA\Property(property="next_visit_date", type="string", format="date", example="2023-01-01")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=15),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=2),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="next", type="string", example="http://example.com/api/consultations_list?page=2")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->consultantionService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultations_details/{id}",
     *     summary="Get single consultation",
     *     tags={"Consultations"},
     *     description="Fetch a single consultation by ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Consultation ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation details fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="Consultation"),
     *                 @OA\Property(property="status", type="string", example="Confirmed"),
     *                 @OA\Property(property="appointment_time", type="string", example="15:30"),
     *                 @OA\Property(property="appointment_date", type="string", example="2025-04-24"),
     *                 @OA\Property(property="appointment_number", type="string", example="APT-000123"),
     *                 @OA\Property(
     *                     property="doctor",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Dr. John"),
     *                     @OA\Property(property="email", type="string", example="john.doe@hospital.com"),
     *                     @OA\Property(property="phone_no", type="string", example="9876543210")
     *                 ),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="first_name", type="string", example="Jane"),
     *                     @OA\Property(property="last_name", type="string", example="Smith"),
     *                     @OA\Property(property="email", type="string", example="jane.smith@example.com"),
     *                     @OA\Property(property="phone_no", type="string", example="9123456780")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->consultantionService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Consultation data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    // /**
    //  * @OA\Post(
    //  *     path="/api/consultations_add",
    //  *     summary="Create a new consultation",
    //  *     tags={"Consultations"},
    //  *     security={{"bearerAuth": {}}},
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *             required={"patient_id","appointment_id", "doctor_id", "next_visit_date", },
    //  *             @OA\Property(property="appointment_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
    //  *             @OA\Property(property="patient_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
    //  *             @OA\Property(property="doctor_id", type="integer", example=1),
    //  *             @OA\Property(property="next_visit_date", type="string", example="2024-01-01"),
    //  *             @OA\Property(property="complaint", type="string", example="Headache"),
    //  *             @OA\Property(property="advice", type="string", example="Advice for headache"),
    //  *             @OA\Property(property="preliminary_diagnosis", type="string", example="Diagnosis for headache")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=201,
    //  *         description="Consultation created successfully",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="status", type="string", example="success"),
    //  *             @OA\Property(property="message", type="string", example="Consultation created successfully.")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         ref="#/components/responses/NotFound"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         ref="#/components/responses/ServerErrorResponse"
    //  *     )
    //  * )
    //  */
    // public function create(Request $request)
    // {
    //     try {
    //         $proxiedService = ServiceInterceptor::intercept($this->consultantionService);
    //         $proxiedService->create($request);
    //         return $this->successResponse();
    //     } catch (ValidationException $ve) {
    //         return $this->errorResponse(
    //             $ve->validator->errors()->toArray(),
    //             'Validation error',
    //             422
    //         );
    //     } catch (Exception $e) {
    //         return $this->exceptionResponse($e);
    //     }
    // }

    /**
     * @OA\Put(
     *     path="/api/consultations_update/{id}",
     *     summary="Update a consultation",
     *     tags={"Consultations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Consultation ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"patient_id","appointment_id", "doctor_id", "next_visit_date" },
     *             @OA\Property(property="appointment_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *             @OA\Property(property="doctor_id", type="integer", example=1),
     *             @OA\Property(property="next_visit_date", type="string", example="2024-01-01"),
     *             @OA\Property(property="complaint", type="string", example="Headache"),
     *             @OA\Property(property="advice", type="string", example="Advice for headache"),
     *             @OA\Property(property="preliminary_diagnosis", type="string", example="Diagnosis for headache"),
     *             @OA\Property(property="temperature", type="string", example="98.6 F"),
     *             @OA\Property(property="bp", type="string", example="120/80"),
     *             @OA\Property(property="pulse", type="string", example="72 bpm"),
     *             @OA\Property(property="cvs", type="string", example="Normal heart sounds, no murmurs"),
     *             @OA\Property(property="rs", type="string", example="Clear breath sounds"),
     *             @OA\Property(property="description", type="string", example="General examination notes"),
     *             @OA\Property(property="examination_overview", type="string", example="Patient appears stable, no acute distress"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->consultantionService);
            return $this->successResponse(['id' => $proxiedService->updateConsultation($request, $id), 'patient_id' => $request->patient_id]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Consultation data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/consultations_delete/{id}",
     *     summary="Delete a consultation",
     *     tags={"Consultations"},
     *     description="Delete a consultation by its ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Consultation ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function delete(string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->consultantionService);
            $proxiedService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Consultation data not found.');
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($ne);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultations_list_for_dropdown",
     *     summary="Get list of consultations",
     *     tags={"Consultations"},
     *     description="Get list of consultations",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Consultation list",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation list retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="appointment_number", type="string", example="APT-000123")
     *             ))
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function consultationList()
    {
        try {
            return $this->successResponse($this->consultantionService->consultationList());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function patientConsultationList(Request $request)
    {
        try {
            return $this->successResponse($this->consultantionService->patientConsultationList($request));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/patients/{id}/anaesthesia_download",
     *     summary="Download anaesthesia form PDF for a patient",
     *     tags={"Patients"},
     *     security={{"bearerAuth": {}}},
     *     description="Generates and downloads the IPD form (part 1) as a PDF for a given patient ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Patient ID",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="019623b3-466b-705e-983b-5ec43d5f6e7a"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file downloaded successfully",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/pdf",
     *                 @OA\Schema(type="string", format="binary")
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function generateConsultationReport(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->consultationReportService->generateConsultationReport($id)]);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (InvalidArgumentException $invalidArgument) {
            return $this->errorResponse(
                [],
                $invalidArgument->getMessage(),
                400
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/prescription_download/{id}",
     *     summary="Download prescription PDF for a consultation",
     *     description="Returns a URL to download the prescription PDF for the specified consultation",
     *     tags={"Consultations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Consultation ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription PDF URL retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="url", type="string", example="https://example.com/storage/pdfs/prescription_123.pdf")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid consultation ID format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Consultation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred while generating the prescription")
     *         )
     *     )
     * )
     */
    public function downloadPrescription(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->consultationReportService->downloadPrescription($id)]);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (InvalidArgumentException $invalidArgument) {
            return $this->errorResponse(
                [],
                $invalidArgument->getMessage(),
                400
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultation_prescription/{appointment_id}",
     *     summary="Download prescription PDF for a consultation",
     *     description="Returns the prescription data for the specified consultation date",
     *     tags={"Consultations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="appointment_id",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation prescription data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="html", type="string", example="<html>...</html>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid consultation Date format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Consultation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred while generating the prescription")
     *         )
     *     )
     * )
     */
    public function getConsultationPrescriptionData($appointment_id)
    {
        try {
            return $this->successResponse(['html_data' => $this->invoiceService->getConsultationPrescriptionData($appointment_id)]);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (InvalidArgumentException $invalidArgument) {
            return $this->errorResponse(
                [],
                $invalidArgument->getMessage(),
                400
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultation_dates/{patient_id}",
     *     summary="Get consultation dates for a patient",
     *     description="Returns a list of consultation dates for the specified patient",
     *     tags={"Consultations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="patient_id",
     *         in="path",
     *         required=true,
     *         description="Patient ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation dates retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="html", type="string", example="<html>...</html>")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid consultation Date format")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Consultation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Consultation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="An error occurred while generating the prescription")
     *         )
     *     )
     * )
     */
    public function getConsultationDatesForPatient($patient_id)
    {
        try {
            return $this->successResponse(['dates' => $this->consultantionService->getConsultationDatesForPatient($patient_id)]);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (InvalidArgumentException $invalidArgument) {
            return $this->errorResponse(
                [],
                $invalidArgument->getMessage(),
                400
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
