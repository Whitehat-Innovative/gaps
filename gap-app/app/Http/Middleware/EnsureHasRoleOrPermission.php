<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHasRoleOrPermission
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes (after registering alias in Kernel):
     * ->middleware('role_or_permission:admin|edit posts')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $rolesOrPermissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $rolesOrPermissions = null)
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        if (! $rolesOrPermissions) {
            // If no roles/permissions provided, allow (or deny) — choose deny for security
            abort(403, 'This action is unauthorized.');
        }

        // allow multiple values separated by | or ,
        $items = preg_split('/[|,]/', $rolesOrPermissions);

        foreach ($items as $item) {
            $item = trim($item);

            if ($item === '') {
                continue;
            }

            // Use Spatie methods (requires HasRoles trait on User model)
            if (method_exists($user, 'hasRole') && $user->hasRole($item)) {
                return $next($request);
            }

            if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($item)) {
                return $next($request);
            }
        }

        abort(403, 'This action is unauthorized.');
    }
}
