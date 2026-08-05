<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\PrescriptionService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @OA\Tag(
 *     name="Prescriptions",
 *     description="Prescriptions operations"
 * )
 */
class PrescriptionController extends Controller
{

    use ResponseTrait;

    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * @OA\Get(
     *      path="/api/prescriptions_list",
     *      summary="Get prescription list",
     *      tags={"Prescriptions"},
     *      description="Retrieve a list of all prescriptions",
     *      security={{"bearerAuth": {}}},
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
     *          description="Field to sort by (type, status, appointment_time, etc.)",
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
     *      @OA\Response(
     *          response=200,
     *          description="Prescriptions list successfully fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Prescriptions list retrieved successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="string", format="uuid", example="a3f1e2c4-5b67-4f2e-bd8c-12abcde45678"),
     *                      @OA\Property(property="consultation_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *                      @OA\Property(property="doctor_id", type="integer", example=2),
     *                      @OA\Property(property="patient_id", type="string", format="uuid", example="e15b0e4d-bb30-4318-8d73-8ff9c2d47819"),
     *                      @OA\Property(property="patient_number", type="string", example="P123456"),
     *                      @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                      @OA\Property(property="doctor_name", type="string", example="Dr. Smith"),
     *                      @OA\Property(property="patient_email", type="string", format="email", example="john@example.com"),
     *                      @OA\Property(property="doctor_email", type="string", format="email", example="drsmith@example.com"),
     *                      @OA\Property(property="patient_phone", type="string", example="+919876543210"),
     *                      @OA\Property(property="doctor_phone", type="string", example="+919812345678"),
     *                      @OA\Property(property="medicine_ids", type="string", example="1,2,3"),
     *                      @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                      @OA\Property(property="dosage", type="string", example="1-0-1"),
     *                      @OA\Property(property="duration", type="string", example="5 days"),
     *                      @OA\Property(property="time", type="string", example="Morning, Evening"),
     *                      @OA\Property(property="food_advice", type="string", example="Take after food"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-09T12:34:56Z"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-09T12:34:56Z")
     *                  )
     *              )
     *          )
     *      ),
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
            return $this->successResponse($this->prescriptionService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/prescriptions_add",
     *     summary="Create a new prescription",
     *     tags={"Prescriptions"},
     *     description="Create a new prescription with patient and doctor details",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"consultation_id", "doctor_id", "patient_id", "medicine_ids", "dosage", "duration", "time", "food_advice"},
     *             @OA\Property(property="consultation_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *             @OA\Property(property="doctor_id", type="integer", example=2),
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="e15b0e4d-bb30-4318-8d73-8ff9c2d47819"),
     *             @OA\Property(property="medicine_ids", type="string", example="1,2,3"),
     *             @OA\Property(property="dosage", type="string", example="1-0-1"),
     *             @OA\Property(property="duration", type="string", example="5 days"),
     *             @OA\Property(property="time", type="string", example="Morning, Evening"),
     *             @OA\Property(property="food_advice", type="string", example="Take after food")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Prescription created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Prescription created successfully"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
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
            $proxiedService = ServiceInterceptor::intercept($this->prescriptionService);
            $proxiedService->create($request);
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
     * @OA\Put(
     *      path="/api/prescriptions_update/{id}",
     *      summary="Update a prescription",
     *      tags={"Prescriptions"},
     *      description="Update a prescription by ID",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Prescription ID",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              format="uuid"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              required={"consultation_id", "doctor_id", "patient_id", "medicine_ids", "dosage", "duration", "time", "food_advice"},
     *              @OA\Property(property="consultation_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *              @OA\Property(property="doctor_id", type="integer", example=2),
     *              @OA\Property(property="patient_id", type="string", format="uuid", example="e15b0e4d-bb30-4318-8d73-8ff9c2d47819"),
     *              @OA\Property(property="medicine_ids", type="string", example="1,2,3"),
     *              @OA\Property(property="dosage", type="string", example="1-0-1"),
     *              @OA\Property(property="duration", type="string", example="5 days"),
     *              @OA\Property(property="time", type="string", example="Morning, Evening"),
     *              @OA\Property(property="food_advice", type="string", example="Take after food")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Prescription updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Prescription updated successfully"),
     *              @OA\Property(property="data", type="object", nullable=true)
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/UnauthorizedResponse"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          ref="#/components/responses/NotFound"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          ref="#/components/responses/ServerErrorResponse"
     *      )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->prescriptionService);
            $proxiedService->update($request, $id);
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
     * @OA\Get(
     *     path="/api/prescriptions_details/{id}",
     *     summary="Get a prescription by its ID",
     *     tags={"Prescriptions"},
     *     description="Get detailed information about a specific prescription",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Prescription ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription details fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Prescription details retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid", example="a3f1e2c4-5b67-4f2e-bd8c-12abcde45678"),
     *                 @OA\Property(property="consultation_id", type="string", format="uuid", example="f47ac10b-58cc-4372-a567-0e02b2c3d479"),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="e15b0e4d-bb30-4318-8d73-8ff9c2d47819"),
     *                 @OA\Property(property="patient_number", type="string", example="P123456"),
     *                 @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                 @OA\Property(property="doctor_name", type="string", example="Dr. Smith"),
     *                 @OA\Property(property="patient_email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="doctor_email", type="string", format="email", example="drsmith@example.com"),
     *                 @OA\Property(property="patient_phone", type="string", example="+919876543210"),
     *                 @OA\Property(property="doctor_phone", type="string", example="+919812345678"),
     *                 @OA\Property(property="medicine_ids", type="string", example="1,2,3"),
     *                 @OA\Property(property="medicine_name", type="string", example="Paracetamol"),
     *                 @OA\Property(property="dosage", type="string", example="1-0-1"),
     *                 @OA\Property(property="duration", type="string", example="5 days"),
     *                 @OA\Property(property="time", type="string", example="Morning, Evening"),
     *                 @OA\Property(property="food_advice", type="string", example="Take after food"),
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
            return $this->successResponse($this->prescriptionService->get($id));
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/prescriptions/{id}",
     *     summary="Delete a prescription",
     *     tags={"Prescriptions"},
     *     description="Delete a prescription by its ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Prescription ID",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Prescription deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Prescription deleted successfully"),
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
            $this->prescriptionService->delete($id);
            return $this->successResponse();
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
