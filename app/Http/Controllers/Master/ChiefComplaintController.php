<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\ChiefComplaintService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @OA\Tag(
 *     name="Chief Complaint",
 *     description="API endpoints for managing patient chief complaints and symptoms"
 * )
 */
class ChiefComplaintController extends Controller
{

    use ResponseTrait;

    private $chiefComplaintService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(ChiefComplaintService $chiefComplaintService)
    {
        $this->chiefComplaintService = $chiefComplaintService;

    }

    /**
     * @OA\Get(
     *     path="/api/chief_complaint_list",
     *     summary="Get all chiefComplaints",
     *     description="Retrieve a list of all chiefComplaints in the system",
     *     tags={"Chief Complaint"},
     *     security={{"bearerAuth": {}}},
     *       @OA\Parameter(
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
     *     @OA\Response(
     *         response=200,
     *         description="A list of chiefComplaints",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Chief complaints list fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="complaint_name", type="string", example="Headache"),
     *                     @OA\Property(property="department", type="string", example="General")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string", example="http://example.com/api/chief_complaint_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://example.com/api/chief_complaint_list?page=1"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string", example="http://example.com/api/chief_complaint_list"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=10)
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
            return $this->successResponse($this->chiefComplaintService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/chief_complaint_details/{id}",
     *     summary="Get complete chiefComplaint details",
     *     tags={"Chief Complaint"},
     *     description="Get complete chiefComplaint details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the chiefComplaint to get chiefComplaint details",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful chiefComplaint details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Chief complaint details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="complaint_name", type="string", example="Headache"),
     *                 @OA\Property(property="department", type="string", example="General"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
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
            return $this->successResponse($this->chiefComplaintService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ChiefComplaint data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/chief_complaint_add",
     *     summary="chiefComplaint add",
     *     tags={"Chief Complaint"},
     *     description="Add a new chiefComplaint details",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new chief complaint details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={"complaint_name", "department"},
     *                 @OA\Property(property="complaint_name", type="string", example="Headache"),
     *                 @OA\Property(property="department", type="string", example="General"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chief complaint created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Chief complaint created successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The name field is required."}),
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
            $this->chiefComplaintService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/chief_complaint_update/{id}",
     *     summary="Update chiefComplaint",
     *     tags={"Chief Complaint"},
     *     description="Update chiefComplaint details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by Id for chiefComplaint",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update chiefComplaint details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={},
     *                 @OA\Property(property="example", type="string", example="Some example value"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful chiefComplaint update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="chiefComplaint updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The name field is required."}),
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
            $this->chiefComplaintService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ChiefComplaint data not found.');
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
     *     path="/api/chief_complaint_delete/{id}",
     *     summary="Delete a chiefComplaint",
     *     tags={"Chief Complaint"},
     *     description="Deletes a chiefComplaint by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the chiefComplaint to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="chiefComplaint successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="chiefComplaint deleted successfully."
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
            $this->chiefComplaintService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ChiefComplaint data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/chief_complaint_dropdown_list/{departmentValue}",
     *     summary="Get all chiefComplaints by department",
     *     tags={"Chief Complaint"},
     *     description="Retrieve a list of all chiefComplaints by department in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="departmentValue",
     *         in="path",
     *         required=false,
     *         description="Department value to filter chief complaints",
     *         @OA\Schema(
     *             type="string",
     *             example="Proctology"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of chiefComplaints filtered by department",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="complaint_name", type="string", example="Chief complaint value"),
     *                 @OA\Property(property="department", type="string", example="Proctology")
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
    public function chiefComplaintList(?string $departmentType = null)
    {
        try {
            return $this->successResponse($this->chiefComplaintService->chiefComplaintList($departmentType));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ChiefComplaint data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}