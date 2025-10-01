<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;

class PedidoEmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:empleado']);
    }

    /** Panel del empleado (cocina/bodega) */
    public function index()
    {
        $items = PedidoItem::with([
            'pedido.cliente:id,name',
            'producto:id,nombre'
        ])
            ->whereIn('fulfillment_status', ['accepted','preparing'])
            ->orderByRaw("FIELD(fulfillment_status,'accepted','preparing') ASC")
            ->latest()
            ->paginate(30);

        return view('empleado.panel', compact('items'));
    }

    /** Marcar un ítem como PREPARANDO */
    public function preparar(PedidoItem $item)
    {
        $item->update(['fulfillment_status' => 'preparing']);
        $this->syncEstadoPedido($item->pedido);

        return back()->with('ok', 'Ítem marcado como preparando.');
    }

    /** Marcar un ítem como LISTO */
    public function listo(PedidoItem $item)
    {
        $item->update(['fulfillment_status' => 'ready']);
        $this->syncEstadoPedido($item->pedido);

        return back()->with('ok', 'Ítem marcado como listo.');
    }

    /** Recalcula un estado agregado del pedido según sus ítems */
    protected function syncEstadoPedido(Pedido $pedido): void
    {
        $pedido->loadMissing('items');

        $s = $pedido->items->pluck('fulfillment_status');

        $pedido->estado_global =
            $s->every(fn($e) => $e === 'delivered')                      ? 'entregado' :
                ($s->every(fn($e) => in_array($e, ['ready','delivered']))    ? 'listo'     :
                    ($s->contains('preparing')                                  ? 'preparando' : 'pendiente'));

        $pedido->save();
    }
}
