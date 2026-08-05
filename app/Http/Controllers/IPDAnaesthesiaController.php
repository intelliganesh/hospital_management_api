<?php
namespace App\Http\Controllers;

use App\Services\IPDAnaesthesiaService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="IPD Anaesthesia",
 *     description="API endpoints for managing IPD anaesthesia records"
 * )
 */
class IPDAnaesthesiaController extends Controller
{
    use ResponseTrait;
    private $anaesthesiaService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDAnaesthesiaService $anaesthesiaService
     */
    public function __construct(IPDAnaesthesiaService $anaesthesiaService)
    {
        $this->anaesthesiaService = $anaesthesiaService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_list",
     *     summary="Get all IPD anaesthesia records",
     *     description="Retrieve a list of all IPD anaesthesia records in the system",
     *     tags={"IPD Anaesthesia"},
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
     *          name="multiple_filter[ipd_surgery_id]",
     *          in="query",
     *          required=false,
     *          description="Filter by Surgery ID",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of IPD anaesthesia records",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD anaesthesia records retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                     @OA\Property(property="diagnosis", type="string", example="Appendicitis"),
     *                     @OA\Property(property="position", type="string", example="Supine"),
     *                     @OA\Property(property="anaesthetist_assistant", type="string", example="Dr. Smith"),
     *                     @OA\Property(property="type_of_anaesthesia", type="string", example="General"),
     *                     @OA\Property(property="uploaded_consent_path", type="string", example="/path/to/consent.pdf"),
     *                     @OA\Property(property="consent_summary", type="string", example="Patient consented for general anaesthesia"),
     *                     @OA\Property(property="upload_anaesthesia_record_path", type="string", example="/path/to/record.pdf"),
     *                     @OA\Property(property="anaesthesia_record_summary", type="string", example="Anaesthesia record summary details"),
     *                     @OA\Property(property="datetime", type="string", format="date-time", example="2026-04-28 14:30:00"),
     *                     @OA\Property(property="patient_height", type="number", format="float", example=170.5),
     *                     @OA\Property(property="patient_weight", type="number", format="float", example=75.5),
     *                     @OA\Property(property="patient_community", type="string", example="General"),
     *                     @OA\Property(property="patient_mother_tongue", type="string", example="English")
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
            return $this->successResponse($this->anaesthesiaService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_details/{id}",
     *     summary="Get IPD anaesthesia record details",
     *     tags={"IPD Anaesthesia"},
     *     description="Get complete IPD anaesthesia record details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the IPD anaesthesia record to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful IPD anaesthesia record details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD anaesthesia details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="diagnosis", type="string", example="Appendicitis"),
     *                 @OA\Property(property="position", type="string", example="Supine"),
     *                 @OA\Property(property="anaesthetist_assistant", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="type_of_anaesthesia", type="string", example="General"),
     *                 @OA\Property(property="uploaded_consent_path", type="string", example="/path/to/consent.pdf"),
     *                 @OA\Property(property="consent_summary", type="string", example="Patient consented for general anaesthesia"),
     *                 @OA\Property(property="upload_anaesthesia_record_path", type="string", example="/path/to/record.pdf"),
     *                 @OA\Property(property="anaesthesia_record_summary", type="string", example="Anaesthesia record summary details"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-04-28 14:30:00"),
     *                 @OA\Property(property="patient_height", type="number", format="float", example=170.5),
     *                 @OA\Property(property="patient_weight", type="number", format="float", example=75.5),
     *                 @OA\Property(property="patient_community", type="string", example="General"),
     *                 @OA\Property(property="patient_mother_tongue", type="string", example="English"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2026-04-15 10:00:00")
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
            return $this->successResponse($this->anaesthesiaService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD anaesthesia record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_anaesthesia_add",
     *     summary="Add IPD anaesthesia record",
     *     tags={"IPD Anaesthesia"},
     *     description="Add a new IPD anaesthesia record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new IPD anaesthesia record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="diagnosis", type="string", example="Appendicitis"),
     *                 @OA\Property(property="position", type="string", example="Supine"),
     *                 @OA\Property(property="anaesthetist_assistant", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="type_of_anaesthesia", type="string", example="General"),
     *                 @OA\Property(property="uploaded_consent_path", type="string", example="/path/to/consent.pdf"),
     *                 @OA\Property(property="consent_summary", type="string", example="Patient consented for general anaesthesia"),
     *                 @OA\Property(property="upload_anaesthesia_record_path", type="string", example="/path/to/record.pdf"),
     *                 @OA\Property(property="anaesthesia_record_summary", type="string", example="Anaesthesia record summary details"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-04-28 14:30:00"),
     *                 @OA\Property(property="patient_height", type="number", format="float", example=170.5),
     *                 @OA\Property(property="patient_weight", type="number", format="float", example=75.5),
     *                 @OA\Property(property="patient_community", type="string", example="General"),
     *                 @OA\Property(property="patient_mother_tongue", type="string", example="English"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully IPD anaesthesia record added",
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
            $this->anaesthesiaService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_anaesthesia_update/{id}",
     *     summary="Update IPD anaesthesia record",
     *     tags={"IPD Anaesthesia"},
     *     description="Update IPD anaesthesia record details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the IPD anaesthesia record",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update IPD anaesthesia record details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
     *                 @OA\Property(property="diagnosis", type="string", example="Appendicitis"),
     *                 @OA\Property(property="position", type="string", example="Supine"),
     *                 @OA\Property(property="anaesthetist_assistant", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="type_of_anaesthesia", type="string", example="General"),
     *                 @OA\Property(property="uploaded_consent_path", type="string", example="/path/to/consent.pdf"),
     *                 @OA\Property(property="consent_summary", type="string", example="Patient consented for general anaesthesia"),
     *                 @OA\Property(property="upload_anaesthesia_record_path", type="string", example="/path/to/record.pdf"),
     *                 @OA\Property(property="anaesthesia_record_summary", type="string", example="Anaesthesia record summary details"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-04-28 14:30:00"),
     *                 @OA\Property(property="patient_height", type="number", format="float", example=170.5),
     *                 @OA\Property(property="patient_weight", type="number", format="float", example=75.5),
     *                 @OA\Property(property="patient_community", type="string", example="General"),
     *                 @OA\Property(property="patient_mother_tongue", type="string", example="English"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful IPD anaesthesia record update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="IPD anaesthesia record updated successfully")
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
            $this->anaesthesiaService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD anaesthesia record not found.');
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
     *     path="/api/ipd_anaesthesia_delete/{id}",
     *     summary="Delete IPD anaesthesia record",
     *     tags={"IPD Anaesthesia"},
     *     description="Deletes an IPD anaesthesia record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the IPD anaesthesia record to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD anaesthesia record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="IPD anaesthesia record deleted successfully."
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
            $this->anaesthesiaService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD anaesthesia record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
