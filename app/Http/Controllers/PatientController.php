<?php
namespace App\Http\Controllers;

use App\Interceptors\ServiceInterceptor;
use App\Services\PatientService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Patients",
 *     description="API endpoints for managing patient information, medical records, and related operations"
 * )
 */
class PatientController extends Controller
{
    use ResponseTrait;

    private $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * @OA\Get(
     *     path="/api/patient_statistics",
     *     tags={"Patients"},
     *     summary="Get patient statistics",
     *     security={{"bearerAuth": {}}},
     *     description="Returns statistics about patients including total count and active count",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Patient statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_patients", type="integer", example=120),
     *                 @OA\Property(property="active_patients", type="integer", example=95)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
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
            $statistics = $this->patientService->getStatistics();
            return $this->successResponse($statistics, 'Patient statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/patient_list",
     *     tags={"Patients"},
     *     summary="Get list of patients",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of patients with their details",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Patient details successfully fetched."
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="result",
     *                      type="object",
     *                      @OA\Property(property="first_name", type="string", description="Patient's first name"),
     *                      @OA\Property(property="last_name", type="string", description="Patient's last name"),
     *                      @OA\Property(property="email", type="string", format="email", description="Patient's email address"),
     *                      @OA\Property(property="patient_number", type="string", description="Unique patient number"),
     *                      @OA\Property(property="phone_no", type="string", description="Phone number"),
     *                      @OA\Property(property="dob", type="string", format="date", description="Date of birth"),
     *                      @OA\Property(property="age", type="integer", description="Patient's age"),
     *                      @OA\Property(property="gender", type="string", description="Patient's gender"),
    *                      @OA\Property(property="address", type="string", description="Address details"),
    *                      @OA\Property(property="city", type="string", description="City details"),
    *                      @OA\Property(property="place_of_living", type="string", description="Place of living details"),
    *                      @OA\Property(property="state", type="string", description="State details"),
    *                      @OA\Property(property="country", type="string", description="Country details"),
     *                      @OA\Property(property="maratal_status", type="string", description="Maratal status details"),
     *                      @OA\Property(property="bood_group", type="string", description="Blood group details"),
     *                      @OA\Property(property="insurance_provider", type="string", description="Insurance provider details"),
     *                      @OA\Property(property="insurance_policy_no", type="string", description="Insurance policy number details"),
     *                      @OA\Property(property="refered_by", type="string", description="Refered by details"),
     *                      @OA\Property(property="refered_by_phone_no", type="string", description="Refered by phone number details"),
     *                      @OA\Property(property="refered_to", type="string", description="Refered to details"),
     *                      @OA\Property(property="admission_status", type="string", description="Admition status details"),
     *                      @OA\Property(property="treatment_status", type="string", description="Treatment status details"),
     *                      @OA\Property(property="surgery_status", type="string", description="Surgery status details"),
     *                      @OA\Property(property="emergency_status", type="string", description="Emergency status details"),
     *                      @OA\Property(property="referral_status", type="string", description="Referral status details"),
     *                      @OA\Property(property="payment_status", type="string", description="Payment status details"),
     *                      @OA\Property(property="status", type="string", description="Patient status details"),
     *                  ),
     *              ),
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
            return $this->successResponse($this->patientService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/patient_details/{id}",
     *     tags={"Patients"},
     *     summary="Get list of patients",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of patients with their details",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the user to get patient details",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="019623b3-466b-705e-983b-5ec43d5f6e7a"
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="first_name", type="string", description="Patient's first name"),
     *                 @OA\Property(property="last_name", type="string", description="Patient's last name"),
     *                 @OA\Property(property="email", type="string", format="email", description="Patient's email address"),
     *                 @OA\Property(property="patient_number", type="string", description="Unique patient number"),
     *                 @OA\Property(property="phone_no", type="string", description="Phone number"),
     *                 @OA\Property(property="dob", type="string", format="date", description="Date of birth"),
     *                 @OA\Property(property="age", type="integer", description="Patient's age"),
     *                 @OA\Property(property="gender", type="string", description="Patient's gender"),
     *                 @OA\Property(property="address", type="string", description="Address details"),
     *                 @OA\Property(property="city", type="string", description="City details"),
     *                 @OA\Property(property="state", type="string", description="State details"),
     *                 @OA\Property(property="country", type="string", description="Country details"),
     *                 @OA\Property(property="maratal_status", type="string", description="Maratal status details"),
     *                 @OA\Property(property="bood_group", type="string", description="Blood group details"),
     *                 @OA\Property(property="insurance_provider", type="string", description="Insurance provider details"),
     *                 @OA\Property(property="insurance_policy_no", type="string", description="Insurance policy number details"),
     *                 @OA\Property(property="refered_by", type="string", description="Refered by details"),
     *                 @OA\Property(property="refered_by_phone_no", type="string", description="Refered by phone number details"),
     *                 @OA\Property(property="refered_to", type="string", description="Refered to details"),
     *                 @OA\Property(property="admission_status", type="string", description="Admition status details"),
     *                 @OA\Property(property="treatment_status", type="string", description="Treatment status details"),
     *                 @OA\Property(property="surgery_status", type="string", description="Surgery status details"),
     *                 @OA\Property(property="emergency_status", type="string", description="Emergency status details"),
     *                 @OA\Property(property="referral_status", type="string", description="Referral status details"),
     *                 @OA\Property(property="payment_status", type="string", description="Payment status details"),
     *                 @OA\Property(property="status", type="string", description="Patient status details"),
     *             ),
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
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->patientService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/patient_add",
     *     tags={"Patients"},
     *     summary="Create a new patient",
     *     description="Adds a new patient to the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Patient data",
     *         @OA\JsonContent(
     *             required={"first_name", "age","last_name", "email", "phone_no", "dob", "gender", "address", "city", "state", "country"},
     *             @OA\Property(property="first_name", type="string", example="John", description="Patient's first name"),
     *             @OA\Property(property="last_name", type="string", example="Doe", description="Patient's last name"),
     *             @OA\Property(property="email", type="string", format="email", example="vishnuprakash@intellispiders.com", description="Patient's email address"),
     *             @OA\Property(property="phone_no", type="string", example="7892474770", description="Patient's phone number"),
     *             @OA\Property(property="age", type="string", example="20", description="Patient's age"),
     *             @OA\Property(property="dob", type="string", format="date", example="1980-01-01", description="Patient's date of birth"),
     *             @OA\Property(
     *                 property="gender",
     *                 type="string",
     *                 enum={"male", "female", "other"},
     *                 example="male",
     *                 description="Patient's gender"
     *             ),
    *             @OA\Property(property="address", type="string", example="123 Main St", description="Patient's address"),
    *             @OA\Property(property="city", type="string", example="Anytown", description="Patient's city"),
    *             @OA\Property(property="place_of_living", type="string", example="Village X", description="Patient's place of living"),
    *             @OA\Property(property="state", type="string", example="Anystate", description="Patient's state"),
    *             @OA\Property(property="country", type="string", example="Anycountry", description="Patient's country"),
     *              @OA\Property(property="marital_status", type="string", example="single"),
     *             @OA\Property(
     *                 property="blood_group",
     *                 type="string",
     *                 enum={"A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"},
     *                 example="O+",
     *                 description="Patient's blood group"
     *             ),
     *             @OA\Property(property="insurance_provider", type="string", example="HealthCare Inc.", description="Patient's insurance provider"),
     *             @OA\Property(property="insurance_policy_no", type="string", example="HC123456789", description="Patient's insurance policy number"),
     *             @OA\Property(property="attendant_with_patient_name", type="string", example="Dr. Smith", description="Referring doctor's name"),
     *             @OA\Property(property="attendant_with_patient_phone_no", type="string", example="7892474770", description="Referring doctor's phone number"),
     *             @OA\Property(property="referred_to", type="string", example="Dr. Jones", description="Referred to doctor"),
     *             @OA\Property(property="referred_by", type="string", example="Jones", description="Attender name"),
     *             @OA\Property(
     *                 property="admission_status",
     *                 type="string",
     *                 enum={"Admission Pending", "Admitted", "Discharge Pending", "Discharged", "Closed"},
     *                 example="Admitted",
     *                 description="Patient's admission status"
     *             ),
     *             @OA\Property(
     *                 property="treatment_status",
     *                 type="string",
     *                 enum={"Under Diagnosis", "Test Pending", "Test Completed", "Prescribed", "In Treatment", "Under Observation", "Follow-up Required"},
     *                 example="Test Pending",
     *                 description="Patient's treatment status"
     *             ),
     *             @OA\Property(
     *                 property="surgery_status",
     *                 type="string",
     *                 enum={"Surgery Scheduled", "Surgery In Progress", "Surgery Completed"},
     *                 example="Surgery Scheduled",
     *                 description="Patient's surgery status"
     *             ),
     *             @OA\Property(
     *                 property="emergency_status",
     *                 type="string",
     *                 enum={"Emergency", "Critical", "Stable","Deceased"},
     *                 example="Emergency",
     *                 description="Patient's emergency status"
     *             ),
     *             @OA\Property(
     *                 property="referral_status",
     *                 type="string",
     *                 enum={"Not Referred", "Referred", "Transferred"},
     *                 example="Not Referred",
     *                 description="Patient's referral status"
     *             ),
     *             @OA\Property(
     *                 property="payment_status",
     *                 type="string",
     *                 enum={"Payment Pending", "Payment Completed"},
     *                 example="Payment Completed",
     *                 description="Patient's payment status"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"Active", "Inactive", "Pending", "Cancelled", "Approved", "Completed", "Draft", "Resolved", "Unresolved"},
     *                 example="Active",
     *                 description="Patient's current status"
     *             ),
     *            @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000Z", description="Patient's creation timestamp")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Patient created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient created successfully."),
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
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->patientService);
            return $this->successResponse($proxiedService->createPatient($request));
            // // $proxiedService->create($request);
            // // $this->patientService->create($request);
            // // return $this->successResponse(['id' => $proxiedService->createPatient($request)]);
            // // $this->patientService->create($request);
            // return $this->successResponse(['id' => $proxiedService->createPatient($request)]);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/patient_edit/{id}",
     *     tags={"Patients"},
     *     summary="Update an existing patient",
     *     description="Updates an existing patient in the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the patient to update",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="019623b3-466b-705e-983b-5ec43d5f6e7a"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Patient data to update",
     *         @OA\JsonContent(
     *             required={"first_name","age", "last_name", "email", "phone_no", "dob", "gender", "address", "city", "state", "country"},
     *             @OA\Property(property="first_name", type="string", example="John", description="Patient's first name"),
     *             @OA\Property(property="last_name", type="string", example="Doe", description="Patient's last name"),
     *             @OA\Property(property="age", type="string", example="20", description="Patient's age"),
     *             @OA\Property(property="email", type="string", format="email", example="vishnuprakash@intellispiders.com", description="Patient's email address"),
     *             @OA\Property(property="phone_no", type="string", example="7892474770", description="Patient's phone number"),
     *             @OA\Property(property="dob", type="string", format="date", example="1980-01-01", description="Patient's date of birth"),
     *             @OA\Property(
     *                 property="gender",
     *                 type="string",
     *                 enum={"male", "female", "other"},
     *                 example="male",
     *                 description="Patient's gender"
     *             ),
     *             @OA\Property(property="address", type="string", example="123 Main St", description="Patient's address"),
     *             @OA\Property(property="city", type="string", example="Anytown", description="Patient's city"),
     *             @OA\Property(property="state", type="string", example="Anystate", description="Patient's state"),
     *             @OA\Property(property="country", type="string", example="Anycountry", description="Patient's country"),
     *             @OA\Property(property="marital_status", type="string", example="Single", description="Patient's marital status"),
     *             @OA\Property(
     *                 property="blood_group",
     *                 type="string",
     *                 enum={"A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"},
     *                 example="O+",
     *                 description="Patient's blood group"
     *             ),
     *             @OA\Property(property="insurance_provider", type="string", example="HealthCare Inc.", description="Patient's insurance provider"),
     *             @OA\Property(property="insurance_policy_no", type="string", example="HC123456789", description="Patient's insurance policy number"),
     *             @OA\Property(property="attendant_with_patient_name", type="string", example="Dr. Smith", description="Referring doctor's name"),
     *             @OA\Property(property="attendant_with_patient_phone_no", type="string", example="7892474770", description="Referring doctor's phone number"),
     *             @OA\Property(property="referred_to", type="string", example="Dr. Jones", description="Referred to doctor"),
     *             @OA\Property(property="referred_by", type="string", example="Jones", description="Attender name"),
     *             @OA\Property(
     *                 property="admission_status",
     *                 type="string",
     *                 enum={"Admission Pending", "Admitted", "Discharge Pending", "Discharged", "Closed"},
     *                 example="Admitted",
     *                 description="Patient's admission status"
     *             ),
     *             @OA\Property(
     *                 property="treatment_status",
     *                 type="string",
     *                 enum={"Under Diagnosis", "Test Pending", "Test Completed", "Prescribed", "In Treatment", "Under Observation", "Follow-up Required"},
     *                 example="Test Pending",
     *                 description="Patient's treatment status"
     *             ),
     *             @OA\Property(
     *                 property="surgery_status",
     *                 type="string",
     *                 enum={"Surgery Scheduled", "Surgery In Progress", "Surgery Completed"},
     *                 example="Surgery Scheduled",
     *                 description="Patient's surgery status"
     *             ),
     *             @OA\Property(
     *                 property="emergency_status",
     *                 type="string",
     *                 enum={"Emergency", "Critical", "Stable","Deceased"},
     *                 example="Emergency",
     *                 description="Patient's emergency status"
     *             ),
     *             @OA\Property(
     *                 property="referral_status",
     *                 type="string",
     *                 enum={"Not Referred", "Referred", "Transferred"},
     *                 example="Not Referred",
     *                 description="Patient's referral status"
     *             ),
     *             @OA\Property(
     *                 property="payment_status",
     *                 type="string",
     *                 enum={"Payment Pending", "Payment Completed"},
     *                 example="Payment Completed",
     *                 description="Patient's payment status"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"Active", "Inactive", "Pending", "Cancelled", "Approved", "Completed", "Draft", "Resolved", "Unresolved"},
     *                 example="Active",
     *                 description="Patient's current status"
     *             ),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000Z", description="Patient's creation timestamp")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient_id", type="string", format="uuid", example="a5f8d4e2-3c4b-4f39-8f2a-1234567890ab", description="ID of the updated patient")
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
     *         description="Patient not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */

    public function update(Request $request, string | null $id)
    {
        try {
            // $this->patientService->update($request, $id);
            $proxiedService = ServiceInterceptor::intercept($this->patientService);
            return $this->successResponse($proxiedService->updatePatient($request, $id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient data not found.');
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
     *     path="/api/patient_delete/{id}",
     *     tags={"Patients"},
     *     summary="Delete an existing patient",
     *     description="Removes a patient from the system by their unique identifier",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the patient to be deleted",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="019623b3-466b-705e-983b-5ec43d5f6e7a"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Patient not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Patient not found.")
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
    public function destroy(string $id)
    {
        try {
            $payment=\App\Models\Payment::where(['patient_id'=>$id,'payment_status'=>'Pending'])->exists();
            $consultation=\App\Models\Consultations::where(['patient_id'=>$id,'status'=>'Pending'])->exists();
            if($payment || $consultation){
                return $this->errorResponse(
                    ['There is a pending invoice or consultation'],
                    "Patient cannot be deleted. There is a pending invoice or consultation.",
                    400
                );
            }
            $this->patientService->delete($id);
            return $this->successResponse();
           
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/patients/{id}/download",
     *     summary="Download IPD form PDF for a patient",
     *     tags={"Patients"},
     *     security={{"bearerAuth": {}}},
     *     description="Generates and downloads the IPD form (part 1) as a PDF for a given patient ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Patient ID",
     *         @OA\Schema(
     *             type="string",
     *             format="uuid",
     *             example="019623b3-466b-705e-983b-5ec43d5f6e7a"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF file downloaded successfully",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/pdf",
     *                 @OA\Schema(type="string", format="binary")
     *             )
     *         }
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
     *         description="Patient not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function download(string $id)
    {
        try {
            return $this->successResponse(['url' => $this->patientService->download($id)]);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (InvalidArgumentException $invalidArgument) {
            return $this->errorResponse(
                [],
                $invalidArgument->getMessage(),
                400
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    // /**
    //  * @OA\Get(
    //  *     path="/api/patients/{id}/anaesthesia_download",
    //  *     summary="Download anaesthesia form PDF for a patient",
    //  *     tags={"Patients"},
    //  *     security={{"bearerAuth": {}}},
    //  *     description="Generates and downloads the IPD form (part 1) as a PDF for a given patient ID.",
    //  *     @OA\Parameter(
    //  *         name="id",
    //  *         in="path",
    //  *         required=true,
    //  *         description="Patient ID",
    //  *         @OA\Schema(
    //  *             type="string",
    //  *             format="uuid",
    //  *             example="019623b3-466b-705e-983b-5ec43d5f6e7a"
    //  *         )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="PDF file downloaded successfully",
    //  *         content={
    //  *             @OA\MediaType(
    //  *                 mediaType="application/pdf",
    //  *                 @OA\Schema(type="string", format="binary")
    //  *             )
    //  *         }
    //  *     ),
    //  *     @OA\Response(
    //  *         response=400,
    //  *         description="Invalid input"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=401,
    //  *         description="Unauthenticated"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=404,
    //  *         description="Patient not found"
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         description="Internal server error"
    //  *     )
    //  * )
    //  */
    public function anaesthesiaForm(string $id): mixed
    {
        try {
            return $this->successResponse(['url' => $this->patientService->anaesthesiaForm($id)]);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (InvalidArgumentException $invalidArgument) {
            return $this->errorResponse(
                [],
                $invalidArgument->getMessage(),
                400
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
