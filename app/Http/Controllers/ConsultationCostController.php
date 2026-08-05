<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ConsultationCostService;


/**
 * @OA\Tag(
 *     name="Consultation Cost",
 *     description="API endpoints for managing consultation costs"
 * )
 */
class ConsultationCostController extends Controller
{

    use ResponseTrait;

    private $consultationCostService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(ConsultationCostService $consultationCostService)
    {
        $this->consultationCostService = $consultationCostService;

    }

    /**
     * @OA\Get(
     *     path="/api/consultation_cost_list",
     *     summary="Get all consultationCosts",
     *     description="Retrieve a list of all consultationCosts in the system",
     *     tags={"Consultation Cost"},
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
     *         description="A list of consultationCosts",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation costs retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="amount", type="number", format="float", example=200.34),
     *                     @OA\Property(property="status", type="string", enum={"Active","Inactive"}, example="Active")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=15),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=2),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="next", type="string", example="http://example.com/api/consultation_cost_list?page=2")
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
            return $this->successResponse($this->consultationCostService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/consultation_cost_details/{id}",
     *     summary="Get complete consultationCost details",
     *     tags={"Consultation Cost"},
     *     description="Get complete consultationCost details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the consultationCost to get consultationCost details",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful consultationCost details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="consultationCosts details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                   @OA\Property(property="amount", type="decimal", description="200.34"),
     *                   @OA\Property(property="status", enum={"Active","Inactive"}, description="Active, Inactive"),
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
            return $this->successResponse($this->consultationCostService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ConsultationCost data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/consultation_cost_add",
     *     summary="Add consultation cost",
     *     tags={"Consultation Cost"},
     *     description="Add a new consultation cost record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add consultation cost details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"amount", "status"},
     *                 @OA\Property(property="amount", type="number", format="float", example=500),
     *                 @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully added consultation cost",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully received")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             required={"amount","status"},
     *             @OA\Property(
     *                 property="amount",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The name field is required."}
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The name field is required."}
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
            $this->consultationCostService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/consultation_cost_update/{id}",
     *     summary="Update consultation cost",
     *     tags={"Consultation Cost"},
     *     description="Update consultation cost details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the consultation cost to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Consultation cost update data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"amount", "status"},
     *                 @OA\Property(property="amount", type="number", format="float", example=750),
     *                 @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful consultation cost update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation cost updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             required={"amount","status"},
     *             @OA\Property(
     *                 property="amount",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The name field is required."}
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"The name field is required."}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->consultationCostService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ConsultationCost data not found.');
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
     *     path="/api/consultation_cost_delete/{id}",
     *     summary="Delete a consultationCost",
     *     tags={"Consultation Cost"},
     *     description="Deletes a consultationCost by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the consultationCost to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="consultationCost successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="consultationCost deleted successfully."
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
            $this->consultationCostService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ConsultationCost data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/consultation_cost_dropdown_list/{departmentValue}",
     *     summary="Get list of consultation costs by department",
     *     tags={"Consultation Cost"},
     *     description="Get list of consultation costs filtered by department",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="departmentValue",
     *         in="path",
     *         required=false,
     *         description="Department value to filter consultation costs",
     *         @OA\Schema(
     *             type="string",
     *             example="Proctology"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of consultation costs filtered by department",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation costs retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="amount", type="number", format="float", example=200.34),
     *                 @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active"),
     *                 @OA\Property(property="department", type="string", example="Proctology")
     *             ))
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
    public function consultationCostList(?string $departmentType = null)
    {
        try {
            return $this->successResponse($this->consultationCostService->consultationCostList($departmentType));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('ConsultationCost data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}