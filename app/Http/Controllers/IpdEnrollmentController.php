<?php

namespace App\Http\Controllers;

use Exception;
use App\Services\IpdEnrollmentService;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="IPD Enrollment",
 *     description="API endpoints for managing IPD enrollment from consultations"
 * )
 */
class IpdEnrollmentController extends Controller
{
    use ResponseTrait;

    private $ipdEnrollmentService;

    public function __construct(IpdEnrollmentService $ipdEnrollmentService)
    {
        $this->ipdEnrollmentService = $ipdEnrollmentService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_enrollment_list",
     *     tags={"IPD Enrollment"},
     *     summary="Get all consultations eligible for IPD admission",
     *     security={{"bearerAuth": {}}},
     *     description="Retrieve all consultations where advice_admission=1 (eligible for IPD enrollment)",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="Search by patient name, email, phone or doctor name",
     *         @OA\Schema(type="string", example="John Doe")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(type="string", example="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Sort direction (asc or desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Consultations retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="appointment_number", type="string"),
     *                     @OA\Property(property="patient_id", type="string", format="uuid"),
     *                     @OA\Property(property="doctor_id", type="integer"),
     *                     @OA\Property(property="advice_admission", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer"),
     *                     @OA\Property(property="count", type="integer"),
     *                     @OA\Property(property="per_page", type="integer"),
     *                     @OA\Property(property="current_page", type="integer"),
     *                     @OA\Property(property="total_pages", type="integer")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->ipdEnrollmentService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_enrollment_details/{id}",
     *     summary="Get single consultation",
     *     tags={"IPD Enrollment"},
     *     description="Fetch a single consultation by ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Consultation ID",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Consultation details fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Consultation fetched successfully."),
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
     *                     @OA\Property(property="name", type="string", example="Dr. John"),
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
            return $this->successResponse($this->ipdEnrollmentService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Consultation not found or not eligible for IPD admission.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/ipd_enrollment_delete/{id}",
     *     tags={"IPD Enrollment"},
     *     summary="Delete IPD record",
     *     description="Delete an IPD admission record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="IPD record ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="IPD record deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="IPD record deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="IPD record not found"
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
            $this->ipdEnrollmentService->delete($id);
            return $this->successResponse(null, 'IPD enrollment deleted successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('IPD enrollment not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
