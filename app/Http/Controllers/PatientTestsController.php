<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\PatientTestsService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Patient Tests",
 *     description="Patient Tests operations"
 * )
 */

class PatientTestsController extends Controller
{
    use ResponseTrait;

    private $patientTestsService;

    public function __construct(PatientTestsService $patientTestsService)
    {
        $this->patientTestsService = $patientTestsService;
    }


    /**
     * @OA\Get(
     *     path="/api/patient_tests_list",
     *     tags={"Patient Tests"},
     *     summary="Get list of Patients test",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of patients test with their details",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example=""
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by (test_name, result_status, billing_amount, etc.)",
     *         @OA\Schema(
     *             type="string",
     *             example=""
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
     *             @OA\Property(property="message", type="string", example="Patient tests retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", format="uuid", example="a1b2c3d4-5678-90ef-gh12-3456ijkl7890"),
     *                     @OA\Property(property="test_id", type="integer", example=12),
     *                     @OA\Property(property="test_name", type="string", example="Blood Test"),
     *                     @OA\Property(property="test_description", type="string", example="Routine blood screening"),
     *                     @OA\Property(property="test_place", type="string", example="In-House Lab"),
     *                     @OA\Property(property="billing_amount", type="integer", example=500),
     *                     @OA\Property(property="result_status", type="string", enum={"Pending", "Started", "Completed"}, example="Pending"),
     *                     @OA\Property(property="consultation_id", type="string", format="uuid", example="e5f6a7b8-9012-3456-cdef-7890abcd1234"),
     *                     @OA\Property(property="result_uploaded_by", type="integer", example=4),
     *                     @OA\Property(property="document_upload", type="string", format="url", example="https://example.com/uploads/test_result.pdf"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T13:00:00Z")
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
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->patientTestsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/patient_tests_details/{id}",
     *     summary="Get a patient test",
     *     tags={"Patient Tests"},
     *     security={{"bearerAuth":{}}},
     *     description="Get a patient test with the given ID",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Patient test's ID",
     *         @OA\Schema(type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120002")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient test data fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient test data fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120002"),
     *                 @OA\Property(property="consultation_id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120005"),
     *                 @OA\Property(property="test_id", type="integer", nullable=true, example=101),
     *                 @OA\Property(property="test_name", type="string", example="Blood Test"),
     *                 @OA\Property(property="test_description", type="string", nullable=true, example="General blood screening test."),
     *                 @OA\Property(property="test_place", type="string", example="Same Hospital"),
     *                 @OA\Property(property="billing_amount", type="integer", example=750),
     *                 @OA\Property(property="result_status", type="string", enum={"Pending", "Started", "Completed"}, example="Started"),
     *                 @OA\Property(property="result_uploaded_by", type="integer", example=2),
     *                 @OA\Property(property="document_upload", type="string", nullable=true, example="documents/test-results/blood-test-123.pdf"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-01T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-03T08:00:00Z")
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
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function get(string $id)
    {
        try {
            return $this->successResponse($this->patientTestsService->get($id));
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/patient_tests_create",
     *     summary="Create a patient test",
     *     tags={"Patient Tests"},
     *     security={{"bearerAuth":{}}},
     *     description="Create a patient test with the given data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"test_name", "patient_id","doctor_id","test_place", "result_status", "result_uploaded_by"},
     *             @OA\Property(property="consultation_id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120005"),
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120005"),
     *             @OA\Property(property="doctor_id", type="number",  example="1"),
     *             @OA\Property(property="test_id", type="integer", nullable=true, example=101),
     *             @OA\Property(property="test_name", type="string", example="Blood Test"),
     *             @OA\Property(property="test_description", type="string", nullable=true, example="General blood screening test."),
     *             @OA\Property(property="test_place", type="string", example="Same Hospital"),
     *             @OA\Property(property="billing_amount", type="integer", example=750),
     *             @OA\Property(property="result_status", type="string", enum={"Pending", "Started", "Completed"}, example="Started"),
     *             @OA\Property(property="result_uploaded_by", type="integer", example=2),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient test created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient test created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120002")
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
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->patientTestsService);
            return $this->successResponse($proxiedService->patientTestCreate($request));
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/patient_tests_update/{id}",
     *     summary="Update a patient test",
     *     tags={"Patient Tests"},
     *     security={{"bearerAuth":{}}},
     *     description="Update an existing patient test record by ID",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Patient test's ID",
     *         @OA\Schema(type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120002")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"test_name", "patient_id", "doctor_id", "test_place", "result_status", "result_uploaded_by"},
     *             @OA\Property(property="test_name", type="string", example="Blood Test - Updated"),
     *             @OA\Property(property="test_description", type="string", nullable=true, example="Updated description for the test"),
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120005"),
     *             @OA\Property(property="doctor_id", type="number",  example="1"),
     *             @OA\Property(property="test_place", type="string", example="Other Hospital"),
     *             @OA\Property(property="billing_amount", type="integer", example=1200),
     *             @OA\Property(property="result_status", type="string", enum={"Pending", "Started", "Completed"}, example="Completed"),
     *             @OA\Property(property="result_uploaded_by", type="integer", example=3),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient test updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient test updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120002")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
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
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->patientTestsService);
            return $this->successResponse($proxiedService->patientTestUpdate($request, $id));
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/patient_tests_delete/{id}",
     *     summary="Delete a patient test",
     *     tags={"Patient Tests"},
     *     security={{"bearerAuth":{}}},
     *     description="Delete a patient test by ID",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Patient test's ID",
     *         @OA\Schema(type="string", format="uuid", example="1e4c2a3a-bf6f-11ee-a64c-0242ac120002")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient test deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient test deleted successfully")
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
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */

    public function delete(string $id)
    {
        try {
            $this->patientTestsService->delete($id);
            return $this->successResponse();
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
