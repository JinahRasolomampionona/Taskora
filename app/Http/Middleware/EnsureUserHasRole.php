<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint un groupe de routes a un ou plusieurs roles.
 *
 * Usage : ->middleware('role:admin') ou ->middleware('role:admin,user')
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Ce compte a ete desactive.']);
        }

        if (! $user->hasRole(...$roles)) {
            abort(403, "Vous n'avez pas acces a cette section de Taskora.");
        }

        return $next($request);
    }
}
