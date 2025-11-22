<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->rol === 'administrador') {
            return $next($request);
        }

        return redirect()->route('panel')
            ->with('error', 'Acceso denegado. No tienes permisos de administrador.');
    }
}
