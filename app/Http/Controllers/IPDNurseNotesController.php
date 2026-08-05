<?php

namespace App\Http\Controllers;

use App\Services\IPDNurseNotesService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="IPD Nurse Notes",
 *     description="API endpoints for managing nurse notes records"
 * )
 */
class IPDNurseNotesController extends Controller
{
    use ResponseTrait;
    private $nurseNotesService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDNurseNotesService $nurseNotesService
     */
    public function __construct(IPDNurseNotesService $nurseNotesService)
    {
        $this->nurseNotesService = $nurseNotesService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_nurse_notes_list",
     *     summary="Get all nurse notes",
     *     description="Retrieve a list of all nurse notes in the system",
     *     tags={"IPD Nurse Notes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="ipd_id",
     *          in="query",
     *          required=false,
     *          description="Filter by IPD ID",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="a123b456-7c89-0d12-34e5-678901234567"
     *         )
     *      ),
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
     *          name="multiple_filter[bp]",
     *          in="query",
     *          required=false,
     *          description="Filter by BP",
     *         @OA\Schema(
     *             type="string",
     *             example="120/80"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[spo2]",
     *          in="query",
     *          required=false,
     *          description="Filter by SPO2",
     *         @OA\Schema(
     *             type="string",
     *             example="98"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[temperature]",
     *          in="query",
     *          required=false,
     *          description="Filter by Temperature",
     *         @OA\Schema(
     *             type="string",
     *             example="98.6"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[pulse]",
     *          in="query",
     *          required=false,
     *          description="Filter by Pulse",
     *         @OA\Schema(
     *             type="string",
     *             example="72"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[datetime]",
     *          in="query",
     *          required=false,
     *          description="Filter by date (YYYY-MM-DD) - filters datetime column",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2026-02-17"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of nurse notes",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Nurse notes retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="nurse_id", type="integer", example=1),
     *                     @OA\Property(property="nurse", type="object",
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john.doe@example.com")
     *                     ),
     *                     @OA\Property(property="bp", type="string", example="120/80"),
     *                     @OA\Property(property="spo2", type="string", example="98"),
     *                     @OA\Property(property="temperature", type="string", example="98.6"),
     *                     @OA\Property(property="pulse", type="string", example="72"),
     *                     @OA\Property(property="remark1", type="string", example="Patient is stable"),
     *                     @OA\Property(property="remark2", type="string", example="Continue monitoring"),
     *                     @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-17 10:30:00")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=15),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=2),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="next", type="string", example="http://example.com/api/ipd_nurse_notes_list?page=2")
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
            return $this->successResponse($this->nurseNotesService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_nurse_notes_details/{id}",
     *     summary="Get complete nurse notes details",
     *     tags={"IPD Nurse Notes"},
     *     description="Get complete nurse notes details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the nurse notes to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful nurse notes details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Nurse notes details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="nurse_id", type="integer", example=1),
     *                 @OA\Property(property="nurse", type="object",
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com")
     *                 ),
     *                 @OA\Property(property="bp", type="string", example="120/80"),
     *                 @OA\Property(property="spo2", type="string", example="98"),
     *                 @OA\Property(property="temperature", type="string", example="98.6"),
     *                 @OA\Property(property="pulse", type="string", example="72"),
     *                 @OA\Property(property="remark1", type="string", example="Patient is stable"),
     *                 @OA\Property(property="remark2", type="string", example="Continue monitoring"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-17 10:30:00")
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
            return $this->successResponse($this->nurseNotesService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Nurse notes data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_nurse_notes_add",
     *     summary="IPD Nurse notes add",
     *     tags={"IPD Nurse Notes"},
     *     description="Add a new nurse notes record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new nurse notes record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","nurse_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="nurse_id", type="integer", example=1),
     *                 @OA\Property(property="bp", type="string", example="120/80"),
     *                 @OA\Property(property="spo2", type="string", example="98"),
     *                 @OA\Property(property="temperature", type="string", example="98.6"),
     *                 @OA\Property(property="pulse", type="string", example="72"),
     *                 @OA\Property(property="remark1", type="string", example="Patient is stable"),
     *                 @OA\Property(property="remark2", type="string", example="Continue monitoring"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-17 10:30:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully nurse notes added",
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
            $this->nurseNotesService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_nurse_notes_update/{id}",
     *     summary="Update nurse notes",
     *     tags={"IPD Nurse Notes"},
     *     description="Update nurse notes details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by Id for nurse notes",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update nurse notes details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="nurse_id", type="integer", example=1),
     *                 @OA\Property(property="bp", type="string", example="120/80"),
     *                 @OA\Property(property="spo2", type="string", example="98"),
     *                 @OA\Property(property="temperature", type="string", example="98.6"),
     *                 @OA\Property(property="pulse", type="string", example="72"),
     *                 @OA\Property(property="remark1", type="string", example="Patient is stable"),
     *                 @OA\Property(property="remark2", type="string", example="Continue monitoring"),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-17 10:30:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful nurse notes update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Nurse notes updated successfully")
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
            $this->nurseNotesService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Nurse notes data not found.');
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
     *     path="/api/ipd_nurse_notes_delete/{id}",
     *     summary="Delete nurse notes",
     *     tags={"IPD Nurse Notes"},
     *     description="Deletes a nurse notes record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the nurse notes to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Nurse notes successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Nurse notes deleted successfully."
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
            $this->nurseNotesService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Nurse notes data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
