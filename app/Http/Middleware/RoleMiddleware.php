<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect('/login');
        }

        if (!in_array(\Illuminate\Support\Facades\Auth::user()->role, $roles)) {
            abort(403, 'Akses tidak diizinkan. Halaman ini butuh role: ' . implode(',', $roles));
        }

        return $next($request);
    }
}
