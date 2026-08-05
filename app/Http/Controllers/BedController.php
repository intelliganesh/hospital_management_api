<?php

namespace App\Http\Controllers;

use Exception;
use App\Services\BedService;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Master",
 *     description="API endpoints for managing hospital beds, including creation, updates, and retrieval"
 * )
 */
class BedController extends Controller
{
    use ResponseTrait;

    protected $container;
    protected $bedService;

    /**
     * Summary of __construct
     * @param \App\Services\BedService $bedService
     */
    public function __construct(BedService $bedService)
    {
        $this->bedService = $bedService;
    }


    /**
     * @OA\Get(
     *     path="/api/bed_list",
     *     summary="Get all bed",
     *     description="Retrieve a list of all bed in the system",
     *     tags={"Bed"},
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
     *          description="Field to sort by field name",
     *         @OA\Schema(
     *             type="string",
     *             example=""
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
     *         description="A list of bed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Beds retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", example="b1a3c5e7-8a9b-4c8d-9e0f-1d2e3f4a5c6b"),
     *                     @OA\Property(property="room_id", type="string", format="uuid", example="b1a3c5e7-8a9b-4c8d-9e0f-1d2e3f4a5c6b"),
     *                     @OA\Property(property="bed_type", type="string", enum={"Single","Double"}, example="Single"),
     *                     @OA\Property(property="size", type="string", enum={"Twin","Queen","King"}, example="Twin"),
     *                     @OA\Property(property="description", type="string", example="Description")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/roles_list?page=1"),
     *                  @OA\Property(property="from", type="integer", example=1),
     *                  @OA\Property(property="last_page", type="integer", example=3),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost/api/roles_list?page=3"),
     *                  @OA\Property(property="next_page_url", type="string", example="http://localhost/api/roles_list?page=2"),
     *                  @OA\Property(property="path", type="string", example="http://localhost/api/roles_list"),
     *                  @OA\Property(property="per_page", type="integer", example=10),
     *                  @OA\Property(property="prev_page_url", type="string", example=null),
     *                  @OA\Property(property="to", type="integer", example=10),
     *                  @OA\Property(property="total", type="integer", example=25)
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
            return $this->successResponse($this->bedService->all($request));
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/bed_details/{id}",
     *     summary="Get a bed",
     *     tags={"Bed"},
     *     security={{"bearerAuth": {}}},
     *     description="Get a bed by id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Bed's id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bed successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="description", type="string", example="Description"),
     *                 @OA\Property(property="bed_type", type="string", enum={"Single","Double"}, example="Single"),
     *                 @OA\Property(property="size", type="string", enum={"Twin","Queen","King"}, example="Twin")
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
            return $this->successResponse($this->bedService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Bed data not found.');
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/bed_add",
     *     summary="Create a bed",
     *     tags={"Bed"},
     *     security={{"bearerAuth": {}}},
     *     description="Create a new bed",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"bed_number", "status", "bed_type"},
     *             @OA\Property(property="room_id", type="integer", example=1, description="Room ID (optional)"),
     *             @OA\Property(property="bed_number", type="string", example="B-001", description="Bed number (required, unique)"),
     *             @OA\Property(property="status", type="string", enum={"Occupied", "Available", "Under Cleaning"}, example="Available", description="Bed status (required)"),
     *             @OA\Property(property="bed_type", type="string", enum={"Single", "Double", "Triple"}, example="Single", description="Bed type (required)"),
     *             @OA\Property(property="description", type="string", example="Description", description="Description (optional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bed successfully created.")
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
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            return $this->successResponse($this->bedService->create($request));
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }


    /**
     *
     * @OA\Put(
     *     path="/api/bed_update/{id}",
     *     summary="Update a Bed",
     *     tags={"Bed"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         description="bed id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="bed_type", type="string", enum={"Single","Double"}, example="Single"),
     *             @OA\Property(property="size", type="string", enum={"Twin","Queen","King"}, example="Twin"),
     *             @OA\Property(property="description", type="string", example="Description"),
     *             @OA\Property(property="status", type="string", enum={"Occupied", "Available", "Under Cleaning"}, example="Available", description="Bed status (required)"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bed successfully updated."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="room_id",
     *                     type="string",
     *                     format="uuid",
     *                     example="b1a3c5e7-8a9b-4c8d-9e0f-1d2e3f4a5c6b"
     *                 ),
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="bed_type", type="string", enum={"Single","Double"}, example="Single"),
     *                 @OA\Property(property="size", type="string", enum={"Twin","Queen","King"}, example="Twin"),
     *                 @OA\Property(property="description", type="string", example="Description"),
     *                 @OA\Property(property="status", type="string", enum={"Occupied", "Available", "Under Cleaning"}, example="Available", description="Bed status (required)"),         
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
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
            return $this->successResponse($this->bedService->update($request, $id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Bed data not found.');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($notFound);        
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/beds_delete/{id}",
     *     summary="Delete a bed",
     *     tags={"Bed"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Bed's ID",
     *         @OA\Schema(type="string", format="integer", example="1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bed deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bed deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
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
            $this->bedService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Bed data not found.');
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/beds_list_for_dropdown/{room_id}",
     *     summary="Show bed list",
     *     tags={"Bed"},
     *     description="Retrieve a list of beds with selected fields for dropdown menus",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="room_id",
     *         in="path",
     *         description="Filter beds by room ID.Set To 0 if all data has to be returned",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of beds",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Beds list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="bed_number", type="string", example="B-001")
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
    public function bedsListForDropdown($room_id)
    {
        try {
            $roomId = $room_id;
            return $this->successResponse($this->bedService->getBedsList(['id', 'bed_number'], $roomId));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Bed data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

}
