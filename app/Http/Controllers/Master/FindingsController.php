<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\FindingsService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Findings",
 *     description="API endpoints for managing clinical findings, observations, and diagnostic results"
 * )
 */
class FindingsController extends Controller
{
    use ResponseTrait;

    private $findingsService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\FindingsService $findingsService
     */
    public function __construct(FindingsService $findingsService)
    {
        $this->findingsService = $findingsService;
    }

    /**
     * @OA\Get(
     *     path="/api/findings_list",
     *     tags={"Findings"},
     *     summary="Get list of findings",
     *     security={{"bearerAuth": {}}},
     *     description="Returns paginated list of findings with their details and search/sort options",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example="finding name"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example="finding_name"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Sort direction (asc or desc)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="asc"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Findings fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="findings_number", type="string", example="FIN0001"),
     *                         @OA\Property(property="finding_name", type="string", example="Finding Name"),
     *                         @OA\Property(property="category", type="string", example="Finding Category"),
     *                         @OA\Property(property="status", type="string", example="Active"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/findings_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/findings_list?page=5"),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://localhost/api/findings_list?page=1"),
     *                         @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost/api/findings_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/findings_list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
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
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Findings not found.")
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
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->findingsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/findings_details/{id}",
     *     tags={"Findings"},
     *     summary="Get finding details by ID",
     *     security={{"bearerAuth": {}}},
     *     description="Returns detailed information about a specific finding",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Finding ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Finding details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Finding details fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="findings_number", type="string", example="FIN0001"),
     *                 @OA\Property(property="finding_name", type="string", example="Finding Name"),
     *                 @OA\Property(property="category", type="string", example="Finding Category"),
     *                 @OA\Property(property="status", type="string", example="Active"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z")
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
     *         description="Finding not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Finding not found.")
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
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->findingsService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Findings data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/findings_add",
     *     summary="Create a new finding",
     *     tags={"Findings"},
     *     security={{"bearerAuth":{}}},
     *     description="Create a new finding with the provided data",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Finding data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"findings_number", "finding_name", "category", "status"},
     *             @OA\Property(property="findings_number", type="string", example="FIN0001", description="Unique finding number"),
     *             @OA\Property(property="finding_name", type="string", example="Finding Name", description="Name of the finding"),
     *             @OA\Property(property="finding_description", type="string", example="Finding Description", description="Description of the finding"),
     *             @OA\Property(property="category", type="string", example="Finding Category", description="Category of the finding"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active", description="Status of the finding")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Finding created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Finding created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="findings_number", type="string", example="FIN0001"),
     *                 @OA\Property(property="finding_name", type="string", example="Finding Name"),
     *                 @OA\Property(property="finding_description", type="string", example="Finding Description"),
     *                 @OA\Property(property="category", type="string", example="Finding Category"),
     *                 @OA\Property(property="status", type="string", example="Active"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="findings_number", type="array", @OA\Items(type="string", example="The findings number field is required.")),
     *                 @OA\Property(property="finding_name", type="array", @OA\Items(type="string", example="The finding name field is required."))
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
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $this->findingsService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/findings_update/{id}",
     *     summary="Update a finding",
     *     tags={"Findings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Finding id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"findings_number", "finding_name", "category", "status"},
     *             @OA\Property(property="findings_number", type="string", example="FIN0001"),
     *             @OA\Property(property="finding_name", type="string", example="Finding Name"),
     *             @OA\Property(property="finding_description", type="string", example="Finding Description"),
     *             @OA\Property(property="category", type="string", example="Finding Category"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Finding updated successfully"
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
            $this->findingsService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Findings data not found.');
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
     *     path="/api/findings_delete/{id}",
     *     summary="Delete a finding",
     *     tags={"Findings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Finding's ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Finding deleted successfully"
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
            $this->findingsService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Findings data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/findings_options_list",
     *     tags={"Findings"},
     *     summary="Get options list of findings",
     *     description="Returns a simplified list of findings for dropdown/select options",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Findings list fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="finding_name", type="string", example="Finding Name"),
     *                     @OA\Property(property="findings_number", type="string", example="FIN0001")
     *                 )
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
     *         description="Findings data not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Findings data not found.")
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
    public function findingsList()
    {
        try {
            return $this->successResponse($this->findingsService->findingsList());
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Findings data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}