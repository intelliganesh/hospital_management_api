<?php

namespace App\Http\Controllers;

use App\Services\IPDAnaesthesiaDepartmentService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="IPD Anaesthesia Department",
 *     description="API endpoints for managing IPD intraoperative anaesthesia records"
 * )
 */
class IPDAnaesthesiaDepartmentController extends Controller
{
    use ResponseTrait;
    private $anaesthesiaDeptService;

    /**
     * Summary of __construct
     * @param \App\Services\IPDAnaesthesiaDepartmentService $anaesthesiaDeptService
     */
    public function __construct(IPDAnaesthesiaDepartmentService $anaesthesiaDeptService)
    {
        $this->anaesthesiaDeptService = $anaesthesiaDeptService;
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_department_list",
     *     summary="Get all IPD anaesthesia department records",
     *     description="Retrieve a list of all intraoperative anaesthesia records",
     *     tags={"IPD Anaesthesia Department"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by",
     *         @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *      ),
     *     @OA\Parameter(
     *          name="per_page",
     *          in="query",
     *          required=false,
     *          description="Number of items per page",
     *         @OA\Schema(type="integer", example=10)
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of anaesthesia department records",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Anaesthesia department records retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->anaesthesiaDeptService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_department_details/{id}",
     *     summary="Get complete IPD anaesthesia department details",
     *     tags={"IPD Anaesthesia Department"},
     *     description="Get complete intraoperative anaesthesia record details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the record to get details",
     *          @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful record details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Anaesthesia department details successfully fetched."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->anaesthesiaDeptService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Anaesthesia department record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/ipd_anaesthesia_department_add",
     *     summary="Add new anaesthesia department record",
     *     tags={"IPD Anaesthesia Department"},
     *     description="Add a new intraoperative anaesthesia record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add new anaesthesia department record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"ipd_id","ipd_surgery_id","ipd_anaesthesia_id"},
     *                 @OA\Property(property="ipd_id", type="string", format="uuid"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid"),
     *                 @OA\Property(property="ipd_anaesthesia_id", type="string", format="uuid"),
     *                 @OA\Property(property="pre_anaesthesia_state", type="string"),
     *                 @OA\Property(property="ventilated_patient", type="string"),
     *                 @OA\Property(property="npo_status", type="string"),
     *                 @OA\Property(property="patient_safety", type="string"),
     *                 @OA\Property(property="pre_oxygenation", type="string"),
     *                 @OA\Property(property="induction", type="string"),
     *                 @OA\Property(property="laryngoscopy", type="string"),
     *                 @OA\Property(property="difficult_intubation", type="boolean"),
     *                 @OA\Property(property="endotracheal_tube", type="string"),
     *                 @OA\Property(property="endotracheal_tube_size", type="string"),
     *                 @OA\Property(property="endotracheal_tube_fixed_at", type="string"),
     *                 @OA\Property(property="endotracheal_tube_type", type="string"),
     *                 @OA\Property(property="airway", type="string"),
     *                 @OA\Property(property="airway_size", type="string"),
     *                 @OA\Property(property="mask_anaesthesia", type="string"),
     *                 @OA\Property(property="throat_pack", type="string"),
     *                 @OA\Property(property="nasogastric_tube", type="string"),
     *                 @OA\Property(property="maintenance", type="string"),
     *                 @OA\Property(property="iv_access", type="string"),
     *                 @OA\Property(property="central_blocks_spinal", type="string"),
     *                 @OA\Property(property="central_blocks_epidural", type="string"),
     *                 @OA\Property(property="central_blocks_epidural_g", type="string"),
     *                 @OA\Property(property="central_blocks_spinal_needle_g", type="string"),
     *                 @OA\Property(property="regional_blocks", type="string"),
     *                 @OA\Property(property="nerve_stimulator", type="string"),
     *                 @OA\Property(property="regional_supplements", type="string"),
     *                 @OA\Property(property="drugs_regional", type="string"),
     *                 @OA\Property(property="monitoring", type="string"),
     *                 @OA\Property(property="temperature", type="string"),
     *                 @OA\Property(property="crystalloids_ml", type="integer"),
     *                 @OA\Property(property="colloids_ml", type="integer"),
     *                 @OA\Property(property="blood_ml", type="integer"),
     *                 @OA\Property(property="anaesthesia_technique_brief", type="string"),
     *                 @OA\Property(property="summary", type="string"),
     *                 @OA\Property(property="abp_details", type="string"),
     *                 @OA\Property(property="cvp_details", type="string"),
     *                 @OA\Property(property="upload_pdf_path", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully added record",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Anaesthesia department record added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function create(Request $request)
    {
        try {
            $this->anaesthesiaDeptService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ipd_anaesthesia_department_update/{id}",
     *     summary="Update anaesthesia department record",
     *     tags={"IPD Anaesthesia Department"},
     *     description="Update an existing intraoperative anaesthesia record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the record to update",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update anaesthesia department record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="ipd_id", type="string", format="uuid"),
     *                 @OA\Property(property="ipd_surgery_id", type="string", format="uuid"),
     *                 @OA\Property(property="ipd_anaesthesia_id", type="string", format="uuid"),
     *                 @OA\Property(property="pre_anaesthesia_state", type="string"),
     *                 @OA\Property(property="ventilated_patient", type="string"),
     *                 @OA\Property(property="npo_status", type="string"),
     *                 @OA\Property(property="patient_safety", type="string"),
     *                 @OA\Property(property="pre_oxygenation", type="string"),
     *                 @OA\Property(property="induction", type="string"),
     *                 @OA\Property(property="laryngoscopy", type="string"),
     *                 @OA\Property(property="difficult_intubation", type="boolean"),
     *                 @OA\Property(property="endotracheal_tube", type="string"),
     *                 @OA\Property(property="endotracheal_tube_size", type="string"),
     *                 @OA\Property(property="endotracheal_tube_fixed_at", type="string"),
     *                 @OA\Property(property="endotracheal_tube_type", type="string"),
     *                 @OA\Property(property="mask_anaesthesia", type="string"),
     *                 @OA\Property(property="throat_pack", type="string"),
     *                 @OA\Property(property="nasogastric_tube", type="string"),
     *                 @OA\Property(property="maintenance", type="string"),
     *                 @OA\Property(property="iv_access", type="string"),
     *                 @OA\Property(property="central_blocks_spinal", type="string"),
     *                 @OA\Property(property="central_blocks_epidural", type="string"),
     *                 @OA\Property(property="regional_blocks", type="string"),
     *                 @OA\Property(property="nerve_stimulator", type="string"),
     *                 @OA\Property(property="regional_supplements", type="string"),
     *                 @OA\Property(property="drugs_regional", type="string"),
     *                 @OA\Property(property="monitoring", type="string"),
     *                 @OA\Property(property="temperature", type="string"),
     *                 @OA\Property(property="crystalloids_ml", type="integer"),
     *                 @OA\Property(property="colloids_ml", type="integer"),
     *                 @OA\Property(property="blood_ml", type="integer"),
     *                 @OA\Property(property="anaesthesia_technique_brief", type="string"),
     *                 @OA\Property(property="summary", type="string"),
     *                 @OA\Property(property="abp_details", type="string"),
     *                 @OA\Property(property="cvp_details", type="string"),
     *                 @OA\Property(property="upload_pdf_path", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated record",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Anaesthesia department record updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $this->anaesthesiaDeptService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Anaesthesia department record not found.');
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
     *     path="/api/ipd_anaesthesia_department_delete/{id}",
     *     summary="Delete anaesthesia department record",
     *     tags={"IPD Anaesthesia Department"},
     *     description="Delete an intraoperative anaesthesia record",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the record to delete",
     *         @OA\Schema(type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Anaesthesia department record deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function delete(string $id)
    {
        try {
            $this->anaesthesiaDeptService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Anaesthesia department record not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_department_list_by_ipd/{ipd_id}",
     *     summary="Get all anaesthesia department records by IPD ID",
     *     tags={"IPD Anaesthesia Department"},
     *     description="Retrieve a list of all intraoperative anaesthesia records for a particular IPD",
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
     *         description="Successfully retrieved anaesthesia department records for IPD",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="IPD anaesthesia department list")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function getByIPDId(string $ipd_id)
    {
        try {
            return $this->successResponse($this->anaesthesiaDeptService->getByIPDId($ipd_id));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ipd_anaesthesia_department_list_by_ipd_anaesthesia/{ipd_anaesthesia_id}",
     *     summary="Get all anaesthesia department records by IPD Anaesthesia ID",
     *     tags={"IPD Anaesthesia Department"},
     *     description="Retrieve a list of all intraoperative anaesthesia records for a particular IPD Anaesthesia",
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
     *         description="Successfully retrieved anaesthesia department records for IPD Anaesthesia",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="IPD anaesthesia department list")
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
            return $this->successResponse($this->anaesthesiaDeptService->getByIPDAnaesthesiaId($ipd_anaesthesia_id));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
