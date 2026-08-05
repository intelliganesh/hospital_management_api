<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use OpenApi\Annotations as OA;
use App\Services\BankInformationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @OA\Tag(
 *     name="Bank Information",
 *     description="API endpoints for managing bank information"
 * )
 */
class BankInformationController extends Controller
{
    use ResponseTrait;

    protected $bankInformationService;

    /**
     * Summary of __construct
     */
    public function __construct(BankInformationService $bankInformationService)
    {
        $this->bankInformationService = $bankInformationService;
    }

    /**
     * @OA\Get(
     *     path="/api/bank_information_list",
     *     summary="Get all bank information",
     *     description="Retrieve a paginated list of all bank information with optional filtering and sorting",
     *     tags={"Bank Information"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword to filter by title",
     *         @OA\Schema(
     *            type="string",
     *            example="Account Details"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by (id, title)",
     *         @OA\Schema(
     *            type="string",
     *            example="title"
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
     *         description="A paginated list of bank information",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bank information list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Account A"),
     *                         @OA\Property(property="details", type="string", example="Bank account details here")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/bank_information_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/bank_information_list?page=1"),
     *                 @OA\Property(property="next_page_url", type="string", example=null),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/bank_information_list"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->bankInformationService->all($request), 'Bank information list successfully fetched.');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/bank_information_add",
     *     summary="Create bank information",
     *     tags={"Bank Information"},
     *     description="Add new bank information with title and details",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Bank information details",
     *         @OA\JsonContent(
     *             required={"title", "details"},
     *             @OA\Property(property="title", type="string", example="Account A", description="Title of bank information"),
     *             @OA\Property(property="details", type="string", example="Bank account details here", description="Detailed bank information")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bank information created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bank information created successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $this->bankInformationService->create($request);
            return $this->successResponse(null, 'Bank information created successfully.');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/bank_information_details/{id}",
     *     summary="Get bank information details",
     *     tags={"Bank Information"},
     *     description="Retrieve a specific bank information record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Bank information ID",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank information retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bank information fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Account A"),
     *                 @OA\Property(property="details", type="string", example="Bank account details here")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank information not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            $bankInformation = $this->bankInformationService->get($id);
            return $this->successResponse($bankInformation, 'Bank information fetched successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Bank information not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/bank_information_update/{id}",
     *     summary="Update bank information",
     *     tags={"Bank Information"},
     *     description="Update a bank information record with new title and details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Bank information ID",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated bank information details",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Account A"),
     *             @OA\Property(property="details", type="string", example="Updated bank details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank information updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bank information updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank information not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->bankInformationService->update($request, $id);
            return $this->successResponse(null, 'Bank information updated successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Bank information not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/bank_information_delete/{id}",
     *     summary="Delete bank information",
     *     tags={"Bank Information"},
     *     description="Delete a bank information record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Bank information ID",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank information deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Bank information deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank information not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->bankInformationService->delete($id);
            return $this->successResponse(null, 'Bank information deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Bank information not found');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /** @OA\Get(
     *     path="/api/bank_information_dropdown_list",
     *     summary="Get all bank information for dropdown",
     *     tags={"Bank Information"},
     *     description="Retrieve a list of all bank information for dropdown selection",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved bank information for dropdown",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Bank of America"),
     *                    @OA\Property(property="details", type="string", example="Account details for Bank of America")
     *                  )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Bank information dropdown list"
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
    public function bankInformationDropdownList()
    {
        try {
            return $this->successResponse($this->bankInformationService->bankInformationDropdownList());
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Bank information data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
