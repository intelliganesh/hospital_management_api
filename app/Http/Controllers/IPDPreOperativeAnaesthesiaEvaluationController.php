<?php
namespace App\Http\Controllers;

use App\Services\IPDPreOperativeAnaesthesiaEvaluationService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Pre-Operative Anaesthesia Evaluation",
 *     description="API endpoints for managing IPD pre-operative anaesthesia evaluation records"
 * )
 */
class IPDPreOperativeAnaesthesiaEvaluationController extends Controller
{
    use ResponseTrait;
    private $evaluationService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDPreOperativeAnaesthesiaEvaluationService $evaluationService
     */
    public function __construct(IPDPreOperativeAnaesthesiaEvaluationService $evaluationService)
    {
        $this->evaluationService = $evaluationService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_pre_operative_anaesthesia_evaluation_list",
     *     summary="Get all IPD pre-operative anaesthesia evaluation records",
     *     description="Retrieve a list of all IPD pre-operative anaesthesia evaluation records in the system",
     *     tags={"IPD Pre-Operative Anaesthesia Evaluation"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example=""
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
     *     @OA\Parameter(
     *          name="multiple_filter[ipd_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by IPD ID",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[datetime]",
     *          in="query",
     *          required=false,
     *          description="Filter by date (YYYY-MM-DD)",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2026-03-11"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of IPD pre-operative anaesthesia evaluation records",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pre-operative anaesthesia evaluation records retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                     @OA\Property(property="previous_anaesthesia_surgery", type="string", example="No previous surgery"),
     *                     @OA\Property(property="asa_grading", type="string", example="II"),
     *                     @OA\Property(property="blood_group", type="string", example="O+"),
     *                     @OA\Property(property="datetime", type="string", format="date-time", example="2026-03-11 10:00:00"),
     *                     @OA\Property(property="summary", type="string", example="Pre-operative evaluation completed")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=5),
     *                     @OA\Property(property="count", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=1),
     *                     @OA\Property(property="links", type="object")
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
            return $this->successResponse($this->evaluationService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_pre_operative_anaesthesia_evaluation_details/{id}",
     *     summary="Get complete IPD pre-operative anaesthesia evaluation details",
     *     tags={"IPD Pre-Operative Anaesthesia Evaluation"},
     *     description="Get complete IPD pre-operative anaesthesia evaluation details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="IPD Surgery ID of the pre-operative anaesthesia evaluation record to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful pre-operative anaesthesia evaluation details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pre-operative anaesthesia evaluation details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="previous_anaesthesia_surgery", type="string", example="No previous surgery"),
     *                 @OA\Property(property="current_medication", type="string", example="Aspirin 100mg daily"),
     *                 @OA\Property(property="allergies", type="string", example="Penicillin"),
     *                 @OA\Property(property="asa_grading", type="string", example="II"),
     *                 @OA\Property(property="airway_assessment", type="string", example="Normal"),
     *                 @OA\Property(property="respiratory_system", type="string", example="Normal"),
     *                 @OA\Property(property="cardio_vascular_system", type="string", example="Normal"),
     *                 @OA\Property(property="cns_musculoskeletal", type="string", example="Normal"),
     *                 @OA\Property(property="hepatic_renal", type="string", example="Normal"),
     *                 @OA\Property(property="endocrine", type="string", example="Normal"),
     *                 @OA\Property(property="blood_group", type="string", example="O+"),
     *                 @OA\Property(property="hb_hct", type="string", example="13.5 g/dL, 40%"),
     *                 @OA\Property(property="mouth_opening", type="string", example=">3cm"),
     *                 @OA\Property(property="teeth", type="string", example="Normal"),
     *                 @OA\Property(property="neck_movement", type="string", example="Good"),
     *                 @OA\Property(property="mallampati_score", type="string", example="Class 2"),
     *                 @OA\Property(property="dentures_check", type="string", example="No dentures"),
     *                 @OA\Property(property="tmd", type="string", example="Normal"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-11 10:00:00")
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
            return $this->successResponse($this->evaluationService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Pre-operative anaesthesia evaluation record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_pre_operative_anaesthesia_evaluation_add",
     *     summary="Add IPD pre-operative anaesthesia evaluation record",
     *     tags={"IPD Pre-Operative Anaesthesia Evaluation"},
     *     description="Add a new IPD pre-operative anaesthesia evaluation record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new IPD pre-operative anaesthesia evaluation record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="ipd_anaesthesia_id", type="string", format="uuid", example="b123b456-7c89-0d12-34e5-678901232333"),
     *                 @OA\Property(property="previous_anaesthesia_surgery", type="string", example="No previous surgery"),
     *                 @OA\Property(property="current_medication", type="string", example="Aspirin 100mg daily"),
     *                 @OA\Property(property="allergies", type="string", example="Penicillin"),
     *                 @OA\Property(property="asa_grading", type="string", example="II"),
     *                 @OA\Property(property="airway_assessment", type="string", example="Normal"),
     *                 @OA\Property(property="respiratory_system", type="string", example="Normal"),
     *                 @OA\Property(property="cardio_vascular_system", type="string", example="Normal"),
     *                 @OA\Property(property="cns_musculoskeletal", type="string", example="Normal"),
     *                 @OA\Property(property="hepatic_renal", type="string", example="Normal"),
     *                 @OA\Property(property="endocrine", type="string", example="Normal"),
     *                 @OA\Property(property="other_system", type="string", example="Normal"),
     *                 @OA\Property(property="clinical_evaluation", type="string", example="Fit for surgery"),
     *                 @OA\Property(property="hb_hct", type="string", example="13.5 g/dL, 40%"),
     *                 @OA\Property(property="tc", type="string", example="7500/cumm"),
     *                 @OA\Property(property="platelets", type="string", example="250,000/cumm"),
     *                 @OA\Property(property="bt_ct", type="string", example="2 min, 5 min"),
     *                 @OA\Property(property="pt_ptt", type="string", example="12 sec, 30 sec"),
     *                 @OA\Property(property="inr", type="string", example="1.0"),
     *                 @OA\Property(property="blood_group", type="string", example="O+"),
     *                 @OA\Property(property="fbs_rbs", type="string", example="100 mg/dL, 120 mg/dL"),
     *                 @OA\Property(property="bun", type="string", example="14 mg/dL"),
     *                 @OA\Property(property="na_k", type="string", example="140 mEq/L, 4 mEq/L"),
     *                 @OA\Property(property="chest_xray", type="string", example="Normal"),
     *                 @OA\Property(property="ecg", type="string", example="Normal"),
     *                 @OA\Property(property="echo", type="string", example="Normal"),
     *                 @OA\Property(property="other_investigation", type="string", example="None"),
     *                 @OA\Property(property="specific_anaesthesia_problem", type="string", example="No specific problems"),
     *                 @OA\Property(property="pre_operative_anaesthesia_instruction", type="string", example="NPO 6 hours before surgery"),
     *                 @OA\Property(property="summary", type="string", example="Pre-operative evaluation completed successfully"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-03-11 10:00:00"),
     *                 @OA\Property(property="upload_pdf_path", type="string", format="binary", description="PDF file to upload"),
     *                 @OA\Property(property="mouth_opening", type="string", example=">3cm"),
     *                 @OA\Property(property="teeth", type="string", example="Normal"),
     *                 @OA\Property(property="neck_movement", type="string", example="Good"),
     *                 @OA\Property(property="mallampati_score", type="string", example="Class 2"),
     *                 @OA\Property(property="dentures_check", type="string", example="No dentures"),
     *                 @OA\Property(property="tmd", type="string", example="Normal"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully pre-operative anaesthesia evaluation record added",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully received")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The ipd_id field is required."}),
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
    public function create(Request $request)
    {
        try {
            $this->evaluationService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_pre_operative_anaesthesia_evaluation_update/{id}",
     *     summary="Update IPD pre-operative anaesthesia evaluation record",
     *     tags={"IPD Pre-Operative Anaesthesia Evaluation"},
     *     description="Update IPD pre-operative anaesthesia evaluation details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by IPD Surgery ID for pre-operative anaesthesia evaluation record",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update pre-operative anaesthesia evaluation details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="ipd_anaesthesia_id", type="string", format="uuid", example="b123b456-7c89-0d12-34e5-678901232333"),
     *                 @OA\Property(property="previous_anaesthesia_surgery", type="string", example="No previous surgery"),
     *                 @OA\Property(property="current_medication", type="string", example="Aspirin 100mg daily"),
     *                 @OA\Property(property="allergies", type="string", example="Penicillin"),
     *                 @OA\Property(property="asa_grading", type="string", example="II"),
     *                 @OA\Property(property="airway_assessment", type="string", example="Normal"),
     *                 @OA\Property(property="respiratory_system", type="string", example="Normal"),
     *                 @OA\Property(property="cardio_vascular_system", type="string", example="Normal"),
     *                 @OA\Property(property="cns_musculoskeletal", type="string", example="Normal"),
     *                 @OA\Property(property="hepatic_renal", type="string", example="Normal"),
     *                 @OA\Property(property="endocrine", type="string", example="Normal"),
     *                 @OA\Property(property="other_system", type="string", example="Normal"),
     *                 @OA\Property(property="clinical_evaluation", type="string", example="Fit for surgery"),
     *                 @OA\Property(property="hb_hct", type="string", example="13.5 g/dL, 40%"),
     *                 @OA\Property(property="tc", type="string", example="7500/cumm"),
     *                 @OA\Property(property="platelets", type="string", example="250,000/cumm"),
     *                 @OA\Property(property="bt_ct", type="string", example="2 min, 5 min"),
     *                 @OA\Property(property="pt_ptt", type="string", example="12 sec, 30 sec"),
     *                 @OA\Property(property="inr", type="string", example="1.0"),
     *                 @OA\Property(property="blood_group", type="string", example="O+"),
     *                 @OA\Property(property="fbs_rbs", type="string", example="100 mg/dL, 120 mg/dL"),
     *                 @OA\Property(property="bun", type="string", example="14 mg/dL"),
     *                 @OA\Property(property="na_k", type="string", example="140 mEq/L, 4 mEq/L"),
     *                 @OA\Property(property="chest_xray", type="string", example="Normal"),
     *                 @OA\Property(property="ecg", type="string", example="Normal"),
     *                 @OA\Property(property="echo", type="string", example="Normal"),
     *                 @OA\Property(property="other_investigation", type="string", example="None"),
     *                 @OA\Property(property="specific_anaesthesia_problem", type="string", example="No specific problems"),
     *                 @OA\Property(property="pre_operative_anaesthesia_instruction", type="string", example="NPO 6 hours before surgery"),
     *                 @OA\Property(property="summary", type="string", example="Pre-operative evaluation completed successfully"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-03-11 10:00:00"),
     *                 @OA\Property(property="upload_pdf_path", type="string", format="binary", description="PDF file to upload"),
     *                 @OA\Property(property="mouth_opening", type="string", example=">3cm"),
     *                 @OA\Property(property="teeth", type="string", example="Normal"),
     *                 @OA\Property(property="neck_movement", type="string", example="Good"),
     *                 @OA\Property(property="mallampati_score", type="string", example="Class 2"),
     *                 @OA\Property(property="dentures_check", type="string", example="No dentures"),
     *                 @OA\Property(property="tmd", type="string", example="Normal"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful pre-operative anaesthesia evaluation update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pre-operative anaesthesia evaluation updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The ipd_id field is required."}),
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
            $this->evaluationService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Pre-operative anaesthesia evaluation record not found.');
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
     *     path="/api/ipd_pre_operative_anaesthesia_evaluation_delete/{id}",
     *     summary="Delete IPD pre-operative anaesthesia evaluation record",
     *     tags={"IPD Pre-Operative Anaesthesia Evaluation"},
     *     description="Deletes a pre-operative anaesthesia evaluation record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pre-operative anaesthesia evaluation record to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pre-operative anaesthesia evaluation record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Pre-operative anaesthesia evaluation record deleted successfully."
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
    public function delete(string $id)
    {
        try {
            $this->evaluationService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Pre-operative anaesthesia evaluation record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_pre_operative_anaesthesia_evaluation_list_by_ipd_anaesthesia/{ipd_anaesthesia_id}",
     *     summary="Get all pre-operative anaesthesia evaluation records by IPD Anaesthesia ID",
     *     tags={"IPD Pre-Operative Anaesthesia Evaluation"},
     *     description="Retrieve a list of all pre-operative anaesthesia evaluation records for a particular IPD Anaesthesia",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="ipd_anaesthesia_id",
     *          in="path",
     *          required=true,
     *          description="ID of the IPD Anaesthesia",
     *          @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901233333")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved pre-operative anaesthesia evaluation records for IPD Anaesthesia",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="IPD pre-operative anaesthesia evaluation list")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function getByIPDAnaesthesiaId(string $ipd_anaesthesia_id)
    {
        try {
            return $this->successResponse($this->evaluationService->getByIPDAnaesthesiaId($ipd_anaesthesia_id));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
