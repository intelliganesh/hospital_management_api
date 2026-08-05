<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\Master\ServiceCostService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @OA\Tag(
 *     name="Service Cost",
 *     description="Service Cost operations"
 * )
 */
class ServiceCostController extends Controller
{

    use ResponseTrait;

    private $serviceCostService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(ServiceCostService $serviceCostService)
    {
        $this->serviceCostService = $serviceCostService;

    }

    /**
     * @OA\Get(
     *     path="/api/service_cost_list",
     *     summary="Get all service costs",
     *     description="Retrieve a list of all service costs in the system",
     *     tags={"Service Cost"},
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
     *          description="Field to sort by",
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
     *     @OA\Response(
     *         response=200,
     *         description="Service costs list successfully fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service costs list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="Service id"),
     *                     @OA\Property(property="cost", type="number", format="float", example=250.00, description="Service cost"),
     *                     @OA\Property(property="service_name", type="string", example="Consultation", description="Service name"),
     *                     @OA\Property(property="description", type="string", example="General consultation service", description="Service description"),
     *                     @OA\Property(property="status", type="string", example="Active", description="Active or Inactive"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-09T12:34:56Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-09T12:34:56Z")
     *                 )
     *             )
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
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->serviceCostService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/service_cost_details/{id}",
     *     summary="Get service cost details",
     *     tags={"Service Cost"},
     *     description="Get detailed information about a specific service cost",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the service cost",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service cost details successfully fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service cost details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="Service id"),
     *                 @OA\Property(property="cost", type="number", format="float", example=250.00, description="Service cost"),
     *                 @OA\Property(property="service_name", type="string", example="Consultation", description="Service name"),
     *                 @OA\Property(property="description", type="string", example="General consultation service", description="Service description"),
     *                 @OA\Property(property="status", type="string", example="Active", description="Active or Inactive"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-09T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-09T12:34:56Z")
     *             )
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

    public function get(string $id)
    {
        try {
            return $this->successResponse($this->serviceCostService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ServiceCost data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/service_cost_add",
     *     summary="Create a new service cost",
     *     tags={"Service Cost"},
     *     description="Add a new service cost record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object", 
     *             required={"cost", "service_name", "status"},
     *             @OA\Property(property="cost", type="number", format="float", example=250.00),
     *             @OA\Property(property="service_name", type="string", example="Consultation"),
     *             @OA\Property(property="description", type="string", example="General consultation service"),
     *             @OA\Property(property="status", type="string", example="Active", enum={"Active", "Inactive"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service cost created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service cost created successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"field_name": {"The field is required."}},
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
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

    public function create(Request $request)
    {
        try {
            $this->serviceCostService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/service_cost_update/{id}",
     *     summary="Update a service cost",
     *     tags={"Service Cost"},
     *     description="Update an existing service cost record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the service cost to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object", 
     *             required={"cost", "service_name", "status"},
     *             @OA\Property(property="cost", type="number", format="float", example=250.00),
     *             @OA\Property(property="service_name", type="string", example="Consultation"),
     *             @OA\Property(property="description", type="string", example="General consultation service"),
     *             @OA\Property(property="status", type="string", example="Active", enum={"Active", "Inactive"}),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service cost updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service cost updated successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"field_name": {"The field is required."}},
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
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

    public function update(Request $request, string $id)
    {
        try {
            $this->serviceCostService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ServiceCost data not found.');
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
     *     path="/api/service_cost_delete/{id}",
     *     summary="Delete a service cost",
     *     tags={"Service Cost"},
     *     description="Delete a service cost record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service cost to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service cost deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service cost deleted successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
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
            $this->serviceCostService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ServiceCost data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/service_cost_dropdown_list/{departmentValue}",
     *     summary="Get service costs for dropdown",
     *     description="Retrieve a list of service costs for dropdown selection",
     *     tags={"Service Cost"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="departmentValue",
     *         in="path",
     *         required=true,
     *         description="Department value to filter service costs",
     *         @OA\Schema(type="string", nullable=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service costs list successfully fetched",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service costs dropdown list retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="Service id"),
     *                     @OA\Property(property="service_name", type="string", example="Consultation", description="Service name")
     *                 )
     *             )
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
    public function serviceCostList(string $departmentValue = null)
    {
        try {
            return $this->successResponse($this->serviceCostService->serviceCostList($departmentValue));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ServiceCost data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}