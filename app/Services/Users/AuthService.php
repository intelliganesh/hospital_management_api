<?php
namespace App\Services\Users;

use App\Attributes\Transactional;
use App\Events\MailEvent;
use App\Mail\ForgotPasswordMail;
use App\Mail\PasswordUpdateSuccessMail;
use App\Models\User;
use App\Services\CheckValidation;
use App\Services\GetImageService;
use App\Services\ImageService;
use App\Traits\EncryAndDecryTrait;
use App\Traits\ResponseTrait;
use App\Traits\UserValidationTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends GetImageService
{

    use ResponseTrait;
    use EncryAndDecryTrait;
    use UserValidationTrait;

    protected $imageService;
    protected $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\ImageService $imageService
     */
    public function __construct(CheckValidation $checkValidationService, ImageService $imageService)
    {
        $this->checkValidationService = $checkValidationService;
        parent::__construct($imageService);
    }

    /**
     * Register a new user and return the registration data.
     *
     * @param Request $request
     * @return array
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create user record within a secure transaction')]
    public function registration(Request $request): void
    {
        $validator = $this->validate($request);
        $this->checkValidationService->checkValidation($validator);

        // $image = $this->convertImage('add', $request, 'image', 'user_image');'image''image' => $image,
        $user = User::create(array_merge($request->except('password'), ['password' => Hash::make($request->input('password'))]));
        // $userAddressProof = [
        //     'user_id' => $user->id,
        //     'id_type' => $request->id_type,
        //     'consent' => $request->consent,
        //     'id_number' => $request->id_value
        // ];
        // $this->userAddressProofService->update(new Request($userAddressProof), $user->id);
        if ($request->role) {
            $user->assignRole($request->role);
        }
    }

    /**
     * Login a user and return the success data.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'email'    => 'required|string|email|max:255',
        ]);

        $this->checkValidationService->checkValidation($validator);
        $credentials = $request->only('email', 'password');
        // $user = User::where('email', $request->email)->first();
        // $token = JWTAuth::fromUser($user);
        if (! $token = JWTAuth::attempt($credentials)) {
            $error = 'Invalid credentials';
            throw new Exception($error);
        }
        $user = Auth::user();
        return [
            'result'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                "role"  => $user->role,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'api_key' => [
                'original' => [
                    'token'      => $token,
                    'expires_in' => config('jwt.ttl'),
                ],
            ],
        ];
    }

    /**
     * Login a user by setting new token and return the success data.
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);
        $this->checkValidationService->checkValidation($validator);
        if (! $request->token) {
            $error = 'Token not provided';
            throw new Exception($error);
        }
        $token = $request->input('token');
        // $newToken = JWTAuth::refresh($request->token);
        $newToken = JWTAuth::setToken($token)->refresh();
        return [
            'result'  => [],
            'api_key' => [
                'original' => [
                    'token'      => $newToken,
                    'expires_in' => 180,
                ],
            ],
        ];
    }
    /**
     * User logout.
     *
     * @return bool
     */
    public function logout(): bool
    {
        $token = JWTAuth::parseToken()->getToken();
        JWTAuth::invalidate($token);
        return (bool) $token;
    }

    /**
     * Summary of forgotPassword
     * @param \Illuminate\Http\Request $request
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function forgotPassword(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);
        $this->checkValidationService->checkValidation($validator);
        $users = User::where('email', $request->email)->first();
        if (! $users) {
            throw new NotFoundHttpException("User not found");
        }
        $verificationUrl = config('services.hospital_app.reset_password_url') . urlencode($this->encrypt($users));
        event(new MailEvent($request->email, new ForgotPasswordMail($users, $verificationUrl)));
    }

    /**
     * Summary of resetPassword
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function resetPassword(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $this->checkValidationService->checkValidation($validator);
        [$email, $expiryTimestamp] = $this->decrypt($request->token);
        $user                      = User::where('email', $email)->first();
        if (! $user) {
            throw new NotFoundHttpException("User not found");
        }
        if (now()->timestamp > (int) $expiryTimestamp) {
            throw new Exception("Token expired");
        }
        $user->password = Hash::make($request->password);
        $user->save();
        event(new MailEvent($user->email, new PasswordUpdateSuccessMail($user)));
    }

    /**
     * Summary of verifyPassword
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function verifyPassword(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'hash' => 'required',
        ]);
        $this->checkValidationService->checkValidation($validator);
        [$email, $expiryTimestamp] = $this->decrypt($request->hash);
        $user                      = User::where('email', $email)->first();
        if (! $user) {
            throw new NotFoundHttpException("User not found");
        }
        if (now()->timestamp > (int) $expiryTimestamp) {
            throw new Exception("Token expired");
        }
    }

    public function userProfile()
    {
        return Auth::user()->makeHidden(['password', 'email_verified_at']);
    }
}
