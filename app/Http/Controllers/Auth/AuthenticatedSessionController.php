<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no son correctas.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Redirigir según el rol
        return $this->redirectToRole($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirigir según el rol del usuario
     */
    protected function redirectToRole($user): RedirectResponse
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.panel');
            case 'empleado':
                return redirect()->route('empleado.panel');
            case 'repartidor':
                return redirect()->route('repartidor.panel');
            case 'vendedor': // <-- NUEVO
                return redirect()->route('vendedor.dashboard');
            case 'cliente':
            default:
                return redirect()->route('cliente.productos');
        }
    }
}
