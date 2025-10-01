<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Producto;
use App\Models\Pedido;

class AdminController extends Controller
{
    public function panel()
    {
        // KPIs
        $clientes_count  = User::where('role', 'cliente')->count();
        // Si tu esquema ya tiene estado_pago = 'pagado', usamos eso; si no, cae a sum(total) general.
        $ingresos        = Pedido::when(
            schema_has_column('pedidos', 'estado_pago'),
            fn($q) => $q->where('estado_pago','pagado')
        )->sum('total');
        $productos_count = Producto::count();

        // Órdenes completadas = pedido sin items pendientes (todos delivered)
        $ordenes_completadas = Pedido::when(
            schema_has_column('pedidos', 'estado_pago'),
            fn($q) => $q->where('estado_pago','pagado')
        )
            ->whereDoesntHave('items', fn($q) => $q->where('fulfillment_status','!=','delivered'))
            ->count();

        // Últimos pedidos (para la tabla)
        $ultimos_pedidos = Pedido::with(['cliente','items'])
            ->latest()->take(5)->get();

        // Ventas por mes (YYYY-MM => total)
        $ventasMes = Pedido::selectRaw('DATE_FORMAT(created_at,"%Y-%m") as m, SUM(total) as t')
            ->when(schema_has_column('pedidos','estado_pago'), fn($q) => $q->where('estado_pago','pagado'))
            ->groupBy('m')->orderBy('m')->pluck('t','m');

        // Notificaciones / repartidores activos (si luego quieres poblarlos desde DB, aquí mismo)
        $notificaciones = [];
        $repartidores_activos = [];

        return view('admin.dashboard', compact(
            'clientes_count',
            'ingresos',
            'productos_count',
            'ordenes_completadas',
            'ultimos_pedidos',
            'ventasMes',
            'notificaciones',
            'repartidores_activos'
        ));
    }
}

/**
 * Helper simple para evitar romper si aún no migraste alguna columna.
 * Puedes mover esto a un Helper global si prefieres.
 */
if (! function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $column): bool {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
