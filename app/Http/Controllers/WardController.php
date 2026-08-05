<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\WardService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Wards",
 *     description="API endpoints for managing hospital ward information"
 * )
 */
class WardController extends Controller
{

    use ResponseTrait;

    private $wardService;


    public function __construct(WardService $wardService)
    {
        $this->wardService = $wardService;

    }

    /**
     * @OA\Get(
     *     path="/api/ward_list",
     *     tags={"Wards"},
     *     summary="Get all wards",
     *     security={{"bearerAuth": {}}},
     *     description="Returns a list of all hospital wards with optional filtering",
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by ward name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by ward type",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by ward status",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by (e.g., name, type, status)",
     *         @OA\Schema(
     *            type="string",
     *            example="name"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Sort direction (asc or desc)",
     *         @OA\Schema(
     *            type="string",
     *            enum={"asc", "desc"},
     *            example="asc"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wards retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="General Ward"),
     *                     @OA\Property(property="type", type="string", example="General"),
     *                     @OA\Property(property="floor", type="string", example="1st Floor"),
     *                     @OA\Property(property="status", type="string", example="Active"),
     *                     @OA\Property(property="ward_number", type="string", example="W-001"),
     *                     @OA\Property(property="description", type="string", example="General ward for regular patients")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->wardService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ward_details/{id}",
     *     tags={"Wards"},
     *     summary="Get ward by ID",
     *     security={{"bearerAuth": {}}},
     *     description="Returns detailed information about a specific ward",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ward ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ward retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="General Ward"),
     *                 @OA\Property(property="type", type="string", example="General"),
     *                 @OA\Property(property="floor", type="string", example="1st Floor"),
     *                 @OA\Property(property="status", type="string", example="Active"),
     *                 @OA\Property(property="ward_number", type="string", example="W-001"),
     *                 @OA\Property(property="description", type="string", example="General ward for regular patients")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ward not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ward data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->wardService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Ward data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ward_add",
     *     tags={"Wards"},
     *     summary="Create a new ward",
     *     security={{"bearerAuth": {}}},
     *     description="Creates a new hospital ward with the provided information",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type", "floor", "status"},
     *             @OA\Property(property="name", type="string", example="General Ward", description="Ward name"),
     *             @OA\Property(property="type", type="string", example="General", description="Ward type (ICU, General, Surgical, Oncology, Maternity, Pediatric, Neurology, Emergency, Cardiology, Orthopedic, Observation, Psychiatric)"),
     *             @OA\Property(property="floor", type="string", example="1st Floor", description="Floor number"),
     *             @OA\Property(property="status", type="string", example="Active", description="Ward status (Active, Inactive, Under Maintenance)"),
     *             @OA\Property(property="ward_number", type="string", example="W-001", description="Ward number"),
     *             @OA\Property(property="description", type="string", example="General ward for regular patients", description="Ward description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ward created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ward created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $this->wardService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/ward_update/{id}",
     *     tags={"Wards"},
     *     summary="Update a ward",
     *     security={{"bearerAuth": {}}},
     *     description="Updates an existing hospital ward with the provided information",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ward ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated General Ward", description="Ward name"),
     *             @OA\Property(property="type", type="string", example="General", description="Ward type (ICU, General, Surgical, Oncology, Maternity, Pediatric, Neurology, Emergency, Cardiology, Orthopedic, Observation, Psychiatric)"),
     *             @OA\Property(property="floor", type="string", example="2nd Floor", description="Floor number"),
     *             @OA\Property(property="status", type="string", example="Active", description="Ward status (Active, Inactive, Under Maintenance)"),
     *             @OA\Property(property="ward_number", type="string", example="W-002", description="Ward number"),
     *             @OA\Property(property="description", type="string", example="Updated general ward description", description="Ward description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ward updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ward updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ward not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ward data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->wardService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Ward data not found.');
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
     *     path="/api/ward_delete/{id}",
     *     tags={"Wards"},
     *     summary="Delete a ward",
     *     security={{"bearerAuth": {}}},
     *     description="Deletes a hospital ward by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Ward ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ward deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ward deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ward not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ward data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->wardService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Ward data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/wards_list_for_dropdown",
     *     tags={"Wards"},
     *     summary="Get wards for dropdown",
     *     security={{"bearerAuth": {}}},
     *     description="Returns a simplified list of wards suitable for dropdown/selection UI components",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Wards dropdown list retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="General Ward"),
     *                     @OA\Property(property="ward_number", type="string", example="W-001")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function wardsListForDropdown()
    {
        try {
            return $this->successResponse($this->wardService->listForDropdown());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}