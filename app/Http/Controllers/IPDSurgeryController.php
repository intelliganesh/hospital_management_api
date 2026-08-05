<?php

namespace App\Http\Controllers;

use App\Services\IPDSurgeryService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="IPD Surgery",
 *     description="API endpoints for managing IPD surgery records"
 * )
 */
class IPDSurgeryController extends Controller
{
    use ResponseTrait;
    private $surgeryService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDSurgeryService $surgeryService
     */
    public function __construct(IPDSurgeryService $surgeryService)
    {
        $this->surgeryService = $surgeryService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_surgery_list",
     *     summary="Get all IPD surgery records",
     *     description="Retrieve a list of all IPD surgery records in the system",
     *     tags={"IPD Surgery"},
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
     *     @OA\Response(
     *         response=200,
     *         description="A list of IPD surgery records",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgery records retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                     @OA\Property(property="surgery_type", type="string", example="General Surgery"),
     *                     @OA\Property(property="surgery_date", type="string", format="date", example="2026-02-23"),
     *                     @OA\Property(property="status", type="string", example="Completed"),
     *                     @OA\Property(property="surgeon", type="string", example="Dr. Smith"),
     *                     @OA\Property(property="anaesthetist", type="string", example="Dr. Jones"),
     *                     @OA\Property(property="department", type="string", example="General Surgery"),
     *                     @OA\Property(property="surgery_start_datetime", type="string", format="date-time", example="2026-02-23 09:00:00"),
     *                     @OA\Property(property="surgery_end_datetime", type="string", format="date-time", example="2026-02-23 10:30:00")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=15),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=2),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="next", type="string", example="http://example.com/api/ipd_surgery_list?page=2")
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
            return $this->successResponse($this->surgeryService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_surgery_details/{id}",
     *     summary="Get complete IPD surgery details",
     *     tags={"IPD Surgery"},
     *     description="Get complete IPD surgery details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the surgery record to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful surgery details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgery details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                 @OA\Property(property="surgery_type", type="string", example="General Surgery"),
     *                 @OA\Property(property="surgery_date", type="string", format="date", example="2026-02-23"),
     *                 @OA\Property(property="status", type="string", example="Completed"),
     *                 @OA\Property(property="surgeon", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="anaesthetist", type="string", example="Dr. Jones"),
     *                 @OA\Property(property="department", type="string", example="General Surgery"),
     *                 @OA\Property(property="surgery_start_datetime", type="string", format="date-time", example="2026-02-23 09:00:00"),
     *                 @OA\Property(property="surgery_end_datetime", type="string", format="date-time", example="2026-02-23 10:30:00"),
     *                 @OA\Property(property="assistant_surgeon", type="string", example="Dr. Brown"),
     *                 @OA\Property(property="scrub_nurse", type="string", example="Nurse Williams"),
     *                 @OA\Property(property="specimen_for_hpe", type="string", example="Appendix"),
     *                 @OA\Property(property="operative_notes", type="string", example="Surgery performed successfully"),
     *                 @OA\Property(property="operative_findings", type="string", example="Inflamed appendix"),
     *                 @OA\Property(property="post_operative_instructions", type="string", example="Rest for 2 weeks"),
     *                 @OA\Property(property="summary", type="string", example="Successful appendectomy")
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
            return $this->successResponse($this->surgeryService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Surgery record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_surgery_add",
     *     summary="Add IPD surgery record",
     *     tags={"IPD Surgery"},
     *     description="Add a new IPD surgery record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new IPD surgery record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","surgery_name","surgery_type","surgery_date"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                 @OA\Property(property="surgery_type", type="string", example="General Surgery"),
     *                 @OA\Property(property="surgery_date", type="string", format="date", example="2026-02-23"),
     *                 @OA\Property(property="status", type="string", example="Completed"),
     *                 @OA\Property(property="surgeon", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="anaesthetist", type="string", example="Dr. Jones"),
     *                 @OA\Property(property="department", type="string", example="General Surgery"),
     *                 @OA\Property(property="surgery_start_datetime", type="string", format="date-time", example="2026-02-23 09:00:00"),
     *                 @OA\Property(property="surgery_end_datetime", type="string", format="date-time", example="2026-02-23 10:30:00"),
     *                 @OA\Property(property="assistant_surgeon", type="string", example="Dr. Brown"),
     *                 @OA\Property(property="scrub_nurse", type="string", example="Nurse Williams"),
     *                 @OA\Property(property="specimen_for_hpe", type="string", example="Appendix"),
     *                 @OA\Property(property="operative_notes", type="string", example="Surgery performed successfully"),
     *                 @OA\Property(property="operative_findings", type="string", example="Inflamed appendix"),
     *                 @OA\Property(property="post_operative_instructions", type="string", example="Rest for 2 weeks"),
     *                 @OA\Property(property="summary", type="string", example="Successful appendectomy"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully surgery record added",
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
            $this->surgeryService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_surgery_update/{id}",
     *     summary="Update IPD surgery record",
     *     tags={"IPD Surgery"},
     *     description="Update IPD surgery details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by Id for surgery record",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update surgery details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                 @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                 @OA\Property(property="surgery_type", type="string", example="General Surgery"),
     *                 @OA\Property(property="surgery_date", type="string", format="date", example="2026-02-23"),
     *                 @OA\Property(property="status", type="string", example="Completed"),
     *                 @OA\Property(property="surgeon", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="anaesthetist", type="string", example="Dr. Jones"),
     *                 @OA\Property(property="department", type="string", example="General Surgery"),
     *                 @OA\Property(property="surgery_start_datetime", type="string", format="date-time", example="2026-02-23 09:00:00"),
     *                 @OA\Property(property="surgery_end_datetime", type="string", format="date-time", example="2026-02-23 10:30:00"),
     *                 @OA\Property(property="assistant_surgeon", type="string", example="Dr. Brown"),
     *                 @OA\Property(property="scrub_nurse", type="string", example="Nurse Williams"),
     *                 @OA\Property(property="specimen_for_hpe", type="string", example="Appendix"),
     *                 @OA\Property(property="operative_notes", type="string", example="Surgery performed successfully"),
     *                 @OA\Property(property="operative_findings", type="string", example="Inflamed appendix"),
     *                 @OA\Property(property="post_operative_instructions", type="string", example="Rest for 2 weeks"),
     *                 @OA\Property(property="summary", type="string", example="Successful appendectomy"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful surgery update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgery updated successfully")
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
            $this->surgeryService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Surgery record not found.');
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
     *     path="/api/ipd_surgery_delete/{id}",
     *     summary="Delete IPD surgery record",
     *     tags={"IPD Surgery"},
     *     description="Deletes a surgery record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the surgery record to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Surgery record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Surgery record deleted successfully."
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
            $this->surgeryService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Surgery record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_surgery_consent_form_update/{id}",
     *     summary="Update IPD surgery consent form",
     *     tags={"IPD Surgery"},
     *     description="Update the consent form of an existing IPD surgery record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update the consent form of an existing IPD surgery record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="consent_summary", type="string", example="Successful appendectomy"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated surgery consent form",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully updated consent form")
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
    public function updateConsentForm(Request $request, string $id)
    {
        try {
            $this->surgeryService->updateConsentDetails($request, $id);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        }
    }
     /**
     * @OA\Get(
     *     path="/api/ipd_surgery_list_by_ipd/{ipd_id}",
     *     summary="Get all IPD surgery records by IPD ID",
     *     tags={"IPD Surgery"},
     *     description="Retrieve a list of all IPD surgery records for a particular IPD",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="ipd_id",
     *          in="path",
     *          required=true,
     *          description="ID of the IPD",
     *          @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved surgery list for IPD",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
     *                     @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                     @OA\Property(property="surgery_type", type="string", example="General Surgery"),
     *                     @OA\Property(property="surgery_date", type="string", format="date", example="2026-02-23"),
     *                     @OA\Property(property="status", type="string", example="Completed"),
     *                     @OA\Property(property="surgeon", type="string", example="Dr. Smith"),
     *                     @OA\Property(property="anaesthetist", type="string", example="Dr. Jones"),
     *                     @OA\Property(property="department", type="string", example="General Surgery"),
     *                     @OA\Property(property="surgery_start_datetime", type="string", format="date-time", example="2026-02-23 09:00:00"),
     *                     @OA\Property(property="surgery_end_datetime", type="string", format="date-time", example="2026-02-23 10:30:00")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="IPD surgery list"
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
    public function getByIPDId(string $ipd_id)
    {
        try {
            return $this->successResponse($this->surgeryService->getByIPDId($ipd_id));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
