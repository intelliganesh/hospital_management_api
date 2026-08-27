<?php
namespace App\Http\Controllers;

use App\Services\IPDDischargeSummaryService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Discharge Summary",
 *     description="API endpoints for managing IPD discharge summaries"
 * )
 */
class IPDDischargeSummaryController extends Controller
{
    use ResponseTrait;

    private $dischargeSummaryService;

    public function __construct(IPDDischargeSummaryService $dischargeSummaryService)
    {
        $this->dischargeSummaryService = $dischargeSummaryService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_discharge_summary_list",
     *     summary="Get all Discharge summaries with optional search and filter",
     *     tags={"IPD Discharge Summary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ipd_id",
     *         in="query",
     *         required=false,
     *         description="Filter by IPD ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discharge summaries retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Discharge summaries retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
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
            return $this->successResponse($this->dischargeSummaryService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_discharge_summary_details/{id}",
     *     summary="Get IPD discharge summary details by IPD ID",
     *     tags={"IPD Discharge Summary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discharge summary retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Discharge summary retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discharge summary not found"
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
            return $this->successResponse($this->dischargeSummaryService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD discharge summary data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_discharge_summary_add",
     *     summary="Add IPD discharge summary",
     *     tags={"IPD Discharge Summary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"ipd_id"},
     *             @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *             @OA\Property(property="doctor_incharge", type="string", example="Dr. John Doe"),
     *             @OA\Property(property="consultants", type="string", example="Dr. Smith, Dr. Patel"),
     *             @OA\Property(property="diagnosis", type="string", example="Fistula in ano"),
     *             @OA\Property(property="case_history_and_complaints", type="string", example="Patient presented with pain and discharge."),
     *             @OA\Property(property="general_examination", type="string", example="Patient conscious and oriented."),
     *             @OA\Property(property="systemic_examination", type="string", example="RS/CVS/CNS clinically normal."),
     *             @OA\Property(property="investigations", type="string", example="CBC, ECG, chest X-ray reviewed."),
     *             @OA\Property(property="operation_done", type="string", example="Fistulectomy"),
     *             @OA\Property(property="findings_and_procedure", type="string", example="Findings and procedure details."),
     *             @OA\Property(property="course_in_hospital", type="string", example="Post-operative period uneventful."),
     *             @OA\Property(property="patient_health_condition_at_discharge", type="string", example="Stable"),
     *             @OA\Property(property="advice_on_discharge", type="string", example="Follow medication and dressing instructions."),
     *             @OA\Property(property="medicines", type="string", example="Paracetamol, Amoxicillin"),
     *             @OA\Property(property="combination_medicines", type="string", example="Ibuprofen + Acetaminophen"),
     *             @OA\Property(property="tests", type="string", example="Complete Blood Count, Urinalysis"),
     *             @OA\Property(property="diet_plan", type="string", example="High protein, low salt diet"),
     *             @OA\Property(property="special_instruction", type="string", example="Follow up after 7 days."),
     *             @OA\Property(property="upload_pdf_path", type="string", example="pdfs/ipd/IPD-001/uploads/discharge_summary_IPD-001.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Discharge summary created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Discharge summary created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD not found"
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
            $this->dischargeSummaryService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_discharge_summary_update/{id}",
     *     summary="Update IPD discharge summary using IPD ID",
     *     tags={"IPD Discharge Summary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Discharge summary ID", @OA\Schema(type="string", format="uuid")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"ipd_id"},
     *             @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *             @OA\Property(property="doctor_incharge", type="string", example="Dr. John Doe"),
     *             @OA\Property(property="consultants", type="string", example="Dr. Smith, Dr. Patel"),
     *             @OA\Property(property="diagnosis", type="string", example="Fistula in ano"),
     *             @OA\Property(property="case_history_and_complaints", type="string", example="Patient presented with pain and discharge."),
     *             @OA\Property(property="general_examination", type="string", example="Patient conscious and oriented."),
     *             @OA\Property(property="systemic_examination", type="string", example="RS/CVS/CNS clinically normal."),
     *             @OA\Property(property="investigations", type="string", example="CBC, ECG, chest X-ray reviewed."),
     *             @OA\Property(property="operation_done", type="string", example="Fistulectomy"),
     *             @OA\Property(property="findings_and_procedure", type="string", example="Findings and procedure details."),
     *             @OA\Property(property="course_in_hospital", type="string", example="Post-operative period uneventful."),
     *             @OA\Property(property="patient_health_condition_at_discharge", type="string", example="Stable"),
     *             @OA\Property(property="advice_on_discharge", type="string", example="Follow medication and dressing instructions."),
     *             @OA\Property(property="medicines", type="string", example="Paracetamol, Amoxicillin"),
     *             @OA\Property(property="combination_medicines", type="string", example="Ibuprofen + Acetaminophen"),
     *             @OA\Property(property="tests", type="string", example="Complete Blood Count, Urinalysis"),
     *             @OA\Property(property="diet_plan", type="string", example="High protein, low salt diet"),
     *             @OA\Property(property="special_instruction", type="string", example="Follow up after 7 days."),
     *             @OA\Property(property="upload_pdf_path", type="string", example="pdfs/ipd/IPD-001/uploads/discharge_summary_IPD-001.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discharge summary updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Discharge summary updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discharge summary not found"
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
            $this->dischargeSummaryService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD discharge summary data not found.');
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
     *     path="/api/ipd_discharge_summary_delete/{id}",
     *     summary="Delete IPD discharge summary",
     *     tags={"IPD Discharge Summary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Discharge summary ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Discharge summary deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Discharge summary deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Discharge summary not found"
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
            $this->dischargeSummaryService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD discharge summary data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
