<?php

namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\YogaAsanaService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Yoga Asana",
 *     description="API endpoints for managing yoga asanas (yoga postures) in the hospital system"
 * )
 */
class YogaAsanaController extends Controller
{
    use ResponseTrait;

    private $yogaAsanaService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\YogaAsanaService $yogaAsanaService
     */
    public function __construct(YogaAsanaService $yogaAsanaService)
    {
        $this->yogaAsanaService = $yogaAsanaService;
    }


    /**
     * @OA\Get(
     *     path="/api/yoga_asana_list",
     *     summary="Get all yoga asana",
     *     tags={"Yoga Asana"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Column to sort by",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sorting order",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->yogaAsanaService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     *
     * @OA\Get(
     *     path="/api/yoga_asana_details/{id}",
     *     summary="Get a yoga asana",
     *     tags={"Yoga Asana"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Yoga asana id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function get(string $id)
    {
        try {
            return $this->successResponse($this->yogaAsanaService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/YogaAsana data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/api/yoga_asana_add",
     *     summary="Create a yoga asana",
     *     tags={"Yoga Asana"},
     *     security={{"bearerAuth": {}}},
     *     description="Create a yoga asana with its details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"asana_name", "status"},
     *             @OA\Property(property="recommended_duration", type="integer", example=60),
     *             @OA\Property(property="asana_name", type="string", maxLength=100, example="Tadasana"),
     *             @OA\Property(property="description", type="string", example="Mountain pose description"),
     *             @OA\Property(property="benefits", type="string", example="Improves posture and balance"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active"),
     *             @OA\Property(property="contraindications", type="string", example="Avoid if you have vertigo"),
     *             @OA\Property(property="difficulty_level", type="string", enum={"Beginner", "Intermediate", "Advanced"}, example="Beginner"),
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
            $this->yogaAsanaService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/yoga_asana_update/{id}",
     *     summary="Update a yoga asana",
     *     tags={"Yoga Asana"},
     *     security={{"bearerAuth": {}}},
     *     description="Update a yoga asana with its details",
     *     @OA\Parameter(
     *         description="Yoga asana id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"asana_name", "status"},
     *             @OA\Property(property="recommended_duration", type="integer", example=60),
     *             @OA\Property(property="asana_name", type="string", maxLength=100, example="Tadasana"),
     *             @OA\Property(property="description", type="string", example="Mountain pose description"),
     *             @OA\Property(property="benefits", type="string", example="Improves posture and balance"),
     *             @OA\Property(property="status", type="string", enum={"Active", "Inactive"}, example="Active"),
     *             @OA\Property(property="contraindications", type="string", example="Avoid if you have vertigo"),
     *             @OA\Property(property="difficulty_level", type="string", enum={"Beginner", "Intermediate", "Advanced"}, example="Beginner"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Yoga asana updated successfully."),
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
    public function update(Request $request, string $id)
    {
        try {
            $this->yogaAsanaService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/YogaAsana data not found.');
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
     *     path="/api/yoga_asana_delete/{id}",
     *     summary="Delete Yoga Asana",
     *     tags={"Yoga Asana"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         description="Yoga Asana id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Yoga Asana deleted successfully."),
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
    public function delete(string $id)
    {
        try {
            $this->yogaAsanaService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/YogaAsana data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/yoga_asana_options_list",
     *     summary="Get yoga asana options for dropdown lists",
     *     tags={"Yoga Asana"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", example="1"),
     *                 @OA\Property(property="asana_name", type="string", example="Tadasana")
     *             ))
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function optionsList()
    {
        try {
            return $this->successResponse($this->yogaAsanaService->optionsList());
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Master/YogaAsana data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}