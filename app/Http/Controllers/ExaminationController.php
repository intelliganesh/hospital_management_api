<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\ExaminationService;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Examinations",
 *     description="API endpoints for managing patient examinations"
 * )
 */
class ExaminationController extends Controller
{
    use ResponseTrait;

    private $examinationService;

    public function __construct(ExaminationService $examinationService)
    {
        $this->examinationService = $examinationService;
    }

    /**
     * @OA\Get(
     *     path="/api/examinations_list",
     *     tags={"Examinations"},
     *     summary="Get all Examinations data",
     *     security={{"bearerAuth": {}}},
     *     description="Get all examinations data",
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
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Examinations retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *                     @OA\Property(property="patient_number", type="string", example="PAT0001"),
     *                     @OA\Property(property="temperature", type="string", example="55 F"),
     *                     @OA\Property(property="bp", type="string", example="120/80"),
     *                     @OA\Property(property="pulse", type="string", example="80")
     *                 )),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=15),
     *                     @OA\Property(property="count", type="integer", example=10),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="total_pages", type="integer", example=2),
     *                     @OA\Property(property="links", type="object",
     *                         @OA\Property(property="next", type="string", example="http://example.com/api/examinations_list?page=2")
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
     *     ),
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->examinationService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/examinations_details/{id}",
     *     summary="Get an examination",
     *     tags={"Examinations"},
     *     security={{"bearerAuth":{}}},
     *     description="Get an examination with the given ID",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Examination's ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Examination data fetched successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="patient_number",
     *                 type="string",
     *                 example="PAT0001"
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="Vishnu"
     *             ),
     *             @OA\Property(
     *                 property="temperature",
     *                 type="string",
     *                 example="55 F"
     *             ),
     *             @OA\Property(
     *                 property="bp",
     *                 type="string",
     *                 example="120/80"
     *             ),
     *             @OA\Property(
     *                 property="pulse",
     *                 type="string",
     *                 example="80"
     *             ),
     *             @OA\Property(
     *                 property="phone_no",
     *                 type="string",
     *                 example="1234567890"
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
     *     ),
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->examinationService->get($id));
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/examinations_update/{id}",
     *     summary="Update an examination",
     *     tags={"Examinations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         description="Examination's ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"patient_id", "appointment_id", "doctor_id", "next_visit_date"},
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *             @OA\Property(property="appointment_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *             @OA\Property(property="doctor_id", type="integer", example=1, description="Doctor's ID"),
     *             @OA\Property(property="temperature", type="string", example="98.6 F"),
     *             @OA\Property(property="bp", type="string", example="120/80"),
     *             @OA\Property(property="pulse", type="string", example="72 bpm"),
     *             @OA\Property(property="cvs", type="string", example="Normal heart sounds, no murmurs"),
     *             @OA\Property(property="rs", type="string", example="Clear breath sounds"),
     *             @OA\Property(property="description", type="string", example="General examination notes"),
     *             @OA\Property(property="examination_overview", type="string", example="Patient appears stable, no acute distress"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Examination updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Examination updated successfully.")
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
            $this->examinationService->update($request, $id);
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
     *
     * @OA\Post(
     *     path="/api/examinations_add",
     *     summary="Create a new examination",
     *     tags={"Examinations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"patient_id", "appointment_id", "doctor_id", "next_visit_date"},
     *             @OA\Property(property="patient_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *             @OA\Property(property="appointment_id", type="string", format="uuid", example="d290f1ee-6c54-4b01-90e6-d701748f0851"),
     *             @OA\Property(property="doctor_id", type="integer", example=1, description="Doctor's ID"),
     *             @OA\Property(property="temperature", type="string", example="98.6 F"),
     *             @OA\Property(property="bp", type="string", example="120/80"),
     *             @OA\Property(property="pulse", type="string", example="72 bpm"),
     *             @OA\Property(property="cvs", type="string", example="Normal heart sounds, no murmurs"),
     *             @OA\Property(property="rs", type="string", example="Clear breath sounds"),
     *             @OA\Property(property="description", type="string", example="General examination notes"),
     *             @OA\Property(property="examination_overview", type="string", example="Patient appears stable, no acute distress"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Examination created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Examination created successfully.")
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
            $this->examinationService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/examinations_delete/{id}",
     *     summary="Delete an examination",
     *     tags={"Examinations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Examination's ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Examination deleted successfully"
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
            $this->examinationService->delete($id);
            return $this->successResponse();
        } catch (NotFoundHttpException $ne) {
            return $this->errorResponse([], $ne->getMessage(), $ne->getStatusCode());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
