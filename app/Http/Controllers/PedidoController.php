<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // Página de seguimiento para el CLIENTE
    public function showCliente(Pedido $pedido)
    {
        // Asegurar que el pedido es del usuario logueado
        abort_unless($pedido->user_id === auth()->id(), 403);

        // Cargar relaciones necesarias
        $pedido->load(['cliente', 'items.producto', 'repartidor']);

        // (Opcional) Guardar en sesión para fallback en la vista
        session([
            'pedido_id'      => $pedido->id,
            'pedido_codigo'  => $pedido->codigo,
            'pedido_realizado' => true,
        ]);

        // Renderiza la vista de seguimiento del cliente (la moderna que ya hicimos)
        return view('cliente.pedido_confirmado', [
            'pedidoId'        => $pedido->id,
            'pedidoCodigo'    => $pedido->codigo,
            'pedidoRealizado' => true,
        ]);
    }

    // JSON para el polling del estado en la vista del cliente
    public function estadoJson(Pedido $pedido)
    {
        abort_unless($pedido->user_id === auth()->id(), 403);

        $pedido->load(['items.producto', 'repartidor']);

        return response()->json([
            'id'        => $pedido->id,
            'codigo'    => $pedido->codigo,
            'estado'    => $pedido->estado_global,           // ajusta al nombre real de tu columna
            'direccion' => $pedido->direccion_envio,         // string o array, según tu modelo
            'total'     => $pedido->total,                    // ajusta si tu campo se llama distinto
            'updated_at'=> $pedido->updated_at,

            'repartidor'=> $pedido->repartidor ? [
                'nombre'   => $pedido->repartidor->nombre,
                'telefono' => $pedido->repartidor->telefono,
                'foto'     => $pedido->repartidor->foto_url ?? null,
            ] : null,

            'productos' => $pedido->items->map(function ($it) {
                return [
                    'nombre'   => $it->producto->nombre ?? '',
                    'precio'   => $it->precio_unitario,
                    'cantidad' => $it->cantidad,
                ];
            })->values(),
        ]);
    }
}
