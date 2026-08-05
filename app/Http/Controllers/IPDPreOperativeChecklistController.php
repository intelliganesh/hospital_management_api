<?php
namespace App\Http\Controllers;

use App\Services\IPDPreOperativeChecklistService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Pre-Operative Checklist",
 *     description="API endpoints for managing IPD pre-operative checklist records"
 * )
 */
class IPDPreOperativeChecklistController extends Controller
{
    use ResponseTrait;
    private $checklistService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDPreOperativeChecklistService $checklistService
     */
    public function __construct(IPDPreOperativeChecklistService $checklistService)
    {
        $this->checklistService = $checklistService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_pre_operative_checklist_list",
     *     summary="Get all IPD pre-operative checklist records",
     *     description="Retrieve a list of all IPD pre-operative checklist records in the system",
     *     tags={"IPD Pre-Operative Checklist"},
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
     *          name="multiple_filter[datetime]",
     *          in="query",
     *          required=false,
     *          description="Filter by date (YYYY-MM-DD)",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2026-02-23"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of IPD pre-operative checklist records",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pre-operative checklist records retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="summary", type="string", example="All investigations completed"),
     *                     @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-23 10:00:00")
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
            return $this->successResponse($this->checklistService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_pre_operative_checklist_details/{id}",
     *     summary="Get complete IPD pre-operative checklist details",
     *     tags={"IPD Pre-Operative Checklist"},
     *     description="Get complete IPD pre-operative checklist details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the pre-operative checklist record to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful pre-operative checklist details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pre-operative checklist details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="q01_investigations", type="string", example="Yes"),
     *                 @OA\Property(property="q02_chest_xray_ecg", type="string", example="Yes"),
     *                 @OA\Property(property="q03_minor_age_parents", type="string", example="No"),
     *                 @OA\Property(property="q04a_blood_thinners", type="string", example="No"),
     *                 @OA\Property(property="q04b_blood_thinners_details", type="string", example="N/A"),
     *                 @OA\Property(property="summary", type="string", example="All investigations completed and normal"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-23 10:00:00")
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
            return $this->successResponse($this->checklistService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Pre-operative checklist record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_pre_operative_checklist_add",
     *     summary="Add IPD pre-operative checklist record",
     *     tags={"IPD Pre-Operative Checklist"},
     *     description="Add a new IPD pre-operative checklist record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new IPD pre-operative checklist record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id","summary","datetime"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="q01_investigations", type="string", example="Yes"),
     *                 @OA\Property(property="q02_chest_xray_ecg", type="string", example="Yes"),
     *                 @OA\Property(property="q03_minor_age_parents", type="string", example="No"),
     *                 @OA\Property(property="q04a_blood_thinners", type="string", example="No"),
     *                 @OA\Property(property="q04b_blood_thinners_details", type="string", example="N/A"),
     *                 @OA\Property(property="q05a_asthma", type="string", example="No"),
     *                 @OA\Property(property="q05b_asthma_treatment", type="string", example="N/A"),
     *                 @OA\Property(property="q06_medication_allergy", type="string", example="No"),
     *                 @OA\Property(property="q07_tooth_extraction", type="string", example="No"),
     *                 @OA\Property(property="q08_surgical_procedure", type="string", example="No"),
     *                 @OA\Property(property="q09a_diabetic", type="string", example="No"),
     *                 @OA\Property(property="q09b_blood_sugar", type="string", example="N/A"),
     *                 @OA\Property(property="q10_thyroid_medication", type="string", example="No"),
     *                 @OA\Property(property="q11a_hypertension", type="string", example="No"),
     *                 @OA\Property(property="q11b_hypertension_medicine", type="string", example="N/A"),
     *                 @OA\Property(property="q11c_hypertension_medication_taken", type="string", example="N/A"),
     *                 @OA\Property(property="q12_informed_consent", type="string", example="Yes"),
     *                 @OA\Property(property="q13_anesthesia_awareness", type="string", example="Yes"),
     *                 @OA\Property(property="q14_operative_procedure_awareness", type="string", example="Yes"),
     *                 @OA\Property(property="q15a_male_patient_age", type="string", example="No"),
     *                 @OA\Property(property="q15b_urinary_symptoms", type="string", example="N/A"),
     *                 @OA\Property(property="q16_urinary_obstruction", type="string", example="No"),
     *                 @OA\Property(property="q17_lithotomy_position", type="string", example="Yes"),
     *                 @OA\Property(property="q18_previous_surgery", type="string", example="No"),
     *                 @OA\Property(property="q19_community", type="string", example="No"),
     *                 @OA\Property(property="q20_previous_surgery_events", type="string", example="No"),
     *                 @OA\Property(property="q21_female_pregnant", type="string", example="No"),
     *                 @OA\Property(property="q22_epilepsy", type="string", example="No"),
     *                 @OA\Property(property="q23_antipsychotic", type="string", example="No"),
     *                 @OA\Property(property="q24_last_food_intake", type="string", example="2026-02-23 12:00 AM"),
     *                 @OA\Property(property="summary", type="string", example="All investigations completed and normal"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-23 10:00:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully pre-operative checklist record added",
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
            $this->checklistService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_pre_operative_checklist_update/{id}",
     *     summary="Update IPD pre-operative checklist record",
     *     tags={"IPD Pre-Operative Checklist"},
     *     description="Update IPD pre-operative checklist details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by Id for pre-operative checklist record",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update pre-operative checklist details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id","summary","datetime"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="q01_investigations", type="string", example="Yes"),
     *                 @OA\Property(property="q02_chest_xray_ecg", type="string", example="Yes"),
     *                 @OA\Property(property="q03_minor_age_parents", type="string", example="No"),
     *                 @OA\Property(property="q04a_blood_thinners", type="string", example="No"),
     *                 @OA\Property(property="q04b_blood_thinners_details", type="string", example="N/A"),
     *                 @OA\Property(property="q05a_asthma", type="string", example="No"),
     *                 @OA\Property(property="q05b_asthma_treatment", type="string", example="N/A"),
     *                 @OA\Property(property="q06_medication_allergy", type="string", example="No"),
     *                 @OA\Property(property="q07_tooth_extraction", type="string", example="No"),
     *                 @OA\Property(property="q08_surgical_procedure", type="string", example="No"),
     *                 @OA\Property(property="q09a_diabetic", type="string", example="No"),
     *                 @OA\Property(property="q09b_blood_sugar", type="string", example="N/A"),
     *                 @OA\Property(property="q10_thyroid_medication", type="string", example="No"),
     *                 @OA\Property(property="q11a_hypertension", type="string", example="No"),
     *                 @OA\Property(property="q11b_hypertension_medicine", type="string", example="N/A"),
     *                 @OA\Property(property="q11c_hypertension_medication_taken", type="string", example="N/A"),
     *                 @OA\Property(property="q12_informed_consent", type="string", example="Yes"),
     *                 @OA\Property(property="q13_anesthesia_awareness", type="string", example="Yes"),
     *                 @OA\Property(property="q14_operative_procedure_awareness", type="string", example="Yes"),
     *                 @OA\Property(property="q15a_male_patient_age", type="string", example="No"),
     *                 @OA\Property(property="q15b_urinary_symptoms", type="string", example="N/A"),
     *                 @OA\Property(property="q16_urinary_obstruction", type="string", example="No"),
     *                 @OA\Property(property="q17_lithotomy_position", type="string", example="Yes"),
     *                 @OA\Property(property="q18_previous_surgery", type="string", example="No"),
     *                 @OA\Property(property="q19_community", type="string", example="No"),
     *                 @OA\Property(property="q20_previous_surgery_events", type="string", example="No"),
     *                 @OA\Property(property="q21_female_pregnant", type="string", example="No"),
     *                 @OA\Property(property="q22_epilepsy", type="string", example="No"),
     *                 @OA\Property(property="q23_antipsychotic", type="string", example="No"),
     *                 @OA\Property(property="q24_last_food_intake", type="string", example="2026-02-23 12:00 AM"),
     *                 @OA\Property(property="summary", type="string", example="All investigations completed and normal"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-23 10:00:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful pre-operative checklist update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Pre-operative checklist updated successfully")
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
            $this->checklistService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Pre-operative checklist record not found.');
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
     *     path="/api/ipd_pre_operative_checklist_delete/{id}",
     *     summary="Delete IPD pre-operative checklist record",
     *     tags={"IPD Pre-Operative Checklist"},
     *     description="Deletes a pre-operative checklist record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the pre-operative checklist record to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pre-operative checklist record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Pre-operative checklist record deleted successfully."
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
            $this->checklistService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Pre-operative checklist record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
