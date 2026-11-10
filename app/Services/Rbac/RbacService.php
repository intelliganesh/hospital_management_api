<?php

namespace App\Services\Rbac;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

/**
 * Server-side RBAC helper: permission catalogue, per-user permission resolution,
 * route -> requirement matching and idempotent seeding. Guard-agnostic (always
 * works on guard "api" rows) so it does not depend on AUTH_GUARD.
 */
class RbacService
{
    private array $memo = [];

    public function guard(): string
    {
        return config('rbac.guard', 'api');
    }

    /** @return array<string,array{label:string,module:string}> */
    public function catalog(): array
    {
        $out = [];
        foreach (config('rbac.catalog', []) as $module => $items) {
            foreach ($items as $key => $label) {
                $out[$key] = ['label' => $label, 'module' => $module];
            }
        }
        return $out;
    }

    public function allKeys(): array
    {
        return array_keys($this->catalog());
    }

    public function roleNames($user): array
    {
        $k = 'roles.' . $user->getKey();
        if (isset($this->memo[$k])) {
            return $this->memo[$k];
        }
        $names = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $user->getKey())
            ->where('model_has_roles.model_type', get_class($user))
            ->where('roles.guard_name', $this->guard())
            ->pluck('roles.name')->all();
        if (! $names && ! empty($user->role)) {
            $names = [$user->role]; // fallback to users.role column
        }
        return $this->memo[$k] = array_values(array_unique($names));
    }

    public function isSuper($user): bool
    {
        return (bool) array_intersect($this->roleNames($user), config('rbac.super_roles', []));
    }

    public function hasRoleIn($user, array $roles): bool
    {
        return (bool) array_intersect($this->roleNames($user), $roles);
    }

    /** permission names the user holds (Super Admin: whole catalogue + DB). */
    public function permissionsFor($user): array
    {
        $k = 'perms.' . $user->getKey();
        if (isset($this->memo[$k])) {
            return $this->memo[$k];
        }
        if ($this->isSuper($user)) {
            $p = array_unique(array_merge($this->allKeys(), DB::table('permissions')->where('guard_name', $this->guard())->pluck('name')->all()));
        } else {
            $roles = $this->roleNames($user);
            $p = DB::table('role_has_permissions')
                ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->whereIn('roles.name', $roles)->where('roles.guard_name', $this->guard())
                ->pluck('permissions.name')->all();
            $direct = DB::table('model_has_permissions')
                ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
                ->where('model_has_permissions.model_id', $user->getKey())
                ->where('model_has_permissions.model_type', get_class($user))
                ->pluck('permissions.name')->all();
            $p = array_unique(array_merge($p, $direct));
        }
        sort($p);
        return $this->memo[$k] = array_values($p);
    }

    /** first matching rule for method + route uri (without "api/"), or null */
    public function matchRule(string $method, string $uri): ?array
    {
        foreach (config('rbac.rules', []) as $rule) {
            if (in_array($method, $rule['methods'], true) && Str::is($rule['pattern'], $uri)) {
                return $rule;
            }
        }
        return null;
    }

    public static function normaliseUri(string $uri): string
    {
        return preg_replace('#^api/#', '', ltrim($uri, '/'));
    }

    // ------------------------------------------------------------- seeding
    /** Idempotent and additive: creates missing permissions, grants (never revokes). */
    public function seed(): array
    {
        $now = now();
        $g = $this->guard();
        $created = 0;
        $keys = $this->allKeys();
        foreach ($keys as $k) {
            $exists = DB::table('permissions')->where('name', $k)->where('guard_name', $g)->exists();
            if (! $exists) {
                DB::table('permissions')->insert(['name' => $k, 'guard_name' => $g, 'created_at' => $now, 'updated_at' => $now]);
                $created++;
            }
        }
        $permIds = DB::table('permissions')->where('guard_name', $g)->pluck('id', 'name')->all();
        $granted = 0;
        $matrix = config('rbac.roles', []);
        $matrix['Super Admin'] = $keys;
        foreach ($matrix as $roleName => $perms) {
            $role = DB::table('roles')->where('name', $roleName)->where('guard_name', $g)->first();
            if (! $role) {
                continue;
            }
            $perms = array_merge($perms, config("rbac.additions.$roleName", []));
            foreach (config('rbac.implied', []) as $has => $extra) {
                if (in_array($has, $perms, true)) {
                    $perms = array_merge($perms, $extra);
                }
            }
            foreach (array_unique($perms) as $p) {
                if (! isset($permIds[$p])) {
                    continue;
                }
                $granted += DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $permIds[$p], 'role_id' => $role->id]);
            }
        }
        $this->forgetCache();
        return ['permissions_created' => $created, 'grants_added' => $granted];
    }

    public function forgetCache(): void
    {
        try {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {
            // cache store unavailable: ignore
        }
    }
}
