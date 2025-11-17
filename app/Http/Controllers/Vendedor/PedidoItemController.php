<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\VendorDeliveryZone;
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
                PedidoItem::DELIVERY_MARKET_COURIER,
            ])],
            'repartidor_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($request->input('delivery_mode'), [
                    PedidoItem::DELIVERY_VENDOR_COURIER,
                    PedidoItem::DELIVERY_MARKET_COURIER,
                ], true)),
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'repartidor')->where('estado', 'activo')),
            ],
            'vendor_zone_id' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($request->input('delivery_mode'), [
                    PedidoItem::DELIVERY_VENDOR_SELF,
                    PedidoItem::DELIVERY_VENDOR_COURIER,
                ], true)),
                Rule::exists('vendor_delivery_zones', 'id')->where(fn ($q) => $q->where('seller_id', $vendorId)),
            ],
            'pickup_contact' => ['nullable', 'string', 'max:120', 'required_if:delivery_mode,' . PedidoItem::DELIVERY_MARKET_COURIER],
            'pickup_phone'   => ['nullable', 'string', 'max:45', 'required_if:delivery_mode,' . PedidoItem::DELIVERY_MARKET_COURIER],
            'pickup_address' => ['nullable', 'string', 'max:255', 'required_if:delivery_mode,' . PedidoItem::DELIVERY_MARKET_COURIER],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
        ], [
            'repartidor_id.required_if' => 'Selecciona un repartidor disponible para la entrega.',
            'vendor_zone_id.required_if' => 'Selecciona la zona que cubrirás al repartir.',
            'pickup_contact.required_if' => 'Ingresa el contacto para coordinar la recolección.',
            'pickup_phone.required_if' => 'Agrega el teléfono de contacto para la recolección.',
            'pickup_address.required_if' => 'Indica la dirección donde el repartidor recogerá el producto.',
        ]);

        $deliveryMode = $data['delivery_mode'];
        $vendorZoneId = $data['vendor_zone_id'] ?? null;
        $vendorZoneId = $vendorZoneId ? (int) $vendorZoneId : null;
        $marketCourierFee = (float) config('market.courier_fee', 20);

        $deliveryFee = 0;
        $zone = null;

        if ($vendorZoneId) {
            $zone = VendorDeliveryZone::where('seller_id', $vendorId)
                ->where('activa', true)
                ->where('id', $vendorZoneId)
                ->first();
        }

        if (in_array($deliveryMode, [PedidoItem::DELIVERY_VENDOR_SELF, PedidoItem::DELIVERY_VENDOR_COURIER], true)) {
            if (!$zone) {
                return back()->withErrors([
                    'vendor_zone_id' => 'Selecciona una zona válida para calcular la tarifa.',
                ]);
            }
            $deliveryFee = (float) $zone->tarifa_reparto;
        }

        if ($deliveryMode === PedidoItem::DELIVERY_MARKET_COURIER) {
            $vendorZoneId = null;
            $deliveryFee = $marketCourierFee;
        }

        $repartidorId = match ($deliveryMode) {
            PedidoItem::DELIVERY_VENDOR_SELF    => auth()->id(),
            PedidoItem::DELIVERY_VENDOR_COURIER => $data['repartidor_id'],
            PedidoItem::DELIVERY_MARKET_COURIER => $data['repartidor_id'],
            default                              => null,
        };

        foreach ($items as $index => $item) {
            $item->delivery_mode = $deliveryMode;
            $item->delivery_fee = $index === 0 ? $deliveryFee : 0;
            $item->repartidor_id = $repartidorId;
            $item->vendor_zone_id = $vendorZoneId;
            if ($deliveryMode === PedidoItem::DELIVERY_MARKET_COURIER) {
                $item->pickup_contact = $data['pickup_contact'];
                $item->pickup_phone = $data['pickup_phone'];
                $item->pickup_address = $data['pickup_address'];
                $item->delivery_instructions = $data['delivery_instructions'];
            } else {
                $item->pickup_contact = null;
                $item->pickup_phone = null;
                $item->pickup_address = null;
                $item->delivery_instructions = null;
            }
            $item->save();
        }

        $pedido->refresh();
        $pedido->syncEnvioFromItems();
        $pedido->refreshEstadoGlobalFromItems();

        if ($deliveryMode === PedidoItem::DELIVERY_MARKET_COURIER && $repartidorId) {
            $pedido->forceFill([
                'repartidor_id'  => $repartidorId,
                'estado'         => 'asignado',
                'fecha_asignado' => now(),
            ])->save();
        }

        return back()->with('ok', 'Preferencias de entrega actualizadas.');
    }
}
