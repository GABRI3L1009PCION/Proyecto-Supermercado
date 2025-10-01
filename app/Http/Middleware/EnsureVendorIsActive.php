<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureVendorIsActive
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = $request->user();

            // Verificar que el usuario está autenticado
            if (!$user) {
                abort(403, 'Usuario no autenticado.');
            }

            // Verificar que el usuario tiene rol de vendedor
            if ($user->role !== 'vendedor') {
                abort(403, 'No tienes acceso al panel de vendedor.');
            }

            // Verificar la relación vendor
            if (!$user->vendor) {
                abort(403, 'Perfil de vendedor no encontrado. Contacta con administración.');
            }

            // Verificar el estado del vendedor (usando 'status' en lugar de 'estado')
            if ($user->vendor->status !== 'active') {
                abort(403, 'Tu cuenta de vendedor está inactiva o suspendida.');
            }

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Error en middleware EnsureVendorIsActive: ' . $e->getMessage());

            // Redirigir al dashboard con mensaje de error
            return redirect()->route('vendedor.dashboard')
                ->with('error', 'Error de acceso: ' . $e->getMessage());
        }
    }
}
