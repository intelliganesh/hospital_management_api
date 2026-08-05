<?php

namespace App\Http\Controllers;

use App\Services\IPDAnaesthesiaRecoverObservationService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="IPD Anaesthesia Recovery Observation",
 *     description="API endpoints for managing IPD anaesthesia recovery observation records"
 * )
 */
class IPDAnaesthesiaRecoverObservationController extends Controller
{
    use ResponseTrait;
    private $recoveryService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDAnaesthesiaRecoverObservationService $recoveryService
     */
    public function __construct(IPDAnaesthesiaRecoverObservationService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_recover_observation_list",
     *     summary="Get all IPD anaesthesia recovery observation records",
     *     description="Retrieve a list of all IPD anaesthesia recovery observation records in the system",
     *     tags={"IPD Anaesthesia Recovery Observation"},
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
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *         @OA\Schema(
     *             type="string",
     *             example=""
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
     *         description="A list of IPD anaesthesia recovery observation records",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Recovery observation records retrieved successfully"),
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
            return $this->successResponse($this->recoveryService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_recover_observation_details/{id}",
     *     summary="Get complete IPD anaesthesia recovery observation details",
     *     tags={"IPD Anaesthesia Recovery Observation"},
     *     description="Get complete IPD anaesthesia recovery observation details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the recovery observation record to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful recovery observation details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Recovery observation details successfully fetched."),
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
            return $this->successResponse($this->recoveryService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Recovery observation record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_anaesthesia_recover_observation_add",
     *     summary="Add new IPD anaesthesia recovery observation record",
     *     tags={"IPD Anaesthesia Recovery Observation"},
     *     description="Add a new IPD anaesthesia recovery observation record to the system",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new recovery observation record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
    *                 type="object",
    *                 required={"ipd_id","ipd_surgery_id","ipd_anaesthesia_id"},
    *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
    *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
    *                 @OA\Property(property="ipd_anaesthesia_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901233333"),
    *                 @OA\Property(property="surgical_procedure", type="string", example="Appendectomy"),
    *                 @OA\Property(property="time_patient_received", type="string", format="date-time", example="2026-04-24 10:00:00"),
    *                 @OA\Property(property="monitors", type="string", example="ECG,NIBP,SaO2,ABP"),
    *                 @OA\Property(property="post_operative_complications", type="string", example="Pain,Hypoxia"),
    *                 @OA\Property(property="post_operative_medications", type="string", example="Medication list"),
    *                 @OA\Property(property="patient_score_on_admission", type="string", example="15"),
    *                 @OA\Property(property="patient_score_before_transfer", type="string", example="14"),
    *                 @OA\Property(property="vital_monitoring", type="array", @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="time", type="string", example="2026-04-24 12:00:00"),
    *                     @OA\Property(property="consciousness_level", type="string", example="Alert"),
    *                     @OA\Property(property="resp_rate", type="string", example="18"),
    *                     @OA\Property(property="pulse", type="string", example="80"),
    *                     @OA\Property(property="sao2", type="string", example="98%"),
    *                     @OA\Property(property="bp", type="string", example="120/80"),
    *                     @OA\Property(property="remark", type="string", example="Stable")
    *                 )),
    *                 @OA\Property(property="transfer_to", type="string", example="ICU"),
    *                 @OA\Property(property="time_of_transfer", type="string", format="date-time", example="2026-04-24 14:00:00"),
    *                 @OA\Property(property="pulse_at_shifting", type="string", example="78"),
    *                 @OA\Property(property="sbp_at_shifting", type="string", example="120"),
    *                 @OA\Property(property="dbp_at_shifting", type="string", example="80"),
    *                 @OA\Property(property="rr_at_shifting", type="string", example="16"),
    *                 @OA\Property(property="post_operative_instructions", type="string", example="Monitor vitals every hour"),
    *                 @OA\Property(property="upload_pdf_path", type="string", example="/storage/pdfs/ipd/123/uploads/observation_123.pdf"),
    *                 @OA\Property(property="summary", type="string", example="Patient stable post-operation."),
    *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully added recovery observation record",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Recovery observation record added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The ipd_id field is required."}),
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
            $this->recoveryService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_anaesthesia_recover_observation_update/{id}",
     *     summary="Update IPD anaesthesia recovery observation record",
     *     tags={"IPD Anaesthesia Recovery Observation"},
     *     description="Update an existing IPD anaesthesia recovery observation record's details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the recovery observation record to update",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update recovery observation record details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
    *                 type="object",
    *                 @OA\Property(property="ipd_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567"),
    *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901232222"),
    *                 @OA\Property(property="ipd_anaesthesia_id", type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901233333"),
    *                 @OA\Property(property="surgical_procedure", type="string", example="Appendectomy"),
    *                 @OA\Property(property="time_patient_received", type="string", format="date-time", example="2026-04-24 10:00:00"),
    *                 @OA\Property(property="monitors", type="string", example="ECG,NIBP,SaO2,ABP"),
    *                 @OA\Property(property="post_operative_complications", type="string", example="Pain,Hypoxia"),
    *                 @OA\Property(property="post_operative_medications", type="string", example="Medication list"),
    *                 @OA\Property(property="patient_score_on_admission", type="string", example="15"),
    *                 @OA\Property(property="patient_score_before_transfer", type="string", example="14"),
    *                 @OA\Property(property="vital_monitoring", type="array", @OA\Items(
    *                     type="object",
    *                     @OA\Property(property="time", type="string", example="2026-04-24 12:00:00"),
    *                     @OA\Property(property="consciousness_level", type="string", example="Alert"),
    *                     @OA\Property(property="resp_rate", type="string", example="18"),
    *                     @OA\Property(property="pulse", type="string", example="80"),
    *                     @OA\Property(property="sao2", type="string", example="98%"),
    *                     @OA\Property(property="bp", type="string", example="120/80"),
    *                     @OA\Property(property="remark", type="string", example="Stable")
    *                 )),
    *                 @OA\Property(property="transfer_to", type="string", example="ICU"),
    *                 @OA\Property(property="time_of_transfer", type="string", format="date-time", example="2026-04-24 14:00:00"),
    *                 @OA\Property(property="pulse_at_shifting", type="string", example="78"),
    *                 @OA\Property(property="sbp_at_shifting", type="string", example="120"),
    *                 @OA\Property(property="dbp_at_shifting", type="string", example="80"),
    *                 @OA\Property(property="rr_at_shifting", type="string", example="16"),
    *                 @OA\Property(property="post_operative_instructions", type="string", example="Monitor vitals every hour"),
    *                 @OA\Property(property="upload_pdf_path", type="string", example="/storage/pdfs/ipd/123/uploads/observation_123.pdf"),
    *                 @OA\Property(property="summary", type="string", example="Patient stable post-operation."),
    *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated recovery observation record",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Recovery observation record updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string")),
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
            $this->recoveryService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Recovery observation record not found.');
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
     *     path="/api/ipd_anaesthesia_recover_observation_delete/{id}",
     *     summary="Delete an IPD anaesthesia recovery observation record",
     *     tags={"IPD Anaesthesia Recovery Observation"},
     *     description="Deletes an IPD anaesthesia recovery observation record by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the recovery observation record to be deleted",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Recovery observation record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Recovery observation record deleted successfully."
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
    public function delete(string $id)
    {
        try {
            $this->recoveryService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Recovery observation record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_recover_observation_list_by_ipd/{ipd_id}",
     *     summary="Get all IPD anaesthesia recovery observation records by IPD ID",
     *     tags={"IPD Anaesthesia Recovery Observation"},
     *     description="Retrieve a list of all IPD anaesthesia recovery observation records for a particular IPD",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="ipd_id",
     *          in="path",
     *          required=true,
     *          description="ID of the IPD",
     *          @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901234567")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved recovery observation records for IPD",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid"),
     *                     @OA\Property(property="ipd_id", type="string", format="uuid"),
     *                     @OA\Property(property="surgical_procedure", type="string"),
     *                     @OA\Property(property="monitors", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="IPD recovery observation list"
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
    public function getByIPDId(string $ipd_id)
    {
        try {
            return $this->successResponse($this->recoveryService->getByIPDId($ipd_id));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_recover_observation_list_by_ipd_anaesthesia/{ipd_anaesthesia_id}",
     *     summary="Get all recovery observation records by IPD Anaesthesia ID",
     *     tags={"IPD Anaesthesia Recovery Observation"},
     *     description="Retrieve a list of all recovery observation records for a particular IPD Anaesthesia",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="ipd_anaesthesia_id",
     *          in="path",
     *          required=true,
     *          description="ID of the IPD Anaesthesia",
     *          @OA\Schema(type="string", format="uuid", example="a123b456-7c89-0d12-34e5-678901233333")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved recovery observation records for IPD Anaesthesia",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="IPD recovery observation list")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function getByIPDAnaesthesiaId(string $ipd_anaesthesia_id)
    {
        try {
            return $this->successResponse($this->recoveryService->getByIPDAnaesthesiaId($ipd_anaesthesia_id));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
