<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with([
            'cliente:id,name,email,telefono',
            'repartidor:id,name,telefono',
            'itemsSupermercado.producto:id,nombre,precio,vendor_id',
        ])->latest()->get();

        // CORREGIDO: Usar 'role' en lugar de 'rol'
        $repartidores = User::where('role', 'repartidor')->where('estado', 'activo')->get();

        $totalPedidos = $pedidos->count();
        $pendientesCount = $pedidos->where('estado_global', 'pendiente')->count();
        $preparandoCount = $pedidos->where('estado_global', 'preparando')->count();
        $listosCount = $pedidos->where('estado_global', 'listo')->count();
        $entregadosCount = $pedidos->where('estado_global', 'entregado')->count();

        return view('admin.pedidos.index', compact(
            'pedidos',
            'repartidores',
            'totalPedidos',
            'pendientesCount',
            'preparandoCount',
            'listosCount',
            'entregadosCount'
        ));
    }

    public function show($id)
    {
        $pedido = Pedido::with([
            'cliente:id,name,email,telefono',
            'repartidor:id,name,telefono',
            'itemsSupermercado.producto:id,nombre,precio,vendor_id',
        ])->findOrFail($id);

        return response()->json($pedido);
    }

    public function asignarRepartidor(Request $request, $id)
    {
        $data = $request->validate([
            'repartidor_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'repartidor')->where('estado', 'activo')),
            ],
            'delivery_fee'  => ['nullable', 'numeric', 'min:0', 'max:500'],
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->forceFill([
            'repartidor_id'   => $request->repartidor_id,
            'estado_global'   => 'preparando',
            'estado'          => 'asignado',
            'fecha_asignado'  => now(),
        ])->save();

        $pedido->refresh();
        $pedido->syncEnvioFromItems();
        $pedido->refreshEstadoGlobalFromItems();

        return redirect()->route('admin.pedidos.index')->with('success', 'Repartidor asignado correctamente a los productos del supermercado.');
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:preparando,listo,entregado'
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->estado_global = $request->estado;
        $pedido->save();

        return redirect()->route('admin.pedidos.index')->with('success', 'Estado del pedido actualizado correctamente.');
    }

    public function asignar(Request $request, $pedido)
    {
        $request->validate([
            'repartidor_id' => 'required|exists:users,id'
        ]);

        $pedido = Pedido::findOrFail($pedido);
        $pedido->forceFill([
            'repartidor_id'  => $request->repartidor_id,
            'estado_global'  => 'preparando',
            'estado'         => 'asignado',
            'fecha_asignado' => now(),
        ])->save();

        return redirect()->route('admin.pedidos.index')->with('success', 'Repartidor asignado correctamente.');
    }
}
