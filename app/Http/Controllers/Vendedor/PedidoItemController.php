<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        $pedido = $pedidoItem->pedido()->with('items')->first();
        if ($pedido) {
            $pedido->refreshEstadoGlobalFromItems();
        }

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
        $pedido->refreshEstadoGlobalFromItems();

        return back()->with('ok', 'Estados actualizados');
    }

    public function assignDelivery(Request $request, Pedido $pedido)
    {
        $vendorId = optional(auth()->user()->vendor)->id;
        abort_if(!$vendorId, 403, 'No tienes perfil de vendedor activo.');

        $items = $pedido->items()->where('vendor_id', $vendorId)->get();
        abort_if($items->isEmpty(), 403, 'No tienes productos en este pedido.');

        $data = $request->validate([
            'delivery_mode' => ['required', Rule::in([
                PedidoItem::DELIVERY_VENDOR_SELF,
                PedidoItem::DELIVERY_VENDOR_COURIER,
            ])],
            'repartidor_id' => [
                'nullable',
                'required_if:delivery_mode,' . PedidoItem::DELIVERY_VENDOR_COURIER,
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'repartidor')->where('estado', 'activo')),
            ],
            'delivery_fee'  => ['nullable', 'numeric', 'min:0', 'max:500'],
        ], [
            'repartidor_id.required_if' => 'Selecciona un repartidor disponible para la entrega.',
        ]);

        $deliveryFee = (float) ($data['delivery_fee'] ?? 0);
        $deliveryMode = $data['delivery_mode'];
        $repartidorId = $deliveryMode === PedidoItem::DELIVERY_VENDOR_COURIER
            ? $data['repartidor_id']
            : auth()->id();

        foreach ($items as $index => $item) {
            $item->delivery_mode = $deliveryMode;
            $item->delivery_fee = $index === 0 ? $deliveryFee : 0;
            $item->repartidor_id = $repartidorId;
            $item->save();
        }

        $pedido->refresh();
        $pedido->syncEnvioFromItems();
        $pedido->refreshEstadoGlobalFromItems();

        return back()->with('ok', 'Preferencias de entrega actualizadas.');
    }
}
