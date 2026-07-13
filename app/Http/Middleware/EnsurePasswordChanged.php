<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->mustChangePassword()) {
            if (! $request->is('change-password', 'logout', 'change-password/store')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
