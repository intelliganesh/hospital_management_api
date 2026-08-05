<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use App\Services\MedicineCategoryMappingService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Medicine Category Mapping",
 *     description="API endpoints for managing medicine category mappings"
 * )
 */
class MedicineCategoryMappingController extends Controller
{

    use ResponseTrait;

    /**
     * Summary of medicineCategoryMappingService
     * @var 
     */
    private $medicineCategoryMappingService;

    /**
     * Summary of __construct
     * @param \App\Services\MedicineCategoryMappingService $medicineCategoryMappingService
     */
    public function __construct(MedicineCategoryMappingService $medicineCategoryMappingService)
    {
        $this->medicineCategoryMappingService = $medicineCategoryMappingService;
    }


    /**
     * @OA\Get(
     *     path="/api/medicine_category_mapping_list",
     *     summary="Get all medicine category mapping",
     *     description="Get all medicine category mapping",
     *     tags={"Medicine Category Mapping"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order, asc or desc",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine category mappings retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="medicine_id", type="integer", example=1),
     *                     @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                     @OA\Property(property="category_id", type="integer", example=1),
     *                     @OA\Property(property="category_name", type="string", example="Pain Relief"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="last_page_url", type="string"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->medicineCategoryMappingService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }

    }


    /**
     * @OA\Post(
     *     path="/api/medicine_category_mapping_add",
     *     summary="Create a new medicine category mapping",
     *     description="Create a new medicine category mapping",
     *     operationId="medicineCategoryMappingCreate",
     *     tags={"Medicine Category Mapping"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="medicine_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="category_id",
     *                 type="integer",
     *                 example=1
     *             )
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
            $this->medicineCategoryMappingService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/medicine_category_mapping_update",
     *     summary="Update a medicine category mapping",
     *     description="Update a medicine category mapping",
     *     operationId="medicineCategoryMappingUpdate",
     *     tags={"Medicine Category Mapping"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="medicine_id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="category_id",
     *                 type="integer",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine category mapping updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->medicineCategoryMappingService->update($request, $id);
            return $this->successResponse();
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }




    /**
     * @OA\Delete(
     *     path="/api/medicine_category_mapping_delete/{id}",
     *     summary="Delete a medicine category mapping",
     *     description="Delete a medicine category mapping",
     *     operationId="medicineCategoryMappingDelete",
     *     tags={"Medicine Category Mapping"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the medicine category mapping to delete",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine category mapping deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->medicineCategoryMappingService->delete($id);
            return $this->successResponse();
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/medicine_category_mapping_details/{id}",
     *     summary="Get a medicine category mapping by ID",
     *     description="Get a medicine category mapping by ID",
     *     tags={"Medicine Category Mapping"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the medicine category mapping to get",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine category mapping details retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="medicine_id", type="integer", example=1),
     *                 @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="category_name", type="string", example="Pain Relief"),
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
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->medicineCategoryMappingService->get($id));
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/medicine_category_mapping_all_list",
     *     summary="Get all medicine category mapping with medicine category and medicine list",
     *     description="Get all medicine category mapping with medicine category and medicine list",
     *     tags={"Medicine Category Mapping"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Medicine category mappings with categories and medicines retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="medicineCategory",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="category_name", type="string", example="Pain Relief")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="medicine",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="medicine_name", type="string", example="Paracetamol")
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
     *         description="Not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function getAllMedicineCategoryAndMedicineList()
    {
        try {
            return $this->successResponse($this->medicineCategoryMappingService->getAllMedicineCategoryAndMedicineList());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
