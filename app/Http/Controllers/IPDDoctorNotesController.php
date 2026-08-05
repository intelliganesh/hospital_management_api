<?php

namespace App\Http\Controllers;

use App\Services\IPDDoctorNotesService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="IPD Doctor Notes",
 *     description="API endpoints for managing doctor notes records"
 * )
 */
class IPDDoctorNotesController extends Controller
{
    use ResponseTrait;
    private $doctorNotesService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDDoctorNotesService $doctorNotesService
     */
    public function __construct(IPDDoctorNotesService $doctorNotesService)
    {
        $this->doctorNotesService = $doctorNotesService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_doctor_notes_list",
     *     summary="Get all doctor notes",
     *     description="Retrieve a list of all doctor notes in the system",
     *     tags={"IPD Doctor Notes"},
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
     *          name="multiple_filter[datetime]",
     *          in="query",
     *          required=false,
     *          description="Filter by date (YYYY-MM-DD) - filters datetime column",
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2026-02-21"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[gc]",
     *          in="query",
     *          required=false,
     *          description="Filter by General Condition",
     *         @OA\Schema(
     *             type="string",
     *             example="Conscious / Oriented"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[bp]",
     *          in="query",
     *          required=false,
     *          description="Filter by Blood Pressure",
     *         @OA\Schema(
     *             type="string",
     *             example="120/80 mmHg"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="multiple_filter[pr]",
     *          in="query",
     *          required=false,
     *          description="Filter by Pulse Rate",
     *         @OA\Schema(
     *             type="string",
     *             example="72 bpm"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of doctor notes",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Doctor notes retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="doctor_id", type="integer", example=1),
     *                     @OA\Property(property="doctor_name", type="string", example="Dr. John Doe"),
     *                     @OA\Property(property="doctor_email", type="string", example="doctor@example.com"),
     *                         @OA\Property(property="name", type="string", example="Dr. John Doe"),
     *                         @OA\Property(property="email", type="string", example="doctor@example.com")
     *                     ),
     *                     @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-21 11:24:00"),
     *                     @OA\Property(property="gc", type="string", example="Conscious / Oriented"),
     *                     @OA\Property(property="bp", type="string", example="120/80 mmHg"),
     *                     @OA\Property(property="pr", type="string", example="72 bpm"),
     *                     @OA\Property(property="clinical_notes", type="string", example="Patient is stable and recovering well"),
     *                     @OA\Property(property="diagnosis", type="string", example="Hypertension")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=15),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=2),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="next", type="string", example="http://example.com/api/ipd_doctor_notes_list?page=2")
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
            return $this->successResponse($this->doctorNotesService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_doctor_notes_details/{id}",
     *     summary="Get complete doctor notes details",
     *     tags={"IPD Doctor Notes"},
     *     description="Get complete doctor notes details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the doctor notes to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful doctor notes details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Doctor notes details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="doctor", type="object",
     *                     @OA\Property(property="name", type="string", example="Dr. John Doe"),
     *                     @OA\Property(property="email", type="string", example="doctor@example.com")
     *                 ),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-21 11:24:00"),
     *                 @OA\Property(property="gc", type="string", example="Conscious / Oriented"),
     *                 @OA\Property(property="bp", type="string", example="120/80 mmHg"),
     *                 @OA\Property(property="pr", type="string", example="72 bpm"),
     *                 @OA\Property(property="clinical_notes", type="string", example="Patient is stable and recovering well"),
     *                 @OA\Property(property="diagnosis", type="string", example="Hypertension")
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
            return $this->successResponse($this->doctorNotesService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Doctor notes data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_doctor_notes_add",
     *     summary="Doctor notes add",
     *     tags={"IPD Doctor Notes"},
     *     description="Add a new doctor notes record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new doctor notes record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","doctor_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-21 11:24:00"),
     *                 @OA\Property(property="gc", type="string", example="Conscious / Oriented"),
     *                 @OA\Property(property="bp", type="string", example="120/80 mmHg"),
     *                 @OA\Property(property="pr", type="string", example="72 bpm"),
     *                 @OA\Property(property="clinical_notes", type="string", example="Patient is stable and recovering well"),
     *                 @OA\Property(property="diagnosis", type="string", example="Hypertension"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully doctor notes added",
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
            $this->doctorNotesService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_doctor_notes_update/{id}",
     *     summary="Update doctor notes",
     *     tags={"IPD Doctor Notes"},
     *     description="Update doctor notes details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by Id for doctor notes",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update doctor notes details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="datetime", type="string", format="date-time", example="2026-02-21 11:24:00"),
     *                 @OA\Property(property="gc", type="string", example="Conscious / Oriented"),
     *                 @OA\Property(property="bp", type="string", example="120/80 mmHg"),
     *                 @OA\Property(property="pr", type="string", example="72 bpm"),
     *                 @OA\Property(property="clinical_notes", type="string", example="Patient is stable and recovering well"),
     *                 @OA\Property(property="diagnosis", type="string", example="Hypertension"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful doctor notes update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Doctor notes updated successfully")
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
            $this->doctorNotesService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Doctor notes data not found.');
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
     *     path="/api/ipd_doctor_notes_delete/{id}",
     *     summary="Delete doctor notes",
     *     tags={"IPD Doctor Notes"},
     *     description="Deletes a doctor notes record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the doctor notes to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor notes successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Doctor notes deleted successfully."
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
            $this->doctorNotesService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Doctor notes data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
