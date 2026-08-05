<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\AppointmentsService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Appointments",
 *     description="API endpoints for managing patient appointments in the hospital system"
 * )
 */
class AppointmentsController extends Controller
{
    use ResponseTrait;

    private $appointmentsService;

    public function __construct(AppointmentsService $appointmentsService)
    {
        $this->appointmentsService = $appointmentsService;
    }

    /**
     * @OA\Get(
     *     path="/api/appointments_statistics",
     *     tags={"Appointments"},
     *     summary="Get appointment statistics",
     *     security={{"bearerAuth": {}}},
     *     description="Returns statistics about appointments including total count, today's count, completed count, and pending count",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_appointments", type="integer", example=150),
     *                 @OA\Property(property="todays_appointments", type="integer", example=12),
     *                 @OA\Property(property="completed_appointments", type="integer", example=95),
     *                 @OA\Property(property="pending_appointments", type="integer", example=55)
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
    public function getStatistics()
    {
        try {
            $statistics = $this->appointmentsService->getStatistics();
            return $this->successResponse($statistics, 'Appointment statistics retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/appointments_list",
     *     tags={"Appointments"},
     *     summary="Get list of Appointments",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of patients with their details",
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword",
     *         @OA\Schema(
     *             type="string",
     *             example="vishnu"
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (type, status, appointment_time, etc.)",
     *         @OA\Schema(
     *             type="string",
     *             example="name"
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
     *     
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointments retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *                     @OA\Property(property="type", type="string", example="First Visit"),
     *                     @OA\Property(property="status", type="string", example="Confirmed"),
     *                     @OA\Property(property="appointment_time", type="string", example="14:30"),   
     *                     @OA\Property(property="appointment_date", type="string", example="2025-04-23"),
     *                     @OA\Property(property="appointment_number", type="string", example="APT-123456"),
     *                     @OA\Property(property="doctor", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Dr. John Doe")
     *                     ),
     *                     @OA\Property(property="patient", type="object",
     *                         @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *                         @OA\Property(property="name", type="string", example="Jane Smith")
     *                     )
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=5)
     *                 )
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
     *     ),
     * )
     */

    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->appointmentsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/appointments_details/{id}",
     *     summary="Get single appointment",
     *     tags={"Appointments"},
     *     description="Fetch a single appointment by ID including doctor and patient details.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Appointment ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment details fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="type", type="string", example="Consultation"),
     *                 @OA\Property(property="status", type="string", example="Confirmed"),
     *                 @OA\Property(property="appointment_time", type="string", example="15:30"),
     *                 @OA\Property(property="appointment_date", type="string", example="2025-04-24"),
     *                 @OA\Property(property="appointment_number", type="string", example="APT-000123"),
     *                 @OA\Property(
     *                     property="doctor",
     *                     type="object",
     *                     @OA\Property(property="first_name", type="string", example="Dr. John"),
     *                     @OA\Property(property="last_name", type="string", example="Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@hospital.com"),
     *                     @OA\Property(property="phone_no", type="string", example="9876543210")
     *                 ),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="first_name", type="string", example="Jane"),
     *                     @OA\Property(property="last_name", type="string", example="Smith"),
     *                     @OA\Property(property="email", type="string", example="jane.smith@example.com"),
     *                     @OA\Property(property="phone_no", type="string", example="9123456780")
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
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->appointmentsService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Appointment data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/appointments_add",
     *     summary="Create appointment",
     *     tags={"Appointments"},
     *     description="Create a new appointment with patient and doctor details.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "status", "doctor_id", "patient_id", "appointment_time","front_desk_user_id"},
     *             @OA\Property(property="type", type="string", example="First Visit"),
     *             @OA\Property(property="status", type="string", example="Pending"),
     *             @OA\Property(property="doctor_id", type="integer", example=1),
     *             @OA\Property(property="front_desk_user_id", type="integer", example=1),
     *             @OA\Property(property="complaint", type="string", example="Fever and headache"),
     *             @OA\Property(property="patient_id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *             @OA\Property(property="appointment_fees", type="number", format="float", example=200.00),
     *             @OA\Property(property="appointment_time", type="string", example="16:30:00"),
     *             @OA\Property(property="appointment_date", type="string", example="2024-04-24")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment created successfully.")
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
            $proxiedService = ServiceInterceptor::intercept($this->appointmentsService);
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
     *     path="/api/appointments_update/{id}",
     *     summary="Update appointment",
     *     tags={"Appointments"},
     *     description="Update an existing appointment by ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Appointment ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type", "status", "doctor_id", "patient_id", "appointment_time","front_desk_user_id"},
     *             @OA\Property(property="type", type="string", example="Follow-up"),
     *             @OA\Property(property="status", type="string", example="Confirmed"),
     *             @OA\Property(property="doctor_id", type="integer", example=1),
     *             @OA\Property(property="front_desk_user_id", type="integer", example=1),
     *             @OA\Property(property="complaint", type="string", example="Persistent cough"),
     *             @OA\Property(property="patient_id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *             @OA\Property(property="appointment_fees", type="number", format="float", example=250.00),
     *             @OA\Property(property="appointment_time", type="string", example="10:00:00"),
     *             @OA\Property(property="appointment_date", type="string", example="2024-04-24")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment updated successfully.")
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
            $proxiedService = ServiceInterceptor::intercept($this->appointmentsService);
            $proxiedService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Appointment data not found.');
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
     *     path="/api/appointments_delete/{id}",
     *     summary="Delete appointment",
     *     tags={"Appointments"},
     *     description="Delete an appointment by its ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Appointment ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment deleted successfully.")
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
            $this->appointmentsService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Appointment data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    // /**
    //  * @OA\Post(
    //  *     path="/api/appointment_fees",
    //  *     summary="Add appointment fees",
    //  *     tags={"Appointments"},
    //  *     description="Add appointment fees for a given appointment.",
    //  *     security={{"bearerAuth": {}}},
    //  *     @OA\RequestBody(
    //  *         required=true,
    //  *         @OA\JsonContent(
    //  *             required={"appointment_number", "amount"},
    //  *             @OA\Property(property="appointment_number", type="string", example="APT0001"),
    //  *             @OA\Property(property="amount", type="number", format="float", example=250.00)
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=201,
    //  *         description="Appointment fees created successfully.",
    //  *         @OA\JsonContent(
    //  *             @OA\Property(property="status", type="string", example="success"),
    //  *             @OA\Property(property="message", type="string", example="Appointment fees created successfully.")
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         ref="#/components/responses/NotFound"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         ref="#/components/responses/ServerErrorResponse"
    //  *     )
    //  * )
    //  */
    // public function appointmentFees(Request $request)
    // {
    //     try {
    //         $this->appointmentsService->appointmentFees($request);
    //         return $this->successResponse();
    //     } catch (Exception $e) {
    //         return $this->exceptionResponse($e);
    //     }
    // }

}