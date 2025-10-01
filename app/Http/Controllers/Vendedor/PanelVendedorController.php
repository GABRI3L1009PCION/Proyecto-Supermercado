<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\PedidoItem;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PanelVendedorController extends Controller
{
    public function index()
    {
        $vendorId = Auth::user()->vendor->id ?? null;

        // KPIs
        $kpis = [
            'mis_productos' => Producto::where('vendor_id', $vendorId)->count(),
            'pendientes'    => PedidoItem::where('vendor_id', $vendorId)
                ->whereIn('fulfillment_status', ['accepted','preparing','ready'])
                ->count(),
            'vendidos_mes'  => PedidoItem::where('vendor_id', $vendorId)
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum(DB::raw('cantidad * precio_unitario')),
        ];

        // Cargar últimos ítems con pedido, cliente y producto
        $items = PedidoItem::with([
            'pedido.cliente:id,name', // cliente asociado al pedido
            'producto:id,nombre'
        ])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->take(10)
            ->get();

        return view('vendedor.dashboard', compact('kpis', 'items'));
    }
}
