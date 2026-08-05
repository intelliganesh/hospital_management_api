<?php
namespace App\Services\Users;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Contracts\MailServiceContract;
use App\Contracts\SoftDeleteContract;
use App\Events\MailEvent;
use App\Mail\WelcomeUserMail;
use App\Models\User;
use App\Models\UserAddressProof;
use App\Services\CheckValidation;
use App\Services\GetImageService;
use App\Services\Master\DepartmentsService;
use App\Traits\FieldValuesTrait;
use App\Traits\UserValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService implements FilterContract, CRUDContract, SoftDeleteContract, MailServiceContract
{

    use FieldValuesTrait;
    use UserValidationTrait;

    private $columns;

    private $filtersColumn;

    private $departmentService;
    protected $getImageService;
    private $userAddressProofService;
    protected $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\GetImageService $getImageService
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\Master\DepartmentsService $departmentService
     * @param \App\Services\Users\UserAddressProofService $userAddressProofService
     */
    public function __construct(DepartmentsService $departmentService, GetImageService $getImageService, CheckValidation $checkValidationService, UserAddressProofService $userAddressProofService)
    {
        $this->columns                 = User::$columns;
        $this->getImageService         = $getImageService;
        $this->filtersColumn           = User::$filtersColumn;
        $this->departmentService       = $departmentService;
        $this->checkValidationService  = $checkValidationService;
        $this->userAddressProofService = $userAddressProofService;
    }

    /**
     * Summary of search
     * @param string $search
     * @param mixed $user
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * @deprecated This method is not used.
     *
     */
    public function filterByDateRange(string $searchText, $data)
    {
        return $data;
    }

    /**
     * @deprecated This method is not used.
     *
     */
    public function sortData(string $searchText, $data)
    {
        // write code here
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return User
     */
    public function get(string $id): mixed
    {
        $user       = User::findOrFail($id);
        $govIdProof = $this->userAddressProofService->getDataByDynamicColumnsName('user_id', $id);
        // $role = Role::where('id', $user->id)->first();
        $user['role']             = $user->getRoleNames()[0] ?? null;
        $user['id_type']          = $govIdProof->id_type ?? "";
        $user['id_number_masked'] = $govIdProof->id_number_masked ?? "";
        $user['consent']          = $govIdProof->consent ?? "";
        $user['gov_image']        = $govIdProof->image ?? "";
        // $user['id_proof_for_pan'] = $govIdProof->id_proof_for_pan ?? "";
        return $user;
    }

    /**
     * Summary of getUserByRole
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    protected function getUserByRole(string $role)
    {
        return User::role($role)->get(['id', 'name', 'email', 'phone', 'image']);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request = null): mixed
    {
        $user = User::query();
        if ($request->has('search')) {
            $searchValue = $request->search;
            $user        = $this->search($searchValue, $user);
        }

        if ($request->has('sort_by')) {
            if ($request->sort_by == 'department') {
                $sortBy = 'department_name';
            } else {
                $sortBy = $request->sort_by ?? 'name';
            }
            $sortOrder = $request->sort_order ?? 'desc';
            $user      = $user->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $user = $this->filterMultipleFields($request->multiple_filter, $user);
        }

        return $user->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filtersColumn as $column) {
            if (! empty($request[$column])) {
                $data->where($column, $request[$column]);
            }
        }
        return $data;
    }

    /**
     * @deprecated This method is not used.
     */
    public function create(Request $request): void
    {
        // write code here
    }

    /**
     * Summary of createUser
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create user record within a secure transaction')]
    public function createUser(Request $request): mixed
    {
        $this->checkValidationService->checkValidation($this->validate($request));

        // $image = $this->convertImage('add', $request, 'image', 'user_image');'image''image' => $image,
        $department        = $this->departmentService->get($request->department);
        $userCompleteArray = [
            'role'            => $request->role,
            'department_name' => $department->name,
            'department_code' => $department->code,
        ];

        // $password  = Str::random(8); //Random password
        $inputData = array_merge($userCompleteArray, $request->except('password'), ['password' => Hash::make($request->input('password'))]);
        $user      = User::create($inputData);

        if ($request->role) {
            $user->assignRole($request->role);
        }

        if (! empty($request->id_type) && isset($request->id_type)) {
            $userAddressProof = [
                'user_id'          => $user->id,
                'id_type'          => $request->id_type,
                'consent'          => $request->consent,
                'id_number'        => $request->id_value,
                'id_proof_for_pan' => $request->id_proof_for_pan,
            ];
            return $this->userAddressProofService->createOrUpdateReturn(new Request($userAddressProof))->id;
        }

        return null;
        // $this->userAddressProofService->update(new Request($userAddressProof), $user->id);
    }

    /**
     * Summary of update
     * @deprecated This method is not used.
     *
     */
    public function update(Request $request, string | null $id): void
    {

    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return mixed
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update user record within a secure transaction')]
    public function updateUser(Request $request, string | null $id): mixed
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $userUpdate = User::findOrFail($request->id);
        // $userAddressProof = [
        //     'user_id' => $id,
        //     'id_type' => $request->id_type,
        //     'consent' => $request->consent,
        //     'id_number' => $request->id_value
        // ];

        // $this->userAddressProofService->update(new Request($userAddressProof), $id);
        // if (!$userUpdate) {
        //     throw new NotFoundHttpException('User data not found.');
        // }
        $department        = $request->department ? $this->departmentService->get($request->department) : $this->departmentService->get($userUpdate->department);
        $userCompleteArray = [
            'department_name' => $department->name,
            'department_code' => $department->code,
            'role'            => $request->role ? $request->role : $userUpdate->role,
        ];
        $data = $request->except('role', 'password', 'id_edited');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $userUpdate->update(array_merge($userCompleteArray, $data));
        if ($request->role && ! $userUpdate->hasRole($request->role)) {
            $userUpdate->syncRoles($request->role);
        }

        if ($request->id_edited && ! empty($request->id_type) && isset($request->id_type)) {
            $userAddressProof = [
                'user_id'          => $id,
                'id_type'          => $request->id_type,
                'consent'          => $request->consent,
                'id_number'        => $request->id_value,
                'id_proof_for_pan' => $request->id_proof_for_pan,
            ];
            return $this->userAddressProofService->createOrUpdateReturn(new Request($userAddressProof))->id;
        }

        $userAddressProof = UserAddressProof::where('user_id', $id)->first();

        return $userAddressProof ? $userAddressProof->id : null;
    }

    /**
     * @deprecated This method is not used.
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        $validator = $this->validate($request, true, $id);
        $this->checkValidationService->checkValidation($validator);
        $userUpdate = User::findOrFail($id);
        if (! $userUpdate) {
            throw new NotFoundHttpException('User data not found.');
        }
        // Partial update: Only update specified fields
        $userUpdate->fill($request->only($this->fileds($request)));
        $userUpdate->save();
    }

    /**
     * @deprecated This method is not used.
     */
    public function delete(string $id): void
    {
        // write code here
    }

    /**
     * Summary of softDelete
     * @param string $id
     * @return bool
     */

    public function softDelete(string $id): bool
    {
        $user = User::findOrFail($id);
        $user->delete();
        return (bool) $user;
    }

    /**
     * Summary of mail
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function sendWelcomeMail(Request $request): bool
    {
        $users = User::where('email', $request->email)->first();
        event(new MailEvent($users->email, new WelcomeUserMail($users)));
        return (bool) $users;
    }

    // /**
    //  * Summary of registration
    //  * @param \Illuminate\Http\Request $request
    //  * @return void
    //  */
    // #[Transactional(secure: true, requiredRole: null, description: 'Create user record within a secure transaction')]
    // public function registration(Request $request): void
    // {
    //     $validator = $this->validate($request);
    //     $this->checkValidationService->checkValidation($validator);

    //     // $image = $this->convertImage('add', $request, 'image', 'user_image');'image''image' => $image,
    //     $user = User::create(array_merge($request->except('password'), ['password' => Hash::make($request->input('password'))]));
    //     $userAddressProof = [
    //         'user_id' => $user->id,
    //         'id_type' => $request->id_type,
    //         'consent' => $request->consent,
    //         'id_number' => $request->id_value
    //     ];
    //     if ($request->role) {
    //         $user->assignRole($request->role);
    //     }
    //     // $this->userAddressProofService->update(new Request($userAddressProof), $user->id);
    //     $this->userAddressProofService->createOrUpdateReturn(new Request($userAddressProof));
    // }

    /**
     * Get users list by role name.
     *
     * @param string $role
     * @return \Illuminate\Support\Collection
     */
    public function getUsersList(string $role): \Illuminate\Support\Collection
    {
        if (! Role::where('name', $role)->exists()) {
            return collect();
        }
        return User::role($role)->select('id', 'name', 'role')->get();
    }

    public function getAlluser()
    {
        return User::select('id', 'name', 'role')->get();
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create user record within a secure transaction')]
    public function ration(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $user = User::create(array_merge($request->except('password'), ['password' => Hash::make($request->input('password'))]));

        $userAddressProof = [
            'user_id'   => $user->id,
            'id_type'   => $request->id_type,
            'consent'   => $request->consent,
            'id_number' => $request->id_value,
        ];
        $this->userAddressProofService->create(new Request($userAddressProof));

        if ($request->role) {
            $user->assignRole($request->role);
        }
    }

    /**
     * Summary of resetPasswordAfterLogin
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function resetPasswordAfterLogin(Request $request): bool
    {
        $user = Auth::user();
        if (Hash::check($request->oldPassword, $user->password)) {
            return true;
        }
        return false;
    }

}
