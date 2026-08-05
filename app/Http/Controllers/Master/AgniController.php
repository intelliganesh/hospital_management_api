<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Services\Master\AgniService;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Agni",
 *     description="API endpoints for managing Agni (digestive fire) types in Ayurvedic assessment"
 * )
 */
class AgniController extends Controller
{

    use ResponseTrait;

    private $agniService;

    /**
     * Summary of __construct
     * @param \App\Services\Master\AgniService $agniService
     */
    public function __construct(AgniService $agniService)
    {
        $this->agniService = $agniService;
    }

    /**
     * @OA\Get(
     *     path="/api/agni_list",
     *     tags={"Agni"},
     *     summary="Get list of agni",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of agni with their details",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="finding details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         enum={"T", "M", "S"},
     *                         description="Agni type (T, M, S)"
     *                     ),
     *                 ),
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
    public function all(?Request $request)
    {
        try {
            return $this->successResponse($this->agniService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/agni_details/{id}",
     *     tags={"Agni"},
     *     summary="Get a agni",
     *     security={{"bearerAuth": {}}},
     *     description="Returns a agni with its details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Agni id",
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
     *             @OA\Property(property="message", type="string", example="Agni details successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     enum={"T", "M", "S"},
     *                     description="Agni type (T, M, S)"
     *                 ),
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
            return $this->successResponse($this->agniService->get($id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Agni data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/agni_add",
     *     tags={"Agni"},
     *     summary="Create a agni",
     *     security={{"bearerAuth": {}}},
     *     description="Create a agni with its details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="label", type="string", example="T"),
     *             @OA\Property(property="value", type="string", enum={"T", "M", "S"}, example="T")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Agni created successfully."),
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
    public function create(Request $request)
    {
        try {
            $this->agniService->create($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/agni_update/{id}",
     *     tags={"Agni"},
     *     summary="Update a agni",
     *     security={{"bearerAuth": {}}},
     *     description="Update a agni with its details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Agni id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="label", type="string", example="T"),
     *             @OA\Property(property="value", type="string", enum={"T", "M", "S"}, example="T")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Agni updated successfully."),
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
            $this->agniService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Agni data not found.');
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
     * @OA\Delete(
     *     path="/api/agni_delete/{id}",
     *     summary="Delete Agni",
     *     tags={"Agni"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         description="Agni id",
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
     *             @OA\Property(property="message", type="string", example="Agni deleted successfully."),
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
            $this->agniService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Agni data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/agni_options_list",
     *     tags={"Agni"},
     *     summary="Get Agni options enum options",
     *     security={{"bearerAuth": {}}},
     *     description="Returns list of Agni options enum options (T, M, S) with labels and values",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Agni options fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="label", type="string", example="T"),
     *                     @OA\Property(
     *                         property="value",
     *                         type="string",
     *                         enum={"T", "M", "S"},
     *                         example="T"
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
    public function list()
    {
        try {
            return $this->successResponse($this->agniService->list());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
