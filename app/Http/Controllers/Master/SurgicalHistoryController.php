<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Services\Master\SurgicalHistoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Surgical History",
 *     description="API endpoints for managing patient surgical history records"
 * )
 */
class SurgicalHistoryController extends Controller
{

    use ResponseTrait;

    private $surgicalHistoryService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\SurgicalHistoryService $surgicalHistoryService
     */
    public function __construct(SurgicalHistoryService $surgicalHistoryService)
    {
        $this->surgicalHistoryService = $surgicalHistoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/surgical_history_list",
     *     summary="Get all surgical histories",
     *     description="Retrieve a paginated list of all surgical histories in the system with optional filtering and sorting",
     *     tags={"Surgical History"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword to filter by surgery name",
     *         @OA\Schema(
     *             type="string",
     *             example="Appendectomy"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (e.g., surgery_name, is_active)",
     *         @OA\Schema(
     *             type="string",
     *             example="surgery_name"
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
     *          description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of surgical histories",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgical history list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                         @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                         @OA\Property(property="description", type="string", example="Surgical removal of the appendix"),
     *                         @OA\Property(property="is_active", enum={"Active","Inactive"}, example="Active"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/surgical_history_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/surgical_history_list?page=3"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost/api/surgical_history_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/surgical_history_list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25)
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
            return $this->successResponse($this->surgicalHistoryService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/surgical_history_details/{id}",
     *     summary="Get complete surgical history details",
     *     tags={"Surgical History"},
     *     description="Get detailed information about a specific surgical history record by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the surgical history record to retrieve",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful surgical history details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgical history details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                  @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                  @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                  @OA\Property(property="description", type="string", example="Surgical removal of the appendix"),
     *                  @OA\Property(property="is_active", enum={"Active","Inactive"}, example="Active"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
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
            return $this->successResponse($this->surgicalHistoryService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('SurgicalHistory data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/surgical_history_add",
     *     summary="Add new surgical history record",
     *     tags={"Surgical History"},
     *     description="Create a new surgical history record in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Surgical history information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"surgery_name","is_active"},
     *                 @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                 @OA\Property(property="description", type="string", example="Surgical removal of the appendix"),
     *                 @OA\Property(property="is_active", enum={"Active","Inactive"}, example="Active"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Surgical history record created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgical history added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="surgery_name",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The surgery_name field is required."}
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The is_active field is required."}
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The description is optional."}
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
            $this->surgicalHistoryService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/surgical_history_update/{id}",
     *     summary="Update surgical history record",
     *     tags={"Surgical History"},
     *     description="Update an existing surgical history record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the surgical history record to update",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="d290f1ee-6c54-4b01-90e6-d701748f0851"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated surgical history information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"surgery_name","is_active"},
     *                 @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                 @OA\Property(property="is_active", enum={"Active","Inactive"}, example="Active"),
     *                 @OA\Property(property="description", type="string", example="Surgical removal of the appendix")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Surgical history record updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgical history updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="surgery_name",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The surgery_name field is required."}
     *             ),
     *             @OA\Property(
     *                 property="is_active",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The is_active field is required."}
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The description is optional."}
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

    public function update(Request $request, string $id)
    {
        try {
            $this->surgicalHistoryService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('SurgicalHistory data not found.');
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
     *     path="/api/surgical_history_delete/{id}",
     *     summary="Delete a surgical history record",
     *     tags={"Surgical History"},
     *     description="Permanently removes a surgical history record from the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the surgical history record to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Surgical history record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Surgical history deleted successfully."
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
            $this->surgicalHistoryService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('SurgicalHistory data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/surgical_history_dropdown_list/{departmentValue}",
     *     summary="Get surgical history records for dropdown list by department",
     *     tags={"Surgical History"},
     *     description="Retrieve a filtered list of surgical history records by department for use in dropdown menus",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="departmentValue",
     *         in="path",
     *         required=false,
     *         description="Department value to filter surgical history records (e.g., Proctology, Gynecology)",
     *         @OA\Schema(
     *             type="string",
     *             example="Proctology"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of surgical history records for dropdown selection",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Surgical history list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="surgery_name", type="string", example="Appendectomy"),
     *                     @OA\Property(property="is_active", enum={"Active","Inactive"}, example="Active"),
     *                     @OA\Property(property="department", type="string", example="Proctology")
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
    public function surgicalHistoryList(?string $departmentValue = null)
    {
        try {
            return $this->successResponse($this->surgicalHistoryService->surgicalHistoryList($departmentValue));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('SurgicalHistory data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}