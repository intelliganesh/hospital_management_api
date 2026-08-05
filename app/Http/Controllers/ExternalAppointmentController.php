<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\ExternalAppointmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="External Appointments",
 *     description="API endpoints for external patient appointment bookings (no authentication required)"
 * )
 */
class ExternalAppointmentController extends Controller
{
    use ResponseTrait;

    private $externalAppointmentService;

    public function __construct(ExternalAppointmentService $externalAppointmentService)
    {
        $this->externalAppointmentService = $externalAppointmentService;
    }

    /**
     * @OA\Post(
     *     path="/api/external-appointments",
     *     tags={"External Appointments"},
     *     summary="Create a new external appointment",
     *     description="Create a new appointment from external patient without authentication",
     *     @OA\RequestBody(
     *         required=true,
     *         description="External appointment data",
     *         @OA\JsonContent(
    *             required={"name", "phone", "email", "doctor_id", "appointment_datetime", "appointment_type"},
    *             @OA\Property(property="name", type="string", example="John Doe"),
    *             @OA\Property(property="age", type="integer", example=30),
    *             @OA\Property(property="phone", type="string", example="+91-9876543210"),
    *             @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male"),
    *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
    *             @OA\Property(property="doctor_id", type="integer", example=1),
    *             @OA\Property(property="appointment_datetime", type="string", format="date-time", example="2026-04-15 10:30:00"),
    *             @OA\Property(property="alternate_date", type="string", format="date-time", example="2026-04-16 14:00:00"),
    *             @OA\Property(property="appointment_type", type="string", example="Consultation"),
    *             @OA\Property(property="symptoms", type="string", example="Fever, cough and body ache"),
    *             @OA\Property(property="amount", type="number", format="decimal", example=500.00),
    *             @OA\Property(property="currency", type="string", example="INR"),
    *             @OA\Property(property="meeting_link", type="string", example="https://zoom.us/j/123456789"),
    *             @OA\Property(property="payment_type", type="string", enum={"Cash", "Credit Card", "Debit Card", "UPI", "Bank Transfer", "Online"}, example="Online"),
    *             @OA\Property(property="payment_info", type="string", example="Transaction ID or payment reference"),
    *             @OA\Property(property="appointment_reference_number", type="string", example="EXT001"),
    *             @OA\Property(property="daily_meeting_info", type="object", description="Meeting info JSON", @OA\Property(property="guest_access_code", type="string"), @OA\Property(property="room_name", type="string"), @OA\Property(property="room_url", type="string"), @OA\Property(property="doctor_token", type="string"), @OA\Property(property="patient_token", type="string")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="External appointment created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="age", type="integer"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="doctor_id", type="integer"),
     *                 @OA\Property(property="appointment_datetime", type="string", format="date-time"),
     *                 @OA\Property(property="alternate_date", type="string", format="date-time"),
     *                 @OA\Property(property="appointment_type", type="string"),
     *                 @OA\Property(property="symptoms", type="string"),
     *                 @OA\Property(property="amount", type="number", format="decimal"),
     *                 @OA\Property(property="meeting_link", type="string"),
     *                 @OA\Property(property="payment_type", type="string"),
     *                 @OA\Property(property="payment_info", type="string"),
     *                 @OA\Property(property="status", type="string", example="Pending"),
     *                 @OA\Property(property="visit_type", type="string", example="FirstVisit"),
     *                 @OA\Property(property="transaction_id", type="string"),
     *                 @OA\Property(property="payment_date", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $appointment = $this->externalAppointmentService->create($request);
            return $this->successResponse($appointment, 'External appointment created successfully', 201);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments",
     *     tags={"External Appointments"},
     *     summary="List all external appointments",
     *     description="Get paginated list of external appointments with filtering options",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by appointment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Pending", "Confirmed", "Completed", "Cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         description="Filter by doctor UUID",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="appointment_type",
     *         in="query",
     *         description="Filter by appointment type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name, phone, or email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter from start date (Y-m-d H:i:s)",
     *         required=false,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter to end date (Y-m-d H:i:s)",
     *         required=false,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field (default: appointment_datetime)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order (default: desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of external appointments",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="External appointments retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string", format="uuid"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="email", type="string"),
     *                         @OA\Property(property="phone", type="string"),
     *                         @OA\Property(property="appointment_datetime", type="string", format="date-time"),
     *                         @OA\Property(property="status", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="last_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $appointments = $this->externalAppointmentService->list($request);
            return $this->successResponse($appointments, 'External appointments retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments/{id}",
     *     tags={"External Appointments"},
     *     summary="Get external appointment details",
     *     description="Retrieve details of a specific external appointment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="External appointment ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="External appointment retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="age", type="integer"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="doctor_id", type="integer"),
     *                 @OA\Property(property="appointment_datetime", type="string", format="date-time"),
     *                 @OA\Property(property="alternate_date", type="string", format="date-time"),
     *                 @OA\Property(property="appointment_type", type="string"),
     *                 @OA\Property(property="symptoms", type="string"),
     *                 @OA\Property(property="currency", type="string", example="INR"),
     *                 @OA\Property(property="amount", type="number", format="decimal"),
     *                 @OA\Property(property="meeting_link", type="string"),
     *                 @OA\Property(property="payment_type", type="string"),
     *                 @OA\Property(property="payment_info", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="visit_type", type="string"),
     *                 @OA\Property(property="transaction_id", type="string"),
     *                 @OA\Property(property="payment_date", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $appointment = $this->externalAppointmentService->getById($id);
            return $this->successResponse($appointment, 'External appointment retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(null, 'Appointment not found', 404);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/external-appointments/{id}",
     *     tags={"External Appointments"},
     *     summary="Update external appointment",
     *     description="Update details of an external appointment",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="External appointment ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated appointment data",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="age", type="integer", example=30),
     *             @OA\Property(property="phone", type="string", example="+91-9876543210"),
     *             @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="doctor_id", type="integer"),
     *             @OA\Property(property="appointment_datetime", type="string", format="date-time"),
     *             @OA\Property(property="alternate_date", type="string", format="date-time"),
     *             @OA\Property(property="appointment_type", type="string"),
     *             @OA\Property(property="symptoms", type="string"),
     *             @OA\Property(property="amount", type="number", format="decimal"),
     *             @OA\Property(property="currency", type="string", example="INR"),
     *             @OA\Property(property="meeting_link", type="string"),
     *             @OA\Property(property="payment_type", type="string", enum={"link","Bank Transfer"}),
     *             @OA\Property(property="payment_info", type="string"),
     *             @OA\Property(property="status", type="string", enum={"Pending", "Confirmed", "Payment Pending", "Paid", "Completed", "Cancelled"}),
     *             @OA\Property(property="visit_type", type="string", example="FirstVisit"),
     *             @OA\Property(property="transaction_id", type="string"),
     *             @OA\Property(property="payment_date", type="string", format="date-time"),
     *             @OA\Property(property="payment_screenshot", type="string", format="path")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="External appointment updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $appointment = $this->externalAppointmentService->update($request, $id);
            return $this->successResponse($appointment, 'External appointment updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(null, 'Appointment not found', 404);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/external-appointments/{id}",
     *     tags={"External Appointments"},
     *     summary="Delete external appointment",
     *     description="Delete a specific external appointment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="External appointment ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="External appointment deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $deleted = $this->externalAppointmentService->delete($id);
            if (!$deleted) {
                return $this->errorResponse(null, 'Appointment not found', 404);
            }
            return $this->successResponse(null, 'External appointment deleted successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments/doctor/{doctor_id}",
     *     tags={"External Appointments"},
     *     summary="Get appointments by doctor",
     *     description="Get all external appointments for a specific doctor",
     *     @OA\Parameter(
     *         name="doctor_id",
     *         in="path",
     *         required=true,
     *         description="Doctor ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"Pending", "Confirmed", "Completed", "Cancelled"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name, phone, or email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field (default: appointment_datetime)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order (default: asc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor's appointments retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getByDoctor($doctorId, Request $request)
    {
        try {
            $appointments = $this->externalAppointmentService->getByDoctor($doctorId, $request);
            return $this->successResponse($appointments, 'Doctor appointments retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/external-appointments/{id}/status",
     *     tags={"External Appointments"},
     *     summary="Update appointment status",
     *     description="Change the status of an external appointment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="External appointment ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="New status",
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"Pending", "Confirmed", "Payment Pending", "Paid", "Completed", "Cancelled"}, example="Confirmed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Appointment status updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid status"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:Pending,Confirmed,Payment Pending,Paid,Completed,Cancelled',
            ]);

            $appointment = $this->externalAppointmentService->changeStatus($id, $request->status);
            return $this->successResponse($appointment, 'Appointment status updated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse(null, 'Appointment not found', 404);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 'Validation failed', 422);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments/upcoming",
     *     tags={"External Appointments"},
     *     summary="Get upcoming appointments",
     *     description="Get all upcoming external appointments",
     *     @OA\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         description="Filter by doctor ID",
     *         required=false,
     *         @OA\Schema(type="string", format="uuid"),
     *          @OA\Property(property="age", type="integer", example=30),
     *          @OA\Property(property="phone", type="string", example="+91-9876543210"),
     *           @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male"),
     *           @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *           @OA\Property(property="citizenship", type="string", description="Citizenship of the patient"),
     *           @OA\Property(property="place_of_living", type="string", description="Place of living of the patient"),
     *         description="Sort field (default: appointment_datetime)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order (default: asc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Upcoming appointments retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getUpcoming(Request $request)
    {
        try {
            $appointments = $this->externalAppointmentService->getUpcoming($request);
            return $this->successResponse($appointments, 'Upcoming appointments retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments/statistics",
     *     tags={"External Appointments"},
     *     summary="Get appointment statistics",
     *     description="Get statistics about external appointments",
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_appointments", type="integer"),
     *                 @OA\Property(property="pending_appointments", type="integer"),
     *                 @OA\Property(property="confirmed_appointments", type="integer"),
     *                 @OA\Property(property="completed_appointments", type="integer"),
     *                 @OA\Property(property="cancelled_appointments", type="integer"),
     *                 @OA\Property(property="upcoming_appointments", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getStatistics()
    {
        try {
            $statistics = $this->externalAppointmentService->getStatistics();
            return $this->successResponse($statistics, 'Statistics retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments/doctors",
     *     summary="Get list of available doctors",
     *     description="Retrieve a paginated list of all active doctors for appointment selection. No authentication required.",
     *     tags={"External Appointments"},
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by (name, email, phone, designation)",
     *         required=false,
     *         @OA\Schema(type="string", default="name")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order (asc or desc)",
     *         required=false,
     *         @OA\Schema(type="string", default="asc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved doctor list",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Doctors retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="string"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="email", type="string"),
     *                         @OA\Property(property="phone", type="string"),
     *                         @OA\Property(property="designation", type="string"),
     *                         @OA\Property(property="qualification", type="string"),
     *                         @OA\Property(property="department_name", type="string"),
     *                         @OA\Property(property="image", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getDoctorList(Request $request)
    {
        try {
            $doctors = $this->externalAppointmentService->getDoctorList($request);
            return $this->successResponse($doctors, 'Doctors retrieved successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/external-appointments/doctors/{doctor_id}",
     *     summary="Get doctor details",
     *     description="Retrieve detailed information about a specific doctor. No authentication required.",
     *     tags={"External Appointments"},
     *     @OA\Parameter(
     *         name="doctor_id",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved doctor details",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Doctor retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="designation", type="string"),
     *                 @OA\Property(property="qualification", type="string"),
     *                 @OA\Property(property="department_name", type="string"),
     *                 @OA\Property(property="image", type="string"),
     *                 @OA\Property(property="status", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getDoctorDetail(string $doctorId)
    {
        try {
            $doctor = $this->externalAppointmentService->getDoctorDetail($doctorId);
            return $this->successResponse($doctor, 'Doctor retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Doctor not found'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    
    /**
     * @OA\Post(
     *     path="/api/external-appointments/{id}/generate_link",
     *     tags={"External Appointments"},
     *     summary="Generate meeting link for consultation",
     *     description="Generate a video meeting link and tokens for an external appointment. Updates daily_meeting_info field.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="External appointment ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Meeting link generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Meeting link generated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="meeting_link", type="string", example="https://meet.jit.si/consultation-123"),
     *                 @OA\Property(property="daily_meeting_info", type="object",
     *                     @OA\Property(property="guest_access_code", type="string"),
     *                     @OA\Property(property="room_name", type="string"),
     *                     @OA\Property(property="room_url", type="string"),
     *                     @OA\Property(property="doctor_token", type="string"),
     *                     @OA\Property(property="patient_token", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Appointment not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function generateLinkForConsultation($id)
    {
        try {
            $meetingData = $this->externalAppointmentService->createMeetingLink($id);
            return $this->successResponse($meetingData, 'Meeting link generated successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Appointment not found'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/external-appointments/{id}/send_link",
     *     tags={"External Appointments"},
     *     summary="Send meeting link for consultation",
     *     description="Send a video meeting link and tokens for an external appointment. Updates daily_meeting_info field.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="External appointment ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="to", type="string", enum={"patient", "doctor"}, default="patient")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Meeting link generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Meeting link sent successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Appointment not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function sendLinkForConsultation($id,Request $request)
    {
        try {
            $this->externalAppointmentService->sendMeetingLink($id,$request->to);
            return $this->successResponse(null, 'Meeting link sent successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Appointment not found'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
