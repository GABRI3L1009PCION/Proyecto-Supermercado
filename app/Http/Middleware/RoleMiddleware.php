<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Si el usuario no está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Si el usuario tiene el rol requerido
        if (Auth::user()->role === $role) {
            return $next($request);
        }

        // Si el rol no coincide, redirigir a la página principal o mostrar error 403
        abort(403, 'No tienes permiso para acceder a esta sección.');
        // o puedes usar: return redirect('/');
    }
}
