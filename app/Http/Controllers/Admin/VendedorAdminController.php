<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendedorAdminController extends Controller
{
    /** Listado de vendedores */
    public function index()
    {
        $vendors = Vendor::with('user')->latest()->paginate(20);
        return view('admin.vendedores.index', compact('vendors'));
    }

    /** Form para crear/promover vendedor */
    public function create()
    {
        // Usuarios que NO son vendedor aún (y opcionalmente sin vendor creado)
        $usuarios = User::where(function ($q) {
            $q->whereNull('role')->orWhere('role', '!=', 'vendedor');
        })
            ->whereDoesntHave('vendor') // si tienes ->vendor en User
            ->get(['id','name','email']);

        return view('admin.vendedores.create', compact('usuarios'));
    }

    /** Guardar: modo usuario existente o crear nuevo */
    public function store(Request $r)
    {
        $mode = $r->input('mode', 'existing'); // 'existing' | 'new'

        if ($mode === 'new') {
            $r->validate([
                'name'            => ['required','string','max:120'],
                'email'           => ['required','email','max:150','unique:users,email'],
                'password'        => ['required','string','min:8'],
                'pricing_mode'    => ['nullable', Rule::in(['markup','commission'])],
                'commission_rate' => ['nullable','numeric','between:0,100'],
                'service_area'    => ['nullable','string','max:255'],
                'payout_bank_info'=> ['nullable','string','max:500'],
            ]);

            $user = User::create([
                'name'     => $r->name,
                'email'    => $r->email,
                'password' => bcrypt($r->password),
            ]);
        } else {
            $r->validate([
                'user_id'         => ['required', Rule::exists('users','id')],
                'pricing_mode'    => ['nullable', Rule::in(['markup','commission'])],
                'commission_rate' => ['nullable','numeric','between:0,100'],
                'service_area'    => ['nullable','string','max:255'],
                'payout_bank_info'=> ['nullable','string','max:500'],
            ]);
            $user = User::findOrFail($r->user_id);
        }

        // Asignar rol "vendedor" (tú usas RoleMiddleware con campo 'role')
        if ($user->role !== 'vendedor') {
            $user->role = 'vendedor';
            $user->save();
        }

        // Crear Vendor (si ya existe, no duplica)
        Vendor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'status'           => 'active',                           // activo desde ya
                'pricing_mode'     => $r->input('pricing_mode', 'markup'),// no cobras, da igual
                'commission_rate'  => $r->input('commission_rate', 0),    // 0 por ahora
                'service_area'     => $r->input('service_area'),
                'payout_bank_info' => $r->input('payout_bank_info'),
            ]
        );

        return redirect()
            ->route('admin.vendedores.index')
            ->with('ok', 'Vendedor creado/promovido correctamente.');
    }

    /** Activar/Suspender vendedor */
    public function toggleStatus(Vendor $vendor)
    {
        $vendor->update([
            'status' => $vendor->status === 'active' ? 'suspended' : 'active'
        ]);

        return back()->with('ok', "Estado actualizado a: {$vendor->status}");
    }

    /** (Opcional) Editar datos del vendor */
    public function edit(Vendor $vendor)
    {
        return view('admin.vendedores.edit', compact('vendor'));
    }

    /** (Opcional) Actualizar datos del vendor */
    public function update(Request $r, Vendor $vendor)
    {
        $r->validate([
            'pricing_mode'    => ['nullable', Rule::in(['markup','commission'])],
            'commission_rate' => ['nullable','numeric','between:0,100'],
            'service_area'    => ['nullable','string','max:255'],
            'payout_bank_info'=> ['nullable','string','max:500'],
            'status'          => ['nullable', Rule::in(['active','suspended'])],
        ]);

        $vendor->update([
            'pricing_mode'     => $r->input('pricing_mode', $vendor->pricing_mode),
            'commission_rate'  => $r->input('commission_rate', $vendor->commission_rate),
            'service_area'     => $r->service_area,
            'payout_bank_info' => $r->payout_bank_info,
            'status'           => $r->input('status', $vendor->status),
        ]);

        return redirect()
            ->route('admin.vendedores.index')
            ->with('ok', 'Vendedor actualizado.');
    }
}
