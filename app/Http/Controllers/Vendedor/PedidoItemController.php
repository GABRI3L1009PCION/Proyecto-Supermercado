<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;

class PedidoItemController extends Controller
{
    public function updateStatus(Request $request, PedidoItem $pedidoItem)
    {
        $request->validate([
            'estado' => 'required|in:accepted,preparing,ready,delivered,rejected',
        ]);

        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        // Dueño del ítem (preferimos vendor_id del propio pedido_item)
        $ownerId = $pedidoItem->vendor_id ?? optional($pedidoItem->producto)->vendor_id;

        if ((int) $ownerId !== (int) $vendorId) {
            abort(403, 'No tienes permiso para actualizar este item.');
        }

        // No permitir cambios si ya fue entregado (opcional)
        if ($pedidoItem->fulfillment_status === 'delivered') {
            return back()->with('error', 'Este item ya fue entregado.');
        }

        $pedidoItem->fulfillment_status = $request->estado;
        $pedidoItem->save();

        // Recalcular estado global del pedido
        $pedido = $pedidoItem->pedido()->with('items')->first();
        $pedido->estado_global = $this->calcularEstadoGlobal($pedido);
        $pedido->save();

        return back()->with('ok', 'Estado del ítem actualizado');
    }

    public function updateAllStatus(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:accepted,preparing,ready,delivered,rejected',
        ]);

        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        // Solo los ítems de este vendor dentro del pedido
        $items = $pedido->items()
            ->where('vendor_id', $vendorId)           // << directo a pedido_items
            ->get();

        foreach ($items as $i) {
            // si quieres evitar sobrescribir entregados:
            // if ($i->fulfillment_status !== 'delivered') {
            $i->fulfillment_status = $request->estado;
            $i->save();
            // }
        }

        $pedido->refresh();
        $pedido->estado_global = $this->calcularEstadoGlobal($pedido);
        $pedido->save();

        return back()->with('ok', 'Estados actualizados');
    }

    private function calcularEstadoGlobal(Pedido $pedido): string
    {
        $s = $pedido->items->pluck('fulfillment_status');

        if ($s->every(fn ($e) => $e === 'delivered')) return 'entregado';
        if ($s->every(fn ($e) => in_array($e, ['ready', 'delivered']))) return 'listo';
        if ($s->contains('preparing')) return 'preparando';

        return 'pendiente';
    }
}
