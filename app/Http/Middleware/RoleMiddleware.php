<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Sprawdzanie czy użytkownik ma wymaganą rolę
        // Zakładamy, że model User ma metodę hasRole
        if (!auth()->user()->hasRole($role)) {
            abort(403, 'Brak dostępu - wymagana rola: ' . $role);
        }

        return $next($request);
    }
}
