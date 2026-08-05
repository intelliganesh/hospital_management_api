<?php

namespace App\Http\Controllers;

use App\Services\PatientFistulaService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Patient Fistula",
 *     description="API endpoints for managing patient fistula records"
 * )
 */
class PatientFistulaController extends Controller
{
    use ResponseTrait;
    private $patientFistulaService;

    public function __construct(PatientFistulaService $patientFistulaService)
    {
        $this->patientFistulaService = $patientFistulaService;
    }

    /**
     * @OA\Get(
     *     path="/api/patient_fistula_list",
     *     summary="Get all patient fistulas",
     *     description="Retrieve a list of all patient fistulas in the system",
     *     tags={"Patient Fistula"},
     *     security={{"bearerAuth": {}}},
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
     *          name="patient_id",
     *          in="query",
     *          required=false,
     *          description="Filter by patient ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example="created_at"
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
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Number of items per page",
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of patient fistulas",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient fistulas retrieved successfully"),
     *             @OA\Property(property="data", type="object")
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
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->patientFistulaService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/patient_fistula_details/{id}",
     *     summary="Get patient fistula details",
     *     tags={"Patient Fistula"},
     *     description="Get complete patient fistula details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the patient fistula to get details",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful patient fistula details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient fistula details successfully fetched."),
     *             @OA\Property(property="data", type="object")
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
            return $this->successResponse($this->patientFistulaService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient fistula data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/patient_fistula_add",
     *     summary="Add patient fistula",
     *     tags={"Patient Fistula"},
     *     description="Add a new patient fistula record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new patient fistula record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"patient_id"},
     *                 @OA\Property(property="patient_id", type="integer", example=1),
     *                 @OA\Property(property="no_of_fistula", type="string", example="1"),
     *                 @OA\Property(property="no_of_tracks_in_one_fistula", type="string", example="2"),
     *                 @OA\Property(property="no_of_external_opening_position", type="string", example="2"),
     *                 @OA\Property(property="external_opening_position", type="string", example="3#6"),
     *                 @OA\Property(property="internal_opening_position", type="string", example="12"),
     *                 @OA\Property(property="any_other", type="string", example="note1#note2"),
     *                 @OA\Property(property="no_of_secondary_opening_position", type="string", example="1"),
     *                 @OA\Property(property="secondary_opening_position", type="string", example="9"),
     *                 @OA\Property(property="secondary_anal_valve", type="string", example="6#9"),
     *                 @OA\Property(property="other_investigation", type="string", example="investigation1#investigation2"),
     *                 @OA\Property(property="anal_valve", type="string", example="valve1#valve2"),
     *                 @OA\Property(property="type_of_crypt", type="string", example="crypt1#crypt2"),
     *                 @OA\Property(property="crypt_cause", type="string", example="cause1#cause2"),
     *                 @OA\Property(property="type_of_fistula_position", type="string", example="position1#position2"),
     *                 @OA\Property(property="type_of_fistula_sphincter", type="string", example="sphincter1#sphincter2"),
     *                 @OA\Property(property="basis_of_high_low_riding", type="string", example="high#low"),
     *                 @OA\Property(property="distant_visceral_communication", type="string", example="communication1#communication2"),
     *                 @OA\Property(property="sono_fistula_gram", type="string", example="sono_result"),
     *                 @OA\Property(property="mri_fistula_gram", type="string", example="mri_result"),
     *                 @OA\Property(property="sonologist_findings", type="string", example="findings text"),
     *                 @OA\Property(property="fistula_recurrence", type="string", example="yes"),
     *                 @OA\Property(property="fistula_recurrence_surgery_count", type="string", example="2"),
     *                 @OA\Property(property="fistula_remark", type="string", example="remark text"),
     *                 @OA\Property(property="posterior_fistulous_angle", type="string", example="45"),
     *                 @OA\Property(property="sonologist", type="string", example="Dr. Smith")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully added patient fistula",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully received")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The patient_id field is required."})
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
    public function create(Request $request)
    {
        try {
            $this->patientFistulaService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/patient_fistula_update/{id}",
     *     summary="Update patient fistula",
     *     tags={"Patient Fistula"},
     *     description="Update patient fistula details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Update by Id for patient fistula",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update patient fistula details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="patient_id", type="integer", example=1),
     *                 @OA\Property(property="no_of_fistula", type="string", example="1"),
     *                 @OA\Property(property="no_of_tracks_in_one_fistula", type="string", example="2"),
     *                 @OA\Property(property="no_of_external_opening_position", type="string", example="2"),
     *                 @OA\Property(property="external_opening_position", type="string", example="3#6"),
     *                 @OA\Property(property="internal_opening_position", type="string", example="12"),
     *                 @OA\Property(property="any_other", type="string", example="note1#note2"),
     *                 @OA\Property(property="no_of_secondary_opening_position", type="string", example="1"),
     *                 @OA\Property(property="secondary_opening_position", type="string", example="9"),
     *                 @OA\Property(property="secondary_anal_valve", type="string", example="6#9"),
     *                 @OA\Property(property="other_investigation", type="string", example="investigation1#investigation2"),
     *                 @OA\Property(property="anal_valve", type="string", example="valve1#valve2"),
     *                 @OA\Property(property="type_of_crypt", type="string", example="crypt1#crypt2"),
     *                 @OA\Property(property="crypt_cause", type="string", example="cause1#cause2"),
     *                 @OA\Property(property="type_of_fistula_position", type="string", example="position1#position2"),
     *                 @OA\Property(property="type_of_fistula_sphincter", type="string", example="sphincter1#sphincter2"),
     *                 @OA\Property(property="basis_of_high_low_riding", type="string", example="high#low"),
     *                 @OA\Property(property="distant_visceral_communication", type="string", example="communication1#communication2"),
     *                 @OA\Property(property="sono_fistula_gram", type="string", example="sono_result"),
     *                 @OA\Property(property="mri_fistula_gram", type="string", example="mri_result"),
     *                 @OA\Property(property="sonologist_findings", type="string", example="findings text"),
     *                 @OA\Property(property="fistula_recurrence", type="string", example="yes"),
     *                 @OA\Property(property="fistula_recurrence_surgery_count", type="string", example="2"),
     *                 @OA\Property(property="fistula_remark", type="string", example="remark text"),
     *                 @OA\Property(property="posterior_fistulous_angle", type="string", example="45"),
     *                 @OA\Property(property="sonologist", type="string", example="Dr. Smith")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful patient fistula update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient fistula updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"))
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
            $this->patientFistulaService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient fistula data not found.');
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
     *     path="/api/patient_fistula_delete/{id}",
     *     summary="Delete patient fistula",
     *     tags={"Patient Fistula"},
     *     description="Deletes a patient fistula by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the patient fistula to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient fistula successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Patient fistula deleted successfully.")
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
    public function destroy(string $id)
    {
        try {
            $this->patientFistulaService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Patient fistula data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/patient_fistula_by_patient/{patientId}",
     *     summary="Get patient fistulas by patient ID",
     *     tags={"Patient Fistula"},
     *     description="Get all fistula records for a specific patient",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="patientId",
     *         in="path",
     *         required=true,
     *         description="Patient ID to get fistula records",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient fistulas retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
    public function getByPatientId(string $patientId)
    {
        try {
            return $this->successResponse($this->patientFistulaService->getByPatientId($patientId));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
