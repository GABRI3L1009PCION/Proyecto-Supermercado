<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RepartidorAdminController extends Controller
{
    // Mostrar lista de repartidores
    public function index()
    {
        $repartidores = User::where('role', 'repartidor')->get();
        return view('admin.repartidores.index', compact('repartidores'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('admin.repartidores.create');
    }

    // Guardar nuevo repartidor
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $user = new User();
        $user->name = $request->nombre;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->estado = $request->estado;
        $user->role = 'repartidor';
        $user->save();

        return redirect()->route('admin.repartidores.index')
            ->with('success', 'Repartidor registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $repartidor = User::findOrFail($id);
        return view('admin.repartidores.edit', compact('repartidor'));
    }

    // Actualizar repartidor
    public function update(Request $request, $id)
    {
        $repartidor = User::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $id,
            'estado' => 'required|in:activo,inactivo',
            'password' => 'nullable|string|min:6',
        ]);

        $repartidor->name = $request->nombre;
        $repartidor->telefono = $request->telefono;
        $repartidor->email = $request->email;
        $repartidor->estado = $request->estado;

        // Actualizar contraseña solo si se envía
        if ($request->filled('password')) {
            $repartidor->password = Hash::make($request->password);
        }

        $repartidor->save();

        return redirect()->route('admin.repartidores.index')
            ->with('success', 'Repartidor actualizado correctamente.');
    }

    // Eliminar repartidor
    public function destroy($id)
    {
        $repartidor = User::findOrFail($id);
        $repartidor->delete();

        return redirect()->route('admin.repartidores.index')
            ->with('success', 'Repartidor eliminado.');
    }
}
