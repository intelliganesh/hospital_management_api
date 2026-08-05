<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\TestService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Tests",
 *     description="API endpoints for managing medical tests and diagnostics in the hospital system"
 * )
 */
class TestsController extends Controller
{
    use ResponseTrait;

    private $testsService;

    public function __construct(TestService $testsService)
    {
        $this->testsService = $testsService;
    }

    /**
     * @OA\Get(
     *     path="/api/tests_list",
     *     tags={"Tests"},
     *     summary="Get all medical tests",
     *     description="Retrieve a paginated list of all medical tests with optional filtering and sorting",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword to filter by test name or description",
     *         @OA\Schema(
     *             type="string",
     *             example="Blood Test"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by (e.g., test_name, test_price, department_type)",
     *         @OA\Schema(
     *             type="string",
     *             example="test_name"
     *         ),
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
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Tests details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                         @OA\Property(property="test_name", type="string", example="Complete Blood Count"),
     *                         @OA\Property(property="test_description", type="string", example="Measures several components of blood"),
     *                         @OA\Property(property="test_price", type="number", format="float", example=1500),
     *                         @OA\Property(property="tax_price", type="number", format="float", example=150),
     *                         @OA\Property(property="department_type", type="string", example="Proctology"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/tests_list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/tests_list?page=3"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost/api/tests_list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/tests_list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25)
     *             )
     *         ),
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
            return $this->successResponse($this->testsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/tests_details/{id}",
     *     tags={"Tests"},
     *     summary="Get detailed information about a specific test",
     *     security={{"bearerAuth": {}}},
     *     description="Retrieve complete details of a medical test by its unique identifier",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Unique identifier of the test",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="d290f1ee-6c54-4b01-90e6-d701748f0851"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Test details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Test details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                 @OA\Property(property="test_name", type="string", example="Complete Blood Count"),
     *                 @OA\Property(property="test_description", type="string", example="Measures several components of blood"),
     *                 @OA\Property(property="test_price", type="number", format="float", example=1500),
     *                 @OA\Property(property="tax_price", type="number", format="float", example=150),
     *                 @OA\Property(property="department_type", type="string", example="Proctology"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
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
            return $this->successResponse($this->testsService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Test data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tests_add",
     *     summary="Create a new medical test",
     *     description="Add a new medical test to the system with name, description, pricing and department classification",
     *     tags={"Tests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Test information",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"test_name"},
     *             @OA\Property(property="test_name", type="string", example="Lipid Profile", description="Name of the medical test"),
     *             @OA\Property(property="test_description", type="string", example="Measures cholesterol and triglycerides", description="Detailed description of the test"),
     *             @OA\Property(property="tax_price", type="number", format="float", example=100, description="Tax amount for the test"),
     *             @OA\Property(property="test_price", type="number", format="float", example=1000, description="Base price of the test"),
     *             @OA\Property(property="department_type", type="string", enum={"None","Allopathy","Proctology","Non Proctology"}, example="Proctology", description="Department classification for the test"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Test created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Test created successfully.")
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
            $this->testsService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/tests_update/{id}",
     *     summary="Update an existing medical test",
     *     description="Modify details of an existing medical test by its unique identifier",
     *     tags={"Tests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Unique identifier of the test",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated test information",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"test_name"},
     *             @OA\Property(property="test_name", type="string", example="Updated Lipid Profile", description="Name of the medical test"),
     *             @OA\Property(property="test_description", type="string", example="Updated description for cholesterol test", description="Detailed description of the test"),
     *             @OA\Property(property="tax_price", type="number", format="float", example=120, description="Tax amount for the test"),
     *             @OA\Property(property="test_price", type="number", format="float", example=1200, description="Base price of the test"),
     *             @OA\Property(property="department_type", type="string", enum={"None","Allopathy","Proctology","Non Proctology"}, example="Proctology", description="Department classification for the test"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Test updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Test updated successfully.")
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
    public function update(Request $request, ?string $id)
    {
        try {
            $this->testsService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Test data not found.');
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
     *     path="/api/tests_delete/{id}",
     *     summary="Delete a medical test",
     *     description="Remove a medical test from the system by its unique identifier",
     *     tags={"Tests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Unique identifier of the test to delete",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Test deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Test deleted successfully.")
     *         )
     *     ),
     *    @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
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
            $this->testsService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Test data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/tests_list_for_dropdown",
     *     summary="Get list of tests for dropdown selection",
     *     description="Retrieve a simplified list of all tests suitable for dropdown menus and selection interfaces",
     *     tags={"Tests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="department_value",
     *          in="query",
     *          required=false,
     *          description="Filter tests by department type",
     *         @OA\Schema(
     *             type="string",
     *             enum={"None","Allopathy","Proctology","Non Proctology"},
     *             example="Proctology"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tests for dropdown",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Test list successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="test_name", type="string", example="Complete Blood Count"),
     *                     @OA\Property(property="test_description", type="string", example="Measures several components of blood"),
     *                     @OA\Property(property="test_price", type="number", format="float", example=1500),
     *                     @OA\Property(property="tax_price", type="number", format="float", example=150),
     *                     @OA\Property(property="department_type", type="string", example="Proctology")
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
    public function testList(?string $departmentValue = null)
    {
        try {
            return $this->successResponse($this->testsService->testList($departmentValue));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Test data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
