<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ajusta 'rol' según tu BD
        if (!auth()->check() || auth()->user()->rol !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}
