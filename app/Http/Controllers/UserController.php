<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\Users\UserService;
use App\Services\Master\RolesService;
use App\Interceptors\ServiceInterceptor;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API endpoints for managing users and user roles"
 * )
 */
class UserController extends Controller
{
    use ResponseTrait;
    protected $userService;
    protected $rolesService;

    /**
     * Summary of __construct
     * @param \App\Services\Users\UserService $userService
     * @param \App\Services\Master\RolesService $rolesService
     */
    public function __construct(UserService $userService, RolesService $rolesService)
    {
        $this->userService = $userService;
        $this->rolesService = $rolesService;
    }
    /**
     * @OA\Post(
     *     path="/api/user_add",
     *     summary="User add",
     *     tags={"Users"},
     *     description="Register a new user with personal and professional details",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User add credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password", "phone", "date_of_birth", "age", "gender", "address", "country", "state", "city", "marital_status", "designation", "qualification"},
     *             @OA\Property(property="name", type="string", example="MP Vishnu Prakash"),
     *             @OA\Property(property="email", type="string", format="email", example="vishnuprakash@intellispiders.com"),
     *             @OA\Property(property="phone", type="string", example="7892474770"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1998-11-06"),
     *             @OA\Property(property="age", type="integer", example=26),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="address", type="string", example="Sri Kamala Nilayam 175 1st Cross Road Vinayaka Layout Uttarahalli Hobli 560061"),
     *             @OA\Property(property="country", type="string", example="India"),
     *             @OA\Property(property="state", type="string", example="Karnataka"),
     *             @OA\Property(property="city", type="string", example="Bangalore"),
    *             @OA\Property(property="marital_status", type="string", example="single"),
    *             @OA\Property(property="designation", type="string", example="Software Engineer"),
    *             @OA\Property(property="qualification", type="string", example="B.Tech in Computer Science"),
    *             @OA\Property(property="available_days", type="string", description="Available days in JSON or text format"),
    *             @OA\Property(property="slot_duration", type="string", example="30", description="Slot duration in minutes"),
    *             @OA\Property(property="leave_date", type="string", example="2024-05-20,2024-05-21", description="Leave dates as comma-separated values or text")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful user add",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully received"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="array", @OA\Items(type="string"), example={"The name field is required."}),
     *             @OA\Property(property="email", type="array", @OA\Items(type="string"), example={"The email must be a valid email address."}),
     *             @OA\Property(property="phone", type="array", @OA\Items(type="string"), example={"The phone should be a 10-digit Indian phone number."}),
     *             @OA\Property(property="password", type="array", @OA\Items(type="string"), example={"The password must be at least 6 characters."}),
     *             @OA\Property(property="date_of_birth", type="array", @OA\Items(type="string"), example={"The date of birth must be a valid date."}),
     *             @OA\Property(property="age", type="array", @OA\Items(type="string"), example={"The age must be between 18 and 120."}),
     *             @OA\Property(property="gender", type="array", @OA\Items(type="string"), example={"The gender must be male, female, or other."}),
     *             @OA\Property(property="address", type="array", @OA\Items(type="string"), example={"The address field is required."}),
     *             @OA\Property(property="country", type="array", @OA\Items(type="string"), example={"The country field is required."}),
     *             @OA\Property(property="state", type="array", @OA\Items(type="string"), example={"The state field is required."}),
     *             @OA\Property(property="city", type="array", @OA\Items(type="string"), example={"The city field is required."}),
     *             @OA\Property(property="marital_status", type="array", @OA\Items(type="string"), example={"The marital status must be single, married, divorced, or widowed."}),
     *             @OA\Property(property="designation", type="array", @OA\Items(type="string"), example={"The designation field is required."}),
     *             @OA\Property(property="qualification", type="array", @OA\Items(type="string"), example={"The qualification field is required."})
     *         )
     *     ),
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
     * )
     */
    public function create(Request $request)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->userService);
            // $proxiedService->registration($request);
            return $this->successResponse(['id' => $proxiedService->createUser($request)]);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\GET(
     *      path="/api/user_list",
     *      summary="User list",
     *      tags={"Users"},
     *      description="Get user list",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Users list successfully fetched.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Users list successfully fetched."
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="name", type="string", example="MP Vishnu Prakash"),
     *                      @OA\Property(property="email", type="string", example="vishnuprakash@intellispiders.com"),
     *                      @OA\Property(property="phone", type="string", example="7892474770"),
     *                      @OA\Property(property="image", type="string", example="uploads/profile.jpg"),
     *                      @OA\Property(property="designation", type="string", example="Software Engineer"),
     *                      @OA\Property(property="qualification", type="string", example="B.Tech in Computer Science"),
     *                  )
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
     * )
     */
    public function all(Request $request)
    {
        try {
            $userList = $this->userService->all($request);
            return $this->successResponse($userList);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\GET(
     *      path="/api/user_details/{id}",
     *      summary="User details",
     *      tags={"Users"},
     *      description="Get user details by id",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the user to get user details",
     *          @OA\Schema(type="integer",example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User details successfully fetched.",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="status",
     *                  type="string",
     *                  example="success"
     *              ),
     *              @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="User details successfully fetched."
     *              ),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="MP Vishnu Prakash"),
     *                     @OA\Property(property="email", type="string", example="vishnuprakash@intellispiders.com"),
     *                     @OA\Property(property="phone", type="string", example="7892474770"),
     *                     @OA\Property(property="image", type="string", example="uploads/profile.jpg"),
     *                     @OA\Property(property="date_of_birth", type="string", example="1998-11-06"),
     *                     @OA\Property(property="age", type="integer", example=26),
     *                     @OA\Property(property="gender", type="string", example="male"),
     *                     @OA\Property(property="address", type="string", example="Sri Kamala Nilayam 175 1st Cross Road Vinayaka Layout Uttarahalli Hobli 560061"),
     *                     @OA\Property(property="country", type="string", example="India"),
     *                     @OA\Property(property="state", type="string", example="Karnataka"),
     *                     @OA\Property(property="city", type="string", example="Bangalore"),
     *                     @OA\Property(property="marital_status", type="string", example="single"),
     *                     @OA\Property(property="designation", type="string", example="Software Engineer"),
     *                     @OA\Property(property="qualification", type="string", example="B.Tech in Computer Science"),
     *                     @OA\Property(property="password", type="string", example="$2y$10$...[hashed_password]")
     *                 ),
     *             ),
     *          ),
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
     * )
     */

    public function get(string $id)
    {
        try {
            $userRemoved = $this->userService->get($id);
            return $this->successResponse($userRemoved, 'User details fetched successfully');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('User data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    /**
     * @OA\Put(
     *     path="/api/user_edit/{id}",
     *     summary="Edit user",
     *     tags={"Users"},
     *     description="Edit existing user details",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the user to get user details",
     *          @OA\Schema(type="integer",example=1)
     *      ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User edit data",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"id", "name", "email", "phone", "DOB", "age", "gender", "address", "country", "state", "city", "marital_status", "designation", "qualification"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="MP Vishnu Prakash"),
     *             @OA\Property(property="email", type="string", format="email", example="vishnuprakash@intellispiders.com"),
     *             @OA\Property(property="phone", type="string", example="7892474770"),
     *             @OA\Property(property="DOB", type="string", format="date", example="1998-11-06"),
     *             @OA\Property(property="age", type="integer", example=26),
     *             @OA\Property(property="gender", type="string", example="male"),
     *             @OA\Property(property="address", type="string", example="Sri Kamala Nilayam 175 1st Cross Road Vinayaka Layout Uttarahalli Hobli 560061"),
     *             @OA\Property(property="country", type="string", example="India"),
     *             @OA\Property(property="state", type="string", example="Karnataka"),
     *             @OA\Property(property="city", type="string", example="Bangalore"),
     *             @OA\Property(property="marital_status", type="string", example="single"),
     *             @OA\Property(property="designation", type="string", example="Software Engineer"),
     *             @OA\Property(property="qualification", type="string", example="B.Tech in Computer Science")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User update successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User details updated successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="array", @OA\Items(type="string"), example={"The name field is required."}),
     *             @OA\Property(property="email", type="array", @OA\Items(type="string"), example={"The email must be a valid email address."}),
     *             @OA\Property(property="phone", type="array", @OA\Items(type="string"), example={"The phone should be a 10-digit Indian phone number."}),
     *             @OA\Property(property="DOB", type="array", @OA\Items(type="string"), example={"The date of birth must be a valid date."}),
     *             @OA\Property(property="age", type="array", @OA\Items(type="string"), example={"The age must be between 18 and 120."}),
     *             @OA\Property(property="gender", type="array", @OA\Items(type="string"), example={"The gender must be male, female, or other."}),
     *             @OA\Property(property="address", type="array", @OA\Items(type="string"), example={"The address field is required."}),
     *             @OA\Property(property="country", type="array", @OA\Items(type="string"), example={"The country field is required."}),
     *             @OA\Property(property="state", type="array", @OA\Items(type="string"), example={"The state field is required."}),
     *             @OA\Property(property="city", type="array", @OA\Items(type="string"), example={"The city field is required."}),
     *             @OA\Property(property="marital_status", type="array", @OA\Items(type="string"), example={"The marital status must be single, married, divorced, or widowed."}),
     *             @OA\Property(property="designation", type="array", @OA\Items(type="string"), example={"The designation field is required."}),
     *             @OA\Property(property="qualification", type="array", @OA\Items(type="string"), example={"The qualification field is required."})
     *         )
     *     ),
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
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $proxiedService = ServiceInterceptor::intercept($this->userService);
            // $proxiedService->update($request, $id);
            return $this->successResponse(['id' => $proxiedService->updateUser($request, $id)], 'User successfully updated');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('User data not found.');
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
     *     path="/api/user_delete/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     description="Deletes a user by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user to be deleted",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User deleted successfully."
     *             )
     *         )
     *     ),
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
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->userService->softDelete($id);
            return $this->successResponse(null, 'User successfully deleted');
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('User data not found.');
        } catch (NotFoundHttpException $ne) {
            return $this->errorResponse([], $ne->getMessage(), $ne->getStatusCode());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/get_roles_list",
     *     summary="Get list of roles",
     *     description="Returns list of roles",
     *     security={{"bearerAuth": {}}},
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Roles fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Roles retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="admin"),
     *                 @OA\Property(property="description", type="string", example="Administrator role"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z")
     *             ))
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
    public function getRolesList()
    {
        try {
            return $this->successResponse($this->rolesService->getRolesList());
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/old_password_check",
     *     summary="Reset password after login",
     *     description="Reset password after login",
     *     security={{"bearerAuth": {}}},
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Old and new password",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="oldPassword", type="string", format="password"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password reset successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="updated", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function resetPasswordAfterLogin(Request $request)
    {
        try {
            return $this->successResponse(['updated' => $this->userService->resetPasswordAfterLogin($request)]);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
