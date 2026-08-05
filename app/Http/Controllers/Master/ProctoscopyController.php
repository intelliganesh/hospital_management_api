<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\ProctoscopyService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @OA\Tag(
 *     name="Proctoscopy",
 *     description="API endpoints for managing proctoscopy examination data in the hospital system"
 * )
 */
class ProctoscopyController extends Controller
{
    use ResponseTrait;

    private $proctoscopyService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(ProctoscopyService $proctoscopyService)
    {
        $this->proctoscopyService = $proctoscopyService;

    }

    /**
     * @OA\Get(
     *     path="/api/proctoscopy_list",
     *     summary="Get all proctoscopies",
     *     description="Retrieve a paginated list of all proctoscopies in the system with optional filtering and sorting",
     *     tags={"Proctoscopy"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword to filter results",
     *          @OA\Schema(
     *             type="string",
     *             example="anal"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (e.g., name, created_at)",
     *          @OA\Schema(
     *             type="string",
     *             example="created_at"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort direction (asc or desc)",
     *          @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="desc"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Number of results per page",
     *          @OA\Schema(
     *             type="integer",
     *             example=10
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="Page number",
     *          @OA\Schema(
     *             type="integer",
     *             example=1
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved list of proctoscopies",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Proctoscopy list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Anal Fissure"),
     *                         @OA\Property(property="description", type="string", example="Tear in the lining of the anal canal"),
     *                         @OA\Property(property="status", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/proctoscopy_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/proctoscopy_list?page=5"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost/api/proctoscopy_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/proctoscopy_list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
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
            return $this->successResponse($this->proctoscopyService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/proctoscopy_details/{id}",
     *     summary="Get complete proctoscopy details",
     *     tags={"Proctoscopy"},
     *     description="Retrieve detailed information about a specific proctoscopy by its ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Unique identifier of the proctoscopy record",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful proctoscopy details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Proctoscopy details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Anal Fissure"),
     *                 @OA\Property(property="description", type="string", example="Tear in the lining of the anal canal"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
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
            return $this->successResponse($this->proctoscopyService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Proctoscopy data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/proctoscopy_add",
     *     summary="Add new proctoscopy",
     *     tags={"Proctoscopy"},
     *     description="Create a new proctoscopy record in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Proctoscopy information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="Anal Fissure", description="Name of the proctoscopy condition"),
     *                 @OA\Property(property="description", type="string", example="Tear in the lining of the anal canal", description="Detailed description of the condition"),
     *                 @OA\Property(property="status", type="integer", example=1, description="Status of the record (1=active, 0=inactive)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proctoscopy successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Proctoscopy added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Anal Fissure"),
     *                 @OA\Property(property="description", type="string", example="Tear in the lining of the anal canal"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
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

    public function create(Request $request)
    {
        try {
            $this->proctoscopyService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/proctoscopy_update/{id}",
     *     summary="Update proctoscopy record",
     *     tags={"Proctoscopy"},
     *     description="Update an existing proctoscopy record with new information",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Unique identifier of the proctoscopy record to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated proctoscopy information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 @OA\Property(property="name", type="string", example="Anal Fissure (Updated)", description="Updated name of the proctoscopy condition"),
     *                 @OA\Property(property="description", type="string", example="Updated description of the tear in the lining of the anal canal", description="Updated detailed description of the condition"),
     *                 @OA\Property(property="status", type="integer", example=1, description="Updated status of the record (1=active, 0=inactive)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proctoscopy successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Proctoscopy updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Anal Fissure (Updated)"),
     *                 @OA\Property(property="description", type="string", example="Updated description of the tear in the lining of the anal canal"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string"), example={"The name field must be a string."}),
     *                 @OA\Property(property="status", type="array", @OA\Items(type="string"), example={"The status must be 0 or 1."})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Proctoscopy not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Proctoscopy data not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        try {
            $this->proctoscopyService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Proctoscopy data not found.');
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
     *     path="/api/proctoscopy_delete/{id}",
     *     summary="Delete a proctoscopy record",
     *     tags={"Proctoscopy"},
     *     description="Permanently removes a proctoscopy record from the system by its unique identifier",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Unique identifier of the proctoscopy record to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proctoscopy successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Proctoscopy deleted successfully."
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
            $this->proctoscopyService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Proctoscopy data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/proctoscopy_dropdown_list/{department_value?}",
     *     summary="Get proctoscopy options list",
     *     tags={"Proctoscopy"},
     *     description="Retrieve a list of proctoscopy options, optionally filtered by department",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="department_value",
     *         in="path",
     *         required=false,
     *         description="Optional department value to filter the proctoscopy options",
     *         @OA\Schema(type="string", example="gastroenterology")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved proctoscopy options list",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Proctoscopy options list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Anal Fissure"),
     *                     @OA\Property(property="description", type="string", example="Tear in the lining of the anal canal"),
     *                     @OA\Property(property="status", type="integer", example=1)
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
    public function proctoscopyList(?string $departmentValue = null)
    {
        try {
            return $this->successResponse($this->proctoscopyService->proctoscopyList($departmentValue));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Proctoscopy data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}