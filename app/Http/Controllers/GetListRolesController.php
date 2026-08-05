<?php
namespace App\Http\Controllers;

use App\Enums\ListBasedOnRolesEnum;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="API endpoints for managing role-based lists"
 * )
 */
class GetListRolesController extends Controller
{

    use ResponseTrait;

    /**
     * @OA\Post(
     *     path="/api/roles_list_for_dropdown",
     *     summary="Get dropdown list based on roles",
     *     description="Retrieves a list of items based on the specified model type and columns",
     *     tags={"Roles"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Specify model type and columns to retrieve",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object", 
     *                 required={"columns", "modal_type"},
     *                 @OA\Property(property="columns", type="array", @OA\Items(type="string"), example="name"),
     *                 @OA\Property(property="modal_type", type="string", example="DOCTOR")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of role-based list",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Role-based list retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dr. John Doe")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="columns", type="array", @OA\Items(type="string"), example={"The columns field is required."}),
     *                 @OA\Property(property="modal_type", type="array", @OA\Items(type="string"), example={"The modal type field is required."})
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
    public function rolesListForDropdown(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'columns'    => 'required',
                'modal_type' => 'required',
            ]);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $modalType        = $request->modal_type;
            $imageServiceEnum = ListBasedOnRolesEnum::from($modalType);
            $modelClass = $imageServiceEnum->model();
            $data = $modelClass::select($request->columns)->get();
            return $this->successResponse($data, 'Role-based list retrieved successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Dashboard data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
