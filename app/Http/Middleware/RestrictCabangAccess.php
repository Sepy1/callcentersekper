<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictCabangAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'cabang') {
            return $next($request);
        }

        if (
            $request->routeIs('cabang.*')
            || $request->routeIs('dashboard')
            || $request->routeIs('logout')
            || $request->path() === '/'
        ) {
            return $next($request);
        }

        abort(403, 'Akun cabang tidak memiliki akses ke halaman ini.');
    }
}
