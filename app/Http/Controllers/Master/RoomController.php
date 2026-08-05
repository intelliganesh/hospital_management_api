<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use OpenApi\Annotations as OA;
use App\Services\Master\RoomService;
use App\Http\Controllers\Controller;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Rooms",
 *     description="API endpoints for managing hospital rooms and wards"
 * )
 */
class RoomController extends Controller
{
    use ResponseTrait;

    protected $roomService;

    /**
     * Summary of __construct
     */
    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * @OA\Get(
     *     path="/api/room_list",
     *     summary="Get all rooms",
     *     description="Retrieve a paginated list of all rooms in the system with optional filtering and sorting",
     *     tags={"Rooms"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword to filter rooms by name, room_type, floor, status",
     *         @OA\Schema(
     *            type="string",
     *            example="Deluxe"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by (e.g., name, room_type, floor, status)",
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
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(
     *            type="integer",
     *            example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of rooms",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Rooms list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Room 101"),
     *                         @OA\Property(property="room_number", type="string", example="R-101"),
     *                         @OA\Property(property="room_type", type="string", example="Deluxe"),
     *                         @OA\Property(property="floor", type="string", example="1st"),
     *                         @OA\Property(property="status", type="string", example="Available"),
     *                         @OA\Property(property="ward_id", type="integer", example=1),
     *                         @OA\Property(property="bed_count", type="integer", example=2),
     *                         @OA\Property(property="description", type="string", example="Updated room description", description="Room description")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/room_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/room_list?page=3"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost/api/room_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/room_list"),
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
            return $this->successResponse($this->roomService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/room_add",
     *     summary="Create a new room",
     *     tags={"Rooms"},
     *     description="Add a new room with the provided information",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "room_type", "status", "ward_id", "bed_count"},
     *             @OA\Property(property="name", type="string", example="Room 101", description="Room name (required, max 100)"),
     *             @OA\Property(property="room_type", type="string", example="Deluxe", description="Room type (required, max 100)"),
     *             @OA\Property(property="floor", type="string", example="1st Floor", description="Floor number (optional, max 10)"),
     *             @OA\Property(property="status", type="string", example="Available", description="Room status (required, max 100)"),
     *             @OA\Property(property="ward_id", type="integer", example=1, description="Ward ID (required, must exist in ward table)"),
     *             @OA\Property(property="room_number", type="string", example="R-101", description="Room number (optional, max 20, unique)"),
     *             @OA\Property(property="bed_count", type="integer", example=2, description="Number of beds (required)"),
     *             @OA\Property(property="description", type="string", example="Updated room description", description="Room description")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Room created successfully")
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
            $proxiedService = ServiceInterceptor::intercept($this->roomService);
            $proxiedService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/room_update/{id}",
     *     summary="Room update",
     *     tags={"Rooms"},
     *     description="Update room details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Room ID to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update room details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={"name","room_type","status","ward_id","bed_count"},
     *                 @OA\Property(property="name", type="string", example="Room 101", description="Room name (required, max 100)"),
     *                 @OA\Property(property="room_number", type="string", example="R-101", description="Room number (optional, max 20, unique)"),
     *                 @OA\Property(property="room_type", type="string", example="Deluxe", description="Room type (required, max 100)"),
     *                 @OA\Property(property="floor", type="string", example="1st", description="Floor number (optional)"),
     *                 @OA\Property(property="status", type="string", example="Available", description="Room status (required, max 100)"),
     *                 @OA\Property(property="ward_id", type="integer", example=1, description="Ward ID (required, must exist in ward table)"),
     *                 @OA\Property(property="bed_count", type="integer", example=2, description="Number of beds (required)"),
     *                 @OA\Property(property="description", type="string", example="Updated room description", description="Room description")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful room update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Room updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="array", @OA\Items(type="string"), example={"The name field is required."}),
     *             @OA\Property(property="room_type", type="array", @OA\Items(type="string"), example={"The room_type field is required."}),
     *             @OA\Property(property="status", type="array", @OA\Items(type="string"), example={"The status field is required."}),
     *             @OA\Property(property="ward_id", type="array", @OA\Items(type="string"), example={"The ward_id field is required."}),
     *             @OA\Property(property="bed_count", type="array", @OA\Items(type="string"), example={"The bed_count field is required."}),
     *             @OA\Property(property="description", type="array", @OA\Items(type="string"), example={"The description field is required."})
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

    /**
     * @OA\Get(
     *     path="/api/room_details/{id}",
     *     tags={"Rooms"},
     *     summary="Get room details",
     *     description="Get complete room details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the room to get room details",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful room details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Rooms details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Room 101"),
     *                 @OA\Property(property="room_number", type="string", example="R-101"),
     *                 @OA\Property(property="room_type", type="string", example="Deluxe"),
     *                 @OA\Property(property="floor", type="string", example="1st"),
     *                 @OA\Property(property="status", type="string", example="Available"),
     *                 @OA\Property(property="ward_id", type="integer", example=1),
     *                 @OA\Property(property="bed_count", type="integer", example=2),
     *                 @OA\Property(property="description", type="string", example="Updated room description", description="Room description")
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
            return $this->successResponse($this->roomService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Room data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->roomService);
            $proxiedService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Room data not found.');
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
     *     path="/api/room_delete/{id}",
     *     summary="Delete a room",
     *     tags={"Rooms"},
     *     description="Deletes a room by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the room to be deleted",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Room deleted successfully."
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
            $this->roomService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Room data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/rooms_list_for_dropdown/{ward_id}",
     *     summary="Show room list",
     *     tags={"Rooms"},
     *     description="Retrieve a list of rooms with selected fields for dropdown menus",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="ward_id",
     *         in="path",
     *         description="Filter rooms by ward ID.Set  to 0 if all data has to be returned",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of rooms",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Rooms list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Room 101")
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
    public function roomsListForDropdown($ward_id)
    {
        try {
            $wardId = $ward_id;
            return $this->successResponse($this->roomService->getRoomsList(['id', 'name'], $wardId));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Room data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}