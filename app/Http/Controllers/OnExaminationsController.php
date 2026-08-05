<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\Master\OnExaminationsService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="On Examinations",
 *     description="API endpoints for managing on examinations"
 * )
 */
class OnExaminationsController extends Controller
{

    use ResponseTrait;

    private $onExaminationsService;


    /**
     * Summary of __construct
     * @param 
     */
    public function __construct(OnExaminationsService $onExaminationsService)
    {
        $this->onExaminationsService = $onExaminationsService;

    }

    /**
     * @OA\Get(
     *     path="/api/on_examinations_list",
     *     summary="Get all onExaminationss",
     *     description="Retrieve a list of all onExaminationss in the system",
     *     tags={"On Examinations"},
     *     security={{"bearerAuth": {}}},
     *       @OA\Parameter(
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
     *     @OA\Response(
     *         response=200,
     *         description="A list of on examinations",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="On examinations retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Examination Name"),
     *                     @OA\Property(property="department", type="string", example="Proctology"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )),
     *                 @OA\Property(property="first_page_url", type="string"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="last_page_url", type="string"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
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
            return $this->successResponse($this->onExaminationsService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/on_examinations_details/{id}",
     *     summary="Get complete onExaminations details",
     *     tags={"On Examinations"},
     *     description="Get complete onExaminations details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the onExaminations to get onExaminations details",
     *          @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful on examination details retrieval",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="On examination details successfully fetched"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Examination Name"),
     *                 @OA\Property(property="department", type="string", example="Proctology"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
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
            return $this->successResponse($this->onExaminationsService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('OnExaminations data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/on_examinations_add",
     *     summary="onExaminations add",
     *     tags={"On Examinations"},
     *     description="Add a new on examination record",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add a new on examination record",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={},
     *                 @OA\Property(property="name", type="string", example="Examination Name"),
     *                 @OA\Property(property="department", type="string", example="Proctology"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully added on examination",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="On examination added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The name field is required."}),
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
            $this->onExaminationsService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/on_examinations_update/{id}",
     *     summary="Update onExaminations",
     *     tags={"On Examinations"},
     *     description="Update on examination details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the on examination to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update on examination details",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={},
     *                 @OA\Property(property="name", type="string", example="Updated Examination Name"),
     *                 @OA\Property(property="department", type="string", example="Proctology"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful on examination update",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="On examination updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The name field is required."}),
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
            $this->onExaminationsService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('OnExaminations data not found.');
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
     *     path="/api/on_examinations_delete/{id}",
     *     summary="Delete a onExaminations",
     *     tags={"On Examinations"},
     *     description="Deletes an on examination by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the on examination to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="On examination successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="On examination deleted successfully")
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
            $this->onExaminationsService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('OnExaminations data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/on_examination_dropdown_list/{departmentValue}",
     *     summary="Get all on examinations by department",
     *     tags={"On Examinations"},
     *     description="Retrieve a list of all on examinations filtered by department",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="departmentValue",
     *         in="path",
     *         required=false,
     *         description="Department value to filter on examinations",
     *         @OA\Schema(
     *             type="string",
     *             example="Proctology"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of on examinations filtered by department",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="On examinations retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Examination Name"),
     *                 @OA\Property(property="department", type="string", example="Proctology")
     *             ))
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
    public function onExaminationList(string $departmentValue)
    {
        try {
            return $this->successResponse($this->onExaminationsService->onExaminationList($departmentValue));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('OnExaminations data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}