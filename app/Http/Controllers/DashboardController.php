<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;


use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Traits\ResponseTrait;
use App\Services\ConsultationService;
use App\Services\PatientHelperService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Enums\Appointment\StatusEnum;
use App\Enums\Payment\PaymentStatusEnum;
use App\Enums\RemovedEnums;
use App\Models\Consultations;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="API endpoints for managing dashboard"
 * )
 */

class DashboardController extends Controller
{
    use ResponseTrait;

    private $column;
    private $consultationService;
    private $totalPatientNumberInfo;

    /**
     * Summary of __construct
     * @param \App\Services\PatientHelperService $totalPatientNumberInfo
     * @param \App\Services\ConsultationService $consultationService
     */
    public function __construct(PatientHelperService $totalPatientNumberInfo, ConsultationService $consultationService)
    {
        $this->consultationService = $consultationService;
        $this->totalPatientNumberInfo = $totalPatientNumberInfo;
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     tags={"Dashboard"},
     *     summary="Get dashboard statistics",
     *     description="Returns total users and total patients for dashboard view",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dashboard data fetched successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="totalUsers", type="integer", example=120, description="Total number of users"),
     *                 @OA\Property(property="totalPatients", type="integer", example=87, description="Total number of patients"),
     *                 @OA\Property(property="totalIPD", type="integer", example=15, description="Total IPD count"),
     *                 @OA\Property(property="noOfBedsOccupied", type="integer", example=10, description="Number of beds occupied"),
     *                 @OA\Property(property="totalOPD", type="integer", example=72, description="Total OPD count"),
     *                 @OA\Property(property="totalAppointments", type="integer", example=150, description="Total appointments count"),
     *                 @OA\Property(property="totalPatientsActive", type="integer", example=65, description="Total active patients"),
     *                 @OA\Property(property="consultation", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="appointment_type", type="string", example="Consultation"),
     *                     @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                     @OA\Property(property="doctor_name", type="string", example="Dr. Smith"),
     *                     @OA\Property(property="next_visit_date", type="string", format="date", example="2023-01-01")
     *                 ))
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
    public function all(?Request $request)
    {
        try {
            $dashboardData = [];
            $patient = $this->totalPatientNumberInfo->getTotalPatientNumberInfo();
            $dashboardData['totalIPD'] = 0;
            $dashboardData['noOfBedsOccupied'] = 0;
            $dashboardData['totalUsers'] = User::count();
            // $dashboardData['totalOPD'] = $patient['totalOPD'];
            $dashboardData['totalOPD']=Consultations::where('removed', RemovedEnums::Active->value)->onlyDoctorRelatedIfDoctorLogedIn()->where('status', StatusEnum::Completed->value)->where('payment_status', PaymentStatusEnum::Completed->value)->count();
            $dashboardData['totalPatients'] = $patient['totalPatients'];
            $dashboardData['totalAppointments'] = Appointments::count();
            $dashboardData['totalPatientsActive'] = $patient['totalPatientsActive'];
            $dashboardData['consultation'] = $this->consultationService->allforUpComingAppointments($request);
            return $this->successResponse($dashboardData);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Dashboard data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}

