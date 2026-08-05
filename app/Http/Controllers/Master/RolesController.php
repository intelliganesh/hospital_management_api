<?php


namespace App\Http\Controllers\Master;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\Master\RolesService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="API endpoints for managing roles"
 * )
 */

class RolesController extends Controller
{
    use ResponseTrait;
    private $rolesService;

    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }

    /**
     * @OA\Get(
     *      path="/api/roles_list",
     *      tags={"Roles"},
     *      summary="Get list of roles",
     *      description="Returns a paginated list of all roles with optional filtering and sorting",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Search keyword to filter roles by name or description",
     *          @OA\Schema(
     *             type="string",
     *             example="admin"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          required=false,
     *          description="Field to sort by (e.g., name, created_at)",
     *          @OA\Schema(
     *             type="string",
     *             example="name"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          required=false,
     *          description="Sort direction (asc or desc)",
     *          @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="asc"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of roles",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Roles fetched successfully"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="data", type="array", 
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="admin"),
     *                          @OA\Property(property="description", type="string", example="Has full access to all modules"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
     *                      )
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string", example="http://localhost/api/roles_list?page=1"),
     *                  @OA\Property(property="from", type="integer", example=1),
     *                  @OA\Property(property="last_page", type="integer", example=3),
     *                  @OA\Property(property="last_page_url", type="string", example="http://localhost/api/roles_list?page=3"),
     *                  @OA\Property(property="next_page_url", type="string", example="http://localhost/api/roles_list?page=2"),
     *                  @OA\Property(property="path", type="string", example="http://localhost/api/roles_list"),
     *                  @OA\Property(property="per_page", type="integer", example=10),
     *                  @OA\Property(property="prev_page_url", type="string", example=null),
     *                  @OA\Property(property="to", type="integer", example=10),
     *                  @OA\Property(property="total", type="integer", example=25)
     *              )
     *          )
     *      ),
     *      @OA\Response(
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
            return $this->successResponse($this->rolesService->all($request));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/roles_add",
     *      tags={"Roles"},
     *      summary="Create a new role",
     *      description="Creates a new role with the provided details and returns success response",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Role information",
     *          @OA\JsonContent(
     *              required={"name", "description"},
     *              @OA\Property(property="name", type="string", example="Super Admin", description="Role name - must be unique"),
     *              @OA\Property(property="description", type="string", example="Has full access to all system modules", description="Role description"),
     *              @OA\Property(property="status", type="integer", example=1, description="Role status: 1 for active, 0 for inactive"),
     *              @OA\Property(property="permissions", type="array", description="Optional array of permission IDs to assign to this role", @OA\Items(type="integer", example=1))
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Role created successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Role created successfully"),
     *              @OA\Property(property="data", type="object", 
     *                  @OA\Property(property="id", type="integer", example=5),
     *                  @OA\Property(property="name", type="string", example="Super Admin"),
     *                  @OA\Property(property="description", type="string", example="Has full access to all system modules"),
     *                  @OA\Property(property="status", type="integer", example=1),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                  @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required."))
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     *  )
     */

    public function create(Request $request)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->rolesService);
            $proxiedService->create($request);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Roles data not found.');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Put(
     *      path="/api/roles_update/{id}",
     *      tags={"Roles"},
     *      summary="Update an existing role",
     *      description="Updates an existing role with the provided details and returns success response",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the role to update",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Role information to update",
     *          @OA\JsonContent(
     *              required={"name", "description"},
     *              @OA\Property(property="name", type="string", example="Updated Admin Role", description="Role name - must be unique"),
     *              @OA\Property(property="description", type="string", example="Updated role with administrative privileges", description="Role description"),
     *              @OA\Property(property="status", type="integer", example=1, description="Role status: 1 for active, 0 for inactive"),
     *              @OA\Property(property="permissions", type="array", description="Optional array of permission IDs to assign to this role", @OA\Items(type="integer", example=1))
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Role updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Role updated successfully"),
     *              @OA\Property(property="data", type="object", 
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="Updated Admin Role"),
     *                  @OA\Property(property="description", type="string", example="Updated role with administrative privileges"),
     *                  @OA\Property(property="status", type="integer", example=1),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00.000000Z"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T10:00:00.000000Z")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                  @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required."))
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     *  )
     *
     */

    public function update(Request $request, string $id)
    {
        try {
            $this->rolesService->update($request, $id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Roles data not found.');
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
     *      path="/api/roles_delete/{id}",
     *      tags={"Roles"},
     *      summary="Delete an existing role",
     *      description="Deletes a role by its ID and returns success response",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the role to delete",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Role deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Role deleted successfully"),
     *              @OA\Property(property="data", type="object", example=null, nullable=true)
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden - Cannot delete system role",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Cannot delete system role.")
     *          )
     *      ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     *  )
     */
    public function delete(string $id)
    {
        try {
            $this->rolesService->delete($id);
            return $this->successResponse();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('Roles data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


}
