<?php

namespace App\Services\Master;

// use App\Models\User;
// use App\Models\Master\Roles;
use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Traits\RolesValidation;
use App\Attributes\Transactional;
use App\Contracts\FilterContract;
use App\Services\CheckValidation;
use Spatie\Permission\Models\Role as Roles;
// use Spatie\Permission\Models\Role as SpatieRole;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RolesService implements CRUDContract, FilterContract
{
    use RolesValidation;

    private $columns = [
        "id",
        "name",
        "status",
        "description",
    ];
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Roles
     */
    public function get(string $id): mixed
    {
        $role = Roles::findOrFail($id);
        // if (!$role) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        return $role;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request = null): mixed
    {
        $roles = Roles::query();
        if ($request && $request->has('search')) {
            $roles = $this->search($request->input('search'), $roles);
        }
        if ($request && $request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $roles = $roles->orderBy($sortBy, $sortOrder);
        }
        return $roles->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create roles record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        // $role =
        Roles::create([
            'guard_name' => 'api',
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
        ]);
        // $role->syncPermissions($request->permission ?? []);
        // $spatieRole = Roles::where('name', $role->name)->first();

        // if ($spatieRole) {
        //     $spatieRole->syncPermissions($request->permission ?? []);
        // }
        // SpatieRole::create(['name' => $request->name, 'guard_name' => 'api']);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $roles = Roles::findOrFail($id);
        $roles->update([
            'guard_name' => 'api',
            'name' => $request->name,
            'status' => $request->status,
            'description' => $request->description,
        ]);
        // $roles->syncPermissions($request->permission ?? []);
        // $roleUsers = User::role($roles->name)->get();
        // if (count($roleUsers) > 0) {
        //     foreach ($roleUsers as $r) {
        //         $r->syncPermissions($request->permission);
        //         $r->save();
        //     }
        // }
        // if (!$roles) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        // $roles->update($request->all());
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $roles = Roles::find($id);
        // if (!$roles) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        $roles->delete();
    }

    /**
     * @deprecated message
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // write code here
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
        // return $data->where('name', 'like', '%' . $searchText . '%')->orWhere('status', 'like', '%' . $searchText . '%');
    }

    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     * @return void
     */
    public function filterByDateRange(string $searchText, $data)
    {
        // write code here
    }

    /**
     * Summary of sortData
     * @param string $searchText
     * @param mixed $data
     * @return void
     */
    public function sortData(string $searchText, $data)
    {
        // write code here
    }

    /**
     * Summary of getRolesList
     * @return \Illuminate\Database\Eloquent\Collection<int, Roles>
     */
    public function getRolesList()
    {
        return Roles::where('status', 'Active')->select('id', 'name')->get();
    }
}