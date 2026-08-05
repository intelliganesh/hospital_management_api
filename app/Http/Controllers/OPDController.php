<?php
namespace App\Http\Controllers;

use App\Interceptors\ServiceInterceptor;
use App\Services\CheckValidation;
use App\Services\OPDService;
use App\Traits\OPDValidation;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="OPD",
 *     description="OPD (Outpatient Department) operations"
 * )
 */
class OPDController extends Controller
{
    use ResponseTrait;
    use OPDValidation;
    private $opdService;
    protected $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\OPDService $opdService
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(OPDService $opdService, CheckValidation $checkValidationService)
    {
        $this->opdService             = $opdService;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * @OA\Get(
     *     path="/api/opd_list",
     *     summary="Get all opd's",
     *     description="Retrieve a list of all opd's in the system",
     *     tags={"OPD"},
     *     security={{"bearerAuth": {}}},
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
     *          description="Field to sort",
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
     *     @OA\Response(
     *         response=200,
     *         description="A list of OPD's",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="OPD records retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="string", example="01967bde-5355-722c-83f7-3bac0b36464e"),
     *                     @OA\Property(property="opd_number", type="string", example="OPD-00001"),
     *                     @OA\Property(property="patient_id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *                     @OA\Property(property="status", type="string", example="Pending"),
     *                     @OA\Property(property="visit_date", type="string", example="2024-11-20"),
     *                     @OA\Property(property="patient_name", type="string", example="John Doe"),
     *                     @OA\Property(property="doctor_name", type="string", example="Dr. Smith")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string", example="http://localhost/api/opd/list?page=1"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="last_page_url", type="string", example="http://localhost/api/opd/list?page=5"),
     *                 @OA\Property(property="next_page_url", type="string", example="http://localhost/api/opd/list?page=2"),
     *                 @OA\Property(property="path", type="string", example="http://localhost/api/opd/list"),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="prev_page_url", type="string", example=null),
     *                 @OA\Property(property="to", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
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
    public function all(Request $request)
    {
        try {
            return $this->successResponse($this->opdService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/opd_details/{id}",
     *     tags={"OPD"},
     *     summary="Get details of opd",
     *     security={{"bearerAuth": {}}},
     *     description="Returns details of opd",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the OPD to get details",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="01967bde-5355-722c-83f7-3bac0b36464e"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success",
     *                 description="Response status"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Request processed successfully",
     *                 description="Response message"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="opd",
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="01967bde-5355-722c-83f7-3bac0b36464e", description="OPD ID"),
     *                     @OA\Property(property="opd_number", type="string", example="OPD0001", description="OPD Number"),
     *                     @OA\Property(property="patient_id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd", description="Patient ID"),
     *                     @OA\Property(property="appointment_id", type="string", example="01967bd2-1dc5-7079-9e39-8bb8449848a2", description="Appointment ID"),
     *                     @OA\Property(property="status", type="string", example="Pending", description="OPD status"),
     *                     @OA\Property(property="visit_date", type="string", format="date-time", example="2024-11-20T05:00:00.000000Z", description="Visit date"),
     *                     @OA\Property(property="complaint", type="string", example="Fever and cough", description="Complaint"),
     *                     @OA\Property(property="referred_to_doctor_id", type="integer", example=1, description="Referred doctor ID"),
     *                     @OA\Property(
     *                         property="patient",
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd", description="Patient ID"),
     *                         @OA\Property(property="patient_first_name", type="string", description="Patient first name"),
     *                         @OA\Property(property="patient_last_name", type="string", description="Patient last name"),
     *                         @OA\Property(property="patient_email", type="string", description="Patient email"),
     *                         @OA\Property(property="patient_number", type="string", description="Patient number"),
     *                         @OA\Property(property="patient_dob", type="string", description="Patient dob"),
     *                         @OA\Property(property="patient_age", type="string", description="Patient age"),
     *                         @OA\Property(property="patient_gender", type="string", description="Patient gender"),
     *                         @OA\Property(property="patient_address", type="string", description="Patient address"),
     *                         @OA\Property(property="patient_city", type="string", description="Patient city"),
     *                         @OA\Property(property="patient_state", type="string", description="Patient state"),
     *                         @OA\Property(property="patient_country", type="string", description="Patient country"),
     *                         @OA\Property(property="patient_pincode", type="string", description="Patient pincode"),
     *                         @OA\Property(property="patient_insurance_provider", type="string", description="Patient insurance provider"),
     *                         @OA\Property(property="patient_insurance_policy_no", type="string", description="Patient insurance policy no"),
     *                         @OA\Property(property="patient_phone_no", type="string", description="Patient phone number")
     *                     ),
     *                     @OA\Property(
     *                         property="appointment",
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd", description="Appointment ID"),
     *                         @OA\Property(property="number", type="string", example="APT0001", description="Appointment number")
     *                     ),
     *                     @OA\Property(
     *                         property="doctor",
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd", description="Doctor ID"),
     *                         @OA\Property(property="name", type="string", example="Vishnu", description="Doctor name")
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
            return $this->successResponse($this->opdService->get($id));
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/opd_add",
     *     tags={"OPD"},
     *     summary="Create a new OPD",
     *     description="Adds a new OPD to the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="OPD data",
     *         @OA\JsonContent(
     *             required={"patient_id", "status", "visit_date", "referred_to_doctor_id"},
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851", description="Patient's UUID"),
     *             @OA\Property(property="appointment_id", type="string", format="uuid", nullable=true, example="9b0a2c47-2d5e-4c09-8886-3ee6624d0222", description="Appointment UUID (optional)"),
     *             @OA\Property(property="status", type="string", example="Pending", description="Status of the OPD visit (Pending, Completed, Converted to IPD, Cancelled)"),
     *             @OA\Property(property="visit_date", type="string", format="date-time", example="2024-11-20 10:30:00", description="Patient visit date in Y-m-d H:i:s format"),
     *             @OA\Property(property="complaint", type="string", example="Fever and cough", description="Patient's complaint"),
     *             @OA\Property(property="referred_to_doctor_id", type="integer", example=1, description="Referred doctor ID (from users table)"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OPD created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="OPD created successfully."),
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
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function create(Request $request)
    {
        try {
            $validate = $this->validate($request);
            $this->checkValidationService->checkValidation($validate);
            $this->opdService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/opd_edit/{id}",
     *     tags={"OPD"},
     *     summary="Update a OPD fields",
     *     description="Updates an existing OPD record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the OPD record to update",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="01967bde-5355-722c-83f7-3bac0b36464e"
     *         )
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="OPD data",
     *         @OA\JsonContent(
     *             required={"patient_id", "status", "visit_date", "referred_to_doctor_id"},
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851", description="Patient's UUID"),
     *             @OA\Property(property="appointment_id", type="string", format="uuid", nullable=true, example="9b0a2c47-2d5e-4c09-8886-3ee6624d0222", description="Appointment UUID (optional)"),
     *             @OA\Property(property="status", type="string", example="Pending", description="Status of the OPD visit (Pending, Completed, Converted to IPD, Cancelled)"),
     *             @OA\Property(property="visit_date", type="string", format="date-time", example="2024-11-20 10:30:00", description="Patient visit date in Y-m-d H:i:s format"),
     *             @OA\Property(property="complaint", type="string", example="Fever and cough", description="Patient's complaint"),
     *             @OA\Property(property="referred_to_doctor_id", type="integer", example=1, description="Referred doctor ID (from users table)"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OPD created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="OPD created successfully."),
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
     *         response=500,
     *         description="Internal server error"
     *     ),
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $validate = $this->validate($request, true, $id);
            $this->checkValidationService->checkValidation($validate);
            $this->opdService->update($request, $id);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/opd_delete/{id}",
     *     summary="Delete a opd",
     *     tags={"OPD"},
     *     description="Deletes a opd by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the opd to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OPD successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="OPD deleted successfully")
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
            $this->opdService->delete($id);
            return $this->successResponse();
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($ne);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/doctor_list_show",
     *     summary="Get all doctors",
     *     description="Retrieve a list of all opd's in the system",
     *     tags={"OPD"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of doctors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Doctors retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *                 @OA\Property(property="name", type="string", example="Dr. John Smith")
     *             ))
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
    public function show()
    {
        try {
            return $this->successResponse($this->opdService->show());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/opd_pua_list",
     *     tags={"OPD"},
     *     summary="Get list of users, patients and appointments",
     *     description="Retrieve a list of users, patients and appointments in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lists of users, patients and appointments",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(
     *                     property="userList",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Dr. John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="patientList",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="0196487c-5246-71a9-a7d0-32f5d472e6dd"),
     *                         @OA\Property(property="patient_number", type="string", example="PAT-0001"),
     *                         @OA\Property(property="patient_first_name", type="string", example="John"),
     *                         @OA\Property(property="patient_last_name", type="string", example="Doe")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="appointmentList",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="string", example="01967bd2-1dc5-7079-9e39-8bb8449848a2"),
     *                         @OA\Property(property="appointment_number", type="string", example="APO-0001"),
     *                         @OA\Property(property="patient_name", type="string", example="John Doe")
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
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function getList()
    {
        try {
            return $this->successResponse($this->opdService->getList());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function opdToIpd(string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->opdService);
            return $this->successResponse(['id' => $proxiedService->opdToIpd($id)]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
