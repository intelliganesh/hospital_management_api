<?php

namespace App\Http\Controllers;

use App\Services\Rbac\RbacService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RbacController extends Controller
{
    use ResponseTrait;

    public function __construct(private RbacService $rbac)
    {
    }

    /** GET /permissions_list -> [{module, permissions:[{key,label}]}] */
    public function permissions()
    {
        $groups = [];
        foreach (config('rbac.catalog') as $module => $items) {
            $perms = [];
            foreach ($items as $key => $label) {
                $perms[] = ['key' => $key, 'label' => $label, 'module' => $module];
            }
            $groups[] = ['module' => $module, 'permissions' => $perms];
        }
        return $this->successResponse($groups);
    }

    private function role($id)
    {
        return DB::table('roles')->where('id', $id)->where('guard_name', $this->rbac->guard())->first();
    }

    /** GET /role_permissions/{role_id} (id as returned by /get_roles_list) */
    public function rolePermissions($roleId)
    {
        $role = $this->role($roleId);
        if (! $role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found.', 'errors' => []], 404);
        }
        return $this->successResponse(['role' => ['id' => $role->id, 'name' => $role->name], 'permissions' => $this->granted($role)]);
    }

    private function granted($role): array
    {
        if (in_array($role->name, config('rbac.super_roles'), true)) {
            return $this->rbac->allKeys();
        }
        $p = DB::table('role_has_permissions')->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('role_has_permissions.role_id', $role->id)->pluck('permissions.name')->all();
        sort($p);
        return $p;
    }

    /** PUT /role_permissions/{role_id} {permissions:[keys]} (full sync) */
    public function syncRolePermissions(Request $request, $roleId)
    {
        $role = $this->role($roleId);
        if (! $role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found.', 'errors' => []], 404);
        }
        $payload = $request->input('permissions');
        if (! is_array($payload)) {
            return response()->json(['status' => 'error', 'message' => 'Validation error', 'errors' => ['permissions' => ['The permissions field must be an array.']]], 422);
        }
        $keys = $this->rbac->allKeys();
        $unknown = array_values(array_diff($payload, $keys));
        if ($unknown) {
            return response()->json(['status' => 'error', 'message' => 'Validation error', 'errors' => ['permissions' => ['Unknown permission keys: ' . implode(', ', $unknown)]]], 422);
        }
        $actor = auth('api')->user();
        $isSuperRole = in_array($role->name, config('rbac.super_roles'), true);
        if ($isSuperRole) {
            if (! $this->rbac->isSuper($actor)) {
                return response()->json(['status' => 'error', 'message' => 'Only a Super Admin can change the Super Admin role.', 'code' => 'PERMISSION_DENIED', 'errors' => []], 403);
            }
            $payload = $keys; // Super Admin can never be emptied
        }
        if ($role->name === 'Admin') {
            $payload[] = 'roles_manage'; // admins cannot lock themselves out of role management
        }
        $payload = array_values(array_unique($payload));
        DB::transaction(function () use ($role, $payload) {
            $now = now();
            foreach ($payload as $k) {
                if (! DB::table('permissions')->where('name', $k)->where('guard_name', $this->rbac->guard())->exists()) {
                    DB::table('permissions')->insert(['name' => $k, 'guard_name' => $this->rbac->guard(), 'created_at' => $now, 'updated_at' => $now]);
                }
            }
            $ids = DB::table('permissions')->where('guard_name', $this->rbac->guard())->whereIn('name', $payload)->pluck('id')->all();
            DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
            foreach ($ids as $id) {
                DB::table('role_has_permissions')->insert(['permission_id' => $id, 'role_id' => $role->id]);
            }
        });
        $this->rbac->forgetCache();
        sort($payload);
        return $this->successResponse(['role' => ['id' => $role->id, 'name' => $role->name], 'permissions' => $payload], 'Role permissions updated successfully');
    }
}
