<?php

namespace App\Http\Middleware;

use App\Services\Rbac\RbacService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Route -> permission enforcement driven by config/rbac.php.
 * RBAC_MODE: enforce (403 PERMISSION_DENIED) | log (never blocks) | off.
 */
class CheckPermission
{
    public function __construct(private RbacService $rbac)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $mode = strtolower((string) config('rbac.mode', 'enforce'));
        if ($mode === 'off') {
            return $next($request);
        }
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $route = $request->route();
        $uri = RbacService::normaliseUri($route ? $route->uri() : $request->path());
        $method = $request->method();
        $rule = $this->rbac->matchRule($method, $uri);

        if (! $rule) {
            if ($mode === 'log') {
                Log::warning('rbac.unmapped_route', ['method' => $method, 'uri' => $uri, 'user_id' => $user->getKey()]);
            }
            return $next($request);
        }
        if ($this->rbac->isSuper($user)) {
            return $next($request);
        }

        [$allowed, $required, $reason] = $this->evaluate($request, $user, $rule);
        if ($allowed) {
            return $next($request);
        }
        $ctx = [
            'user_id' => $user->getKey(), 'roles' => $this->rbac->roleNames($user),
            'method' => $method, 'uri' => $uri, 'route_name' => $route?->getName(),
            'required_any_of' => $required, 'reason' => $reason, 'mode' => $mode,
        ];
        if ($mode === 'log') {
            Log::warning('rbac.would_deny', $ctx);
            return $next($request);
        }
        Log::info('rbac.denied', $ctx);
        return response()->json([
            'status' => 'error',
            'message' => $reason ?: 'You do not have permission to perform this action.',
            'code' => 'PERMISSION_DENIED',
            'required_permission' => $required[0] ?? null,
            'required_permissions' => $required,
            'errors' => [],
        ], 403);
    }

    /** @return array{0:bool,1:array,2:?string} */
    private function evaluate(Request $request, $user, array $rule): array
    {
        $held = $this->rbac->permissionsFor($user);
        $required = $rule['any'] ?? [];

        // role-restricted rule (e.g. documentation logs)
        if (! empty($rule['roles'])) {
            $ok = $this->rbac->hasRoleIn($user, $rule['roles']);
            return [$ok, [], $ok ? null : 'This area is restricted to administrators.'];
        }
        // param-conditional extra requirement (e.g. /images modal_type=system_settings)
        if (! empty($rule['when'])) {
            $val = (string) $request->input($rule['when']['param']);
            $extra = $rule['when']['values'][$val] ?? null;
            if ($extra) {
                $ok = (bool) array_intersect($extra, $held);
                return [$ok, $extra, $ok ? null : null];
            }
        }
        if (! empty($rule['open'])) {
            return [true, [], null];
        }

        $ok = (bool) array_intersect($required, $held);
        if (! $ok && ! empty($rule['self'])) {
            $id = $request->route('id');
            if ($id !== null && (string) $id === (string) $user->getKey()) {
                $ok = true;
                // a user may edit own profile but never change their own role
                $newRole = $request->input('role');
                if ($newRole && ! in_array($newRole, $this->rbac->roleNames($user), true)) {
                    return [false, $required, 'You cannot change your own role.'];
                }
            }
        }
        if ($ok && ($rule['guard'] ?? null) === 'users') {
            $deny = $this->userGuard($request, $user);
            if ($deny) {
                return [false, $required, $deny];
            }
        }
        return [$ok, $required, null];
    }

    /** Only a Super Admin may create/edit/delete Super Admins or assign that role. */
    private function userGuard(Request $request, $actor): ?string
    {
        $protected = config('rbac.protected_role', 'Super Admin');
        if ($this->rbac->hasRoleIn($actor, [$protected])) {
            return null;
        }
        if (strcasecmp((string) $request->input('role'), $protected) === 0) {
            return "Only a {$protected} can assign the {$protected} role.";
        }
        $id = $request->route('id');
        if ($id !== null && (string) $id !== (string) $actor->getKey()) {
            $target = DB::table('users')->where('id', $id)->first();
            if ($target) {
                $tUser = (new \App\Models\User())->forceFill((array) $target);
                if ($this->rbac->hasRoleIn($tUser, [$protected]) || strcasecmp((string) $target->role, $protected) === 0) {
                    return "Only a {$protected} can modify a {$protected} account.";
                }
            }
        }
        return null;
    }
}
