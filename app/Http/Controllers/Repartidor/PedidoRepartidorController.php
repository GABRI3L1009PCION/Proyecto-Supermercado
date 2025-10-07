<?php

namespace App\Http\Controllers\Repartidor;

use App\Http\Controllers\Controller;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoRepartidorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:repartidor']);
    }

    public function index(Request $request)
    {
        $repartidorId = Auth::id();

        $activos = PedidoItem::with([
            'pedido:id,cliente_id,direccion_envio,metodo_pago,codigo',
            'pedido.cliente:id,name,telefono,email',
            'producto:id,nombre',
        ])
            ->where('repartidor_id', $repartidorId)
            ->whereNotIn('fulfillment_status', [
                PedidoItem::ESTADO_ENTREGADO,
                PedidoItem::ESTADO_RECHAZADO,
                PedidoItem::ESTADO_CANCELADO,
            ])
            ->orderByRaw("FIELD(fulfillment_status,'ready','preparing','accepted') ASC")
            ->latest()
            ->get();

        $historial = PedidoItem::with([
            'pedido:id,cliente_id,direccion_envio,metodo_pago,codigo',
            'pedido.cliente:id,name,telefono,email',
            'producto:id,nombre',
        ])
            ->where('repartidor_id', $repartidorId)
            ->where('fulfillment_status', PedidoItem::ESTADO_ENTREGADO)
            ->latest('updated_at')
            ->take(10)
            ->get();

        $stats = [
            'pendientes' => $activos->whereIn('fulfillment_status', [PedidoItem::ESTADO_ACEPTADO, PedidoItem::ESTADO_PREPARANDO])->count(),
            'listos'     => $activos->where('fulfillment_status', PedidoItem::ESTADO_LISTO)->count(),
            'entregados' => PedidoItem::where('repartidor_id', $repartidorId)
                ->where('fulfillment_status', PedidoItem::ESTADO_ENTREGADO)
                ->count(),
        ];

        return view('repartidor.panel', [
            'items'     => $activos,
            'historial' => $historial,
            'stats'     => $stats,
        ]);
    }

    public function entregar(PedidoItem $item)
    {
        $this->ensureOwnsItem($item);

        if ($item->fulfillment_status !== PedidoItem::ESTADO_LISTO) {
            return back()->with('error', 'Solo puedes cerrar entregas que estén marcadas como listas para entregar.');
        }

        $item->marcarComoEntregado();

        $pedido = $item->pedido;
        if ($pedido) {
            $pedido->refreshEstadoGlobalFromItems();
        }

        return back()->with('success', '¡Entrega confirmada! Gracias por tu trabajo.');
    }

    protected function ensureOwnsItem(PedidoItem $item): void
    {
        abort_unless((int) $item->repartidor_id === (int) Auth::id(), 403, 'No tienes acceso a esta entrega.');
    }
}
