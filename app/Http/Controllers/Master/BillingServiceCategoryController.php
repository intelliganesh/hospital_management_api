<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Services\Master\BillingServiceCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Billing Service Category",
 *     description="API endpoints for managing billing service categories"
 * )
 */
class BillingServiceCategoryController extends Controller
{
    use ResponseTrait;

    private $billingServiceCategoryService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\BillingServiceCategoryService $billingServiceCategoryService
     */
    public function __construct(BillingServiceCategoryService $billingServiceCategoryService)
    {
        $this->billingServiceCategoryService = $billingServiceCategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/billing_service_category_list",
     *     tags={"Billing Service Category"},
     *     summary="Get list of billing service categories",
     *     security={{"bearerAuth": {}}},
     *     description="Returns paginated list of billing service categories with search, sort, and filter options",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword for category name or status",
     *         @OA\Schema(type="string", example="Room")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(type="string", example="category_name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Sort direction",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Billing service categories fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Success."),
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
     *                         @OA\Property(property="category_name", type="string", example="Room Charges"),
     *                         @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active")
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer", example=25),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->billingServiceCategoryService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billing_service_category_details/{id}",
     *     tags={"Billing Service Category"},
     *     summary="Get billing service category details by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Billing service category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Billing service category details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Success."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_name", type="string", example="Room Charges"),
     *                 @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active"),
     *                 @OA\Property(property="name", type="string", example="Room Charges")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->billingServiceCategoryService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/billing_service_category_add",
     *     tags={"Billing Service Category"},
     *     summary="Create a billing service category",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Billing service category data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"category_name", "status"},
     *             @OA\Property(property="category_name", type="string", maxLength=100, example="Room Charges"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Billing service category created successfully"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="category_name", type="array", @OA\Items(type="string", example="The category name field is required.")),
     *                 @OA\Property(property="status", type="array", @OA\Items(type="string", example="The selected status is invalid."))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function create(Request $request)
    {
        try {
            $this->billingServiceCategoryService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/billing_service_category_update/{id}",
     *     tags={"Billing Service Category"},
     *     summary="Update a billing service category",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Billing service category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Billing service category data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"category_name", "status"},
     *             @OA\Property(property="category_name", type="string", maxLength=100, example="Room Charges"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Inactive")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Billing service category updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->billingServiceCategoryService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
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
     *     path="/api/billing_service_category_delete/{id}",
     *     tags={"Billing Service Category"},
     *     summary="Delete a billing service category",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Billing service category ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Billing service category deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->billingServiceCategoryService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/billing_service_category_dropdown_list",
     *     tags={"Billing Service Category"},
     *     summary="Get billing service category dropdown list",
     *     security={{"bearerAuth": {}}},
     *     description="Returns active billing service categories for dropdown/select options",
     *     @OA\Response(
     *         response=200,
     *         description="Billing service category dropdown list fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Success."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="category_name", type="string", example="Room Charges")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function billingServiceCategoryList()
    {
        try {
            return $this->successResponse($this->billingServiceCategoryService->billingServiceCategoryList());
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Billing service category data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
