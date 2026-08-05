<?php
namespace App\Http\Controllers;

use App\Services\LogActivityService;
use App\Services\Users\AuthService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;


/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for managing user login,registration and password reset"
 * )
 */

class AuthController extends Controller
{

    use ResponseTrait;
    use PaginationTrait;
    protected $authService;
    protected $logs;
    public function __construct(AuthService $authService, LogActivityService $logs)
    {
        $this->logs        = $logs;
        $this->authService = $authService;
    }

    /**
     * Summary of documentation
     * @return \Illuminate\Contracts\View\View
     */
    public function documentation()
    {
        return view("documentation");
    }

    public function index(Request $request)
    {
        try {
            $logs = $this->paginate($request, $this->logs->logActivityLists());
            return view("logs", compact("logs"));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/registration",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     description="Register a new user with personal and professional details",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration credentials including a profile image",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "phone", "image", "DOB", "age", "gender", "address", "country", "state", "city", "marital_status", "designation", "qualification"},
     *                 @OA\Property(property="name", type="string", example="MP Vishnu Prakash"),
     *                 @OA\Property(property="email", type="string", format="email", example="vishnuprakash@intellispiders.com"),
     *                 @OA\Property(property="phone", type="string", example="7892474770"),
     *                 @OA\Property(property="password", type="string", format="password", example="Developer@123"),
     *                 @OA\Property(property="image", type="string", format="binary"),
     *                 @OA\Property(property="DOB", type="string", format="date", example="1998-11-06"),
     *                 @OA\Property(property="age", type="integer", example=26),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="address", type="string", example="Sri Kamala Nilayam 175 1st Cross Road Vinayaka Layout Uttarahalli Hobli 560061"),
     *                 @OA\Property(property="country", type="string", example="India"),
     *                 @OA\Property(property="state", type="string", example="Karnataka"),
     *                 @OA\Property(property="city", type="string", example="Bangalore"),
     *                 @OA\Property(property="marital_status", type="string", example="single"),
     *                 @OA\Property(property="designation", type="string", example="Software Engineer"),
     *                 @OA\Property(property="qualification", type="string", example="B.Tech in Computer Science")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful registration",
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
     *             @OA\Property(property="image", type="array", @OA\Items(type="string"), example={"The image must be an image and not exceed 2MB."}),
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
    public function registration(Request $request)
    {
        try {
            $this->authService->registration($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     description="Authenticate a user with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@ashospital.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Admin@123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
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
    public function login(Request $request)
    {
        try {
            $authLogin = $this->authService->login($request);
            return $this->successResponse($authLogin);
        } catch (JWTException $e) {
            return $this->exceptionResponse($e);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
            // return $this->errorResponse(
            //     $ve->validator->errors()->toArray(),
            //     'Validation error',
            //     422
            // );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/refresh_token",
     *     summary="User refresh token",
     *     tags={"Authentication"},
     *     description="Authenticate a user with new token",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"token"},
     *             @OA\Property(property="token", type="string", format="token", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *             ),
     *             @OA\Property(
     *                 property="api_key",
     *                 type="object",
     *                 @OA\Property(
     *                     property="original",
     *                     type="object",
     *                     @OA\Property(
     *                         property="token",
     *                         type="string",
     *                         example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
     *                     ),
     *                     @OA\Property(
     *                         property="expires_in",
     *                         type="integer",
     *                         example=180
     *                     )
     *                 )
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
    public function refreshToken(Request $request)
    {
        try {
            $token = $this->authService->refreshToken($request);
            return $this->successResponse(compact('token'), 'Token refreshed successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     description="User logout",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful user logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User successfully logout")
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
    public function logout()
    {
        try {
            $this->authService->logout();
            return $this->successResponse([], 'User successfully logout');
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/forgot_password",
     *     summary="Send forgot password email",
     *     tags={"Authentication"},
     *     description="Sends a password reset link to the user's email",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email field is required.")
     *                 )
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
    public function forgotPassword(Request $request)
    {
        try {
            $this->authService->forgotPassword($request);
            return $this->successResponse();
        } catch (NotFoundHttpException $ne) {
            return $this->notFoundResponse($ne);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reset_password",
     *     summary="Reset user password",
     *     tags={"Authentication"},
     *     description="Allows the user to reset their password using a valid token or verification link",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "password_confirmation", "token"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="new_password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="new_password123"),
     *             @OA\Property(property="token", type="string", example="secure-reset-token-or-url-signature")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email field is required.")
     *                 )
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
    public function resetPassword(Request $request)
    {
        try {
            $this->authService->resetPassword($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/verify-password",
     *     tags={"Authentication"},
     *     summary="Verify User Password",
     *     description="Validates the user's password using a hash, checks the user's existence, and ensures the token is not expired.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Request data containing the hashed password to verify",
     *         @OA\JsonContent(
     *             required={"hash"},
     *             @OA\Property(property="hash", type="string", example="encryptedHashHere")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password verification successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Password verified successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request due to missing or invalid data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid request data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Token has expired or user is not authenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthorized or token expired")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * )
     */
    public function verifyPassword(Request $request)
    {
        try {
            $this->authService->verifyPassword($request);
            return $this->successResponse();
        } catch (ValidationException $ve) {
            return $this->errorResponse(
                $ve->validator->errors()->toArray(),
                'Validation error',
                422
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user_profile_details",
     *     summary="User details",
     *     tags={"Users"},
     *     description="Get user details by ID",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User details successfully fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User details successfully fetched."),
     *             @OA\Property(
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
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedResponse"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=500, ref="#/components/responses/ServerErrorResponse")
     * )
     */
    public function userProfile()
    {
        try {
            return $this->successResponse($this->authService->userProfile());
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

}
