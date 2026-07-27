<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->withErrors(['email' => 'Votre compte est désactivé.']);
        }

        $userRole = strtolower(trim((string) $user->role));
        $allowedRoles = array_map(static fn (string $role): string => strtolower(trim($role)), $roles);

        if (! in_array($userRole, $allowedRoles, true)) {
            Log::warning('Role authorization denied.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'required_roles' => $roles,
                'path' => $request->path(),
            ]);

            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
