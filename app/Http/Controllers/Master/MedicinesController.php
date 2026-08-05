<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Services\Master\MedicinesService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Medicines",
 *     description="API endpoints for managing hospital medicines and pharmaceuticals"
 * )
 */
class MedicinesController extends Controller
{

    use ResponseTrait;

    private $medicinesService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\MedicinesService $medicinesService
     */
    public function __construct(MedicinesService $medicinesService)
    {
        $this->medicinesService = $medicinesService;
    }

    /**
     * @OA\Get(
     *     path="/api/medicines_list",
     *     tags={"Medicines"},
     *     summary="Get list of medicines",
     *     description="Returns paginated list of medicines with their details and search/sort options",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of medicines with their details",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example="paracetamol"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example="medicine_name"
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
     *             @OA\Property(property="message", type="string", example="Medicines fetched successfully."),
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
     *                         @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                         @OA\Property(property="generic_name", type="string", example="Acetaminophen"),
     *                         @OA\Property(property="dosage_form", type="string", example="Tablet"),
     *                         @OA\Property(property="manufacturer", type="string", example="Cipla"),
     *                         @OA\Property(property="expiry_date", type="string", format="date", example="2025-01-01"),
     *                         @OA\Property(property="strength", type="string", example="500mg"),
     *                         @OA\Property(property="stock_quantity", type="integer", example=100),
     *                         @OA\Property(property="unit_price", type="number", format="float", example=10.50),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/medicines_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/medicines_list?page=5"),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true, example="http://localhost/api/medicines_list?page=1"),
     *                         @OA\Property(property="label", type="string", example="&laquo; Previous"),
     *                         @OA\Property(property="active", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost/api/medicines_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/medicines_list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
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
    public function all(?Request $response)
    {
        try {
            return $this->successResponse($this->medicinesService->all($response));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/medicines_details/{id}",
     *     tags={"Medicines"},
     *     summary="Get details of a specific medicine",
     *     security={{"bearerAuth": {}}},
     *     description="Returns detailed information about a specific medicine by ID",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the medicine to get details",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine details fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                 @OA\Property(property="generic_name", type="string", example="Acetaminophen"),
     *                 @OA\Property(property="dosage_form", type="string", example="Tablet"),
     *                 @OA\Property(property="manufacturer", type="string", example="Cipla"),
     *                 @OA\Property(property="expiry_date", type="string", format="date", example="2025-01-01"),
     *                 @OA\Property(property="strength", type="string", example="500"),
     *                 @OA\Property(property="strength_unit", type="string", example="mg"),
     *                 @OA\Property(property="stock_quantity", type="integer", example=100),
     *                 @OA\Property(property="unit_price", type="number", format="float", example=10.50),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z")
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
            return $this->successResponse($this->medicinesService->get($id));
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/medicines_add",
     *     summary="Create a new medicine",
     *     tags={"Medicines"},
     *     security={{"bearerAuth":{}}},
     *     description="Creates a new medicine record with the provided details",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Medicine information",
     *         @OA\JsonContent(
     *             required={"medicine_name", "dosage_form", "manufacturer", "expiry_date", "strength", "strength_unit", "stock_quantity", "unit_price", "is_active"},
     *             @OA\Property(property="medicine_name", type="string", example="Paracetamol", description="Name of the medicine"),
     *             @OA\Property(property="dosage_form", type="string", example="Tablet", description="Form of the medicine (Tablet, Capsule, Syrup, Injection, Other)"),
     *             @OA\Property(property="manufacturer", type="string", example="Cipla", description="Manufacturer name"),
     *             @OA\Property(property="generic_name", type="string", example="Acetaminophen", description="Generic name of medicine"),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2025-11-01", description="Expiry date in YYYY-MM-DD format"),
     *             @OA\Property(property="strength", type="string", example="500", description="Strength value of the medicine"),
     *             @OA\Property(property="strength_unit", type="string", example="mg", description="Unit of strength measurement"),
     *             @OA\Property(property="stock_quantity", type="integer", example=100, description="Available quantity in stock"),
     *             @OA\Property(property="unit_price", type="number", format="float", example=10.50, description="Price per unit"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="Whether the medicine is active"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Medicine created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-02T10:00:00.000000Z")
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
            $this->medicinesService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->errorResponse(
                $ve->validator->errors(),
                'Validation error',
                422
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/medicines_update/{id}",
     *     summary="Update an existing medicine",
     *     tags={"Medicines"},
     *     security={{"bearerAuth":{}}},
     *     description="Updates an existing medicine record with the provided details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Medicine ID to update",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated medicine information",
     *         @OA\JsonContent(
     *             required={"medicine_name", "dosage_form", "manufacturer", "expiry_date", "strength", "strength_unit", "stock_quantity", "unit_price", "is_active"},
     *             @OA\Property(property="medicine_name", type="string", example="Paracetamol", description="Name of the medicine"),
     *             @OA\Property(property="dosage_form", type="string", example="Tablet", description="Form of the medicine (Tablet, Capsule, Syrup, Injection, Other)"),
     *             @OA\Property(property="manufacturer", type="string", example="Cipla", description="Manufacturer name"),
     *             @OA\Property(property="generic_name", type="string", example="Acetaminophen", description="Generic name of medicine"),
     *             @OA\Property(property="expiry_date", type="string", format="date", example="2025-11-01", description="Expiry date in YYYY-MM-DD format"),
     *             @OA\Property(property="strength", type="string", example="500", description="Strength value of the medicine"),
     *             @OA\Property(property="strength_unit", type="string", example="mg", description="Unit of strength measurement"),
     *             @OA\Property(property="stock_quantity", type="integer", example=100, description="Available quantity in stock"),
     *             @OA\Property(property="unit_price", type="number", format="float", example=10.50, description="Price per unit"),
     *             @OA\Property(property="is_active", type="boolean", example=true, description="Whether the medicine is active"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medicine updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-02T10:30:00.000000Z")
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
            $this->medicinesService->update($request, $id);
            return $this->successResponse();
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
     *     path="/api/medicines_delete/{id}",
     *     summary="Delete medicine by ID",
     *     tags={"Medicines"},
     *     security={{"bearerAuth":{}}},
     *     description="Deletes a medicine record by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Medicine ID to delete",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medicine deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine deleted successfully.")
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
            $this->medicinesService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Medicine data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/medicine_list",
     *     summary="Get simplified list of medicines",
     *     tags={"Medicines"},
     *     security={{"bearerAuth":{}}},
     *     description="Returns a simplified list of medicines with ID and name only, suitable for dropdowns",
     *     @OA\Response(
     *         response=200,
     *         description="Medicines list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicines list fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="medicine_name", type="string", example="Paracetamol")
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
    public function getMedicineList()
    {
        try {
            return $this->successResponse($this->medicinesService->getMedicineList(['id', 'medicine_name']));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/medicines_list_for_dropdown",
     *     summary="Get list of medicines with pricing for dropdown",
     *     tags={"Medicines"},
     *     security={{"bearerAuth":{}}},
     *     description="Returns a list of medicines with ID, name, and unit price for dropdown selection",
     *     @OA\RequestBody(
     *         required=false,
     *         description="Optional filter parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="field_name", type="string", example="medicine_name", description="Field name to filter by")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medicines list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicines list fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                     @OA\Property(property="unit_price", type="string", example="Rs 100")
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
    public function medicinesList(Request $fieldname)
    {
        try {
            return $this->successResponse($this->medicinesService->medicinesList($fieldname));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
