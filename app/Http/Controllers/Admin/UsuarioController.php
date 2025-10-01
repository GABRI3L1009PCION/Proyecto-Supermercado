<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /** Listado */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.usuarios.index', compact('users'));
    }

    /** Form de creación */
    public function create()
    {
        return view('admin.usuarios.create');
    }

    /** Guardar nuevo */
    public function store(Request $request)
    {
        // normaliza
        $request->merge([
            'role'   => strtolower($request->input('role')),
            'estado' => strtolower($request->input('estado', 'activo')),
        ]);

        $request->validate([
            'name'      => ['required','string','max:120'],
            'email'     => ['required','email','max:150','unique:users,email'],
            'password'  => ['required','string','min:6','confirmed'],
            'role'      => ['required', Rule::in(['admin','empleado','repartidor','cliente','vendedor'])],
            'telefono'  => ['nullable','string','max:25'],
            'estado'    => ['required', Rule::in(['activo','inactivo'])],

            // Datos de vendedor (opcionales)
            'v_service_area'     => ['nullable','string','max:255'],
            'v_pricing_mode'     => ['nullable', Rule::in(['markup','commission'])],
            'v_commission_rate'  => ['nullable','numeric','between:0,100'],
            'v_bank'             => ['nullable','string','max:500'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'telefono' => $request->telefono,
            'estado'   => $request->estado,
        ]);

        // Si es vendedor: crear Vendor
        $this->syncVendor($user, $request);

        return redirect()->route('admin.usuarios.index')
            ->with('ok', 'Usuario creado correctamente.');
    }

    /** Form de edición */
    public function edit($id)
    {
        $user = User::with('vendor')->findOrFail($id);
        return view('admin.usuarios.edit', compact('user'));
    }

    /** Actualizar */
    public function update(Request $request, $id)
    {
        $user = User::with('vendor')->findOrFail($id);

        // normaliza
        $request->merge([
            'role'   => strtolower($request->input('role', $user->role)),
            'estado' => strtolower($request->input('estado', $user->estado)),
        ]);

        $request->validate([
            'name'      => ['required','string','max:120'],
            'email'     => ['required','email','max:150', Rule::unique('users','email')->ignore($user->id)],
            'password'  => ['nullable','string','min:6','confirmed'],
            'role'      => ['required', Rule::in(['admin','empleado','repartidor','cliente','vendedor'])],
            'telefono'  => ['nullable','string','max:25'],
            'estado'    => ['required', Rule::in(['activo','inactivo'])],

            // Datos de vendedor (opcionales)
            'v_service_area'     => ['nullable','string','max:255'],
            'v_pricing_mode'     => ['nullable', Rule::in(['markup','commission'])],
            'v_commission_rate'  => ['nullable','numeric','between:0,100'],
            'v_bank'             => ['nullable','string','max:500'],
        ]);

        // Actualiza datos base
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->telefono = $request->telefono;
        $user->estado   = $request->estado;
        $user->role     = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Sincroniza Vendor según rol
        $this->syncVendor($user, $request);

        return redirect()->route('admin.usuarios.index')
            ->with('ok', 'Usuario actualizado correctamente.');
    }

    /** Eliminar */
    public function destroy($id)
    {
        $user = User::with('vendor')->findOrFail($id);

        // Evitar que un usuario se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Evitar quedarte sin administradores
        if ($user->role === 'admin' && User::where('role','admin')->count() <= 1) {
            return back()->with('error', 'No puedes eliminar al último administrador.');
        }

        // Si tiene vendor, lo marcamos suspendido (o elimínalo si prefieres)
        if ($user->vendor) {
            $user->vendor->update(['status' => 'suspended']);
            // O: $user->vendor->delete();
        }

        $user->delete();

        return back()->with('ok', 'Usuario eliminado.');
    }

    /**
     * Crea/actualiza/suspende el Vendor según el rol.
     */
    protected function syncVendor(User $user, Request $request): void
    {
        if ($user->role === 'vendedor') {
            Vendor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'status'           => 'active',
                    'service_area'     => $request->input('v_service_area'),
                    'pricing_mode'     => $request->input('v_pricing_mode', 'markup'),
                    'commission_rate'  => (float) $request->input('v_commission_rate', 0),
                    'payout_bank_info' => $request->input('v_bank'),
                ]
            );
        } else {
            // Si dejó de ser vendedor, suspende vendor si existía
            if ($user->vendor) {
                $user->vendor->update(['status' => 'suspended']);
            }
        }
    }
}
