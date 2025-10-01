<?php

namespace App\Http\Controllers\Repartidor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PedidoRepartidorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:repartidor']);
    }

    /**
     * Panel del repartidor.
     */
    public function index(Request $request)
    {
        $repartidorId = Auth::id();

        // Contadores del header (tratamos 'aceptado' como activo)
        $stats = [
            'pendientes' => Pedido::where('repartidor_id', $repartidorId)
                ->whereIn('estado', ['asignado','aceptado'])
                ->count(),
            'en_camino'  => Pedido::where('repartidor_id', $repartidorId)
                ->where('estado', 'en_camino')
                ->count(),
            'entregados' => Pedido::where('repartidor_id', $repartidorId)
                ->where('estado', 'entregado')
                ->count(),
        ];

        // Métricas opcionales (solo si existen columnas)
        $metricas = ['entregadosHoy' => 0, 'tiempoPromedioMin' => null];

        if (Schema::hasColumn('pedidos', 'fecha_entregado')) {
            $metricas['entregadosHoy'] = Pedido::where('repartidor_id', $repartidorId)
                ->where('estado', 'entregado')
                ->whereDate('fecha_entregado', now())
                ->count();
        }

        if (Schema::hasColumn('pedidos', 'fecha_salida') && Schema::hasColumn('pedidos', 'fecha_entregado')) {
            $metricas['tiempoPromedioMin'] = Pedido::where('repartidor_id', $repartidorId)
                ->where('estado', 'entregado')
                ->whereNotNull('fecha_salida')
                ->whereNotNull('fecha_entregado')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, fecha_salida, fecha_entregado)) AS m')
                ->value('m');
        }

        // Lista para el panel (activos)
        $pedidos = Pedido::with([
            // usa la relación que tengas; probamos ambas y tomamos la que exista en showJson
            'user:id,name', 'cliente:id,name',
            'productos', 'productos.producto:id,nombre'
        ])
            ->where('repartidor_id', $repartidorId)
            ->whereIn('estado', ['asignado','aceptado','en_camino'])
            ->orderByRaw("FIELD(estado,'asignado','aceptado','en_camino') ASC")
            ->latest()
            ->get();

        $estadoUsuario = 'Disponible';

        // Tu Blade espera estas 4 variables
        return view('repartidor.panel', compact('stats','pedidos','metricas','estadoUsuario'));
    }

    /**
     * Aceptar pedido.
     * - Caso A: Admin ya lo asignó -> estado 'asignado' y repartidor_id = Auth::id()
     * - Caso B (opcional): Pedido 'listo' sin repartidor -> el repartidor se lo adjudica
     */
    public function aceptar(Pedido $pedido)
    {
        $repartidorId = Auth::id();

        // Caso A: ya asignado a mí
        if ($pedido->estado === 'asignado' && (int)$pedido->repartidor_id === (int)$repartidorId) {
            $update = ['estado' => 'aceptado'];
            if (Schema::hasColumn('pedidos','fecha_aceptado')) {
                $update['fecha_aceptado'] = now();
            }
            $pedido->forceFill($update)->save();
            return back()->with('success', 'Has aceptado el pedido.');
        }

        // Caso B: pedido libre (listo) sin repartidor
        if ($pedido->estado === 'listo' && is_null($pedido->repartidor_id)) {
            $update = [
                'repartidor_id' => $repartidorId,
                'estado'        => 'aceptado',
            ];
            if (Schema::hasColumn('pedidos','fecha_aceptado')) {
                $update['fecha_aceptado'] = now();
            }
            $pedido->forceFill($update)->save();
            return back()->with('success', 'Has tomado el pedido.');
        }

        return back()->with('warning', 'El pedido no se puede aceptar en su estado actual.');
    }

    /**
     * Iniciar la ruta de entrega.
     */
    public function iniciar(Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        if (!in_array($pedido->estado, ['asignado','aceptado'], true)) {
            return back()->with('warning', 'No se puede iniciar la ruta en el estado actual.');
        }

        $update = ['estado' => 'en_camino'];
        if (Schema::hasColumn('pedidos','fecha_salida')) {
            $update['fecha_salida'] = now();
        }

        $pedido->forceFill($update)->save();

        return back()->with('success', 'Ruta iniciada.');
    }

    /**
     * Marcar como entregado.
     */
    public function entregar(Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        if ($pedido->estado !== 'en_camino') {
            return back()->with('warning', 'Solo puedes entregar pedidos en ruta.');
        }

        DB::transaction(function () use ($pedido) {
            $pedido->estado = 'entregado';
            if (Schema::hasColumn('pedidos','fecha_entregado')) {
                $pedido->fecha_entregado = now();
            }
            $pedido->save();
        });

        return back()->with('success', 'Pedido marcado como entregado.');
    }

    /**
     * JSON para detalle (panel repartidor).
     * Tolerante con nombres de columnas y relaciones.
     */
    public function showJson(Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        // Carga flexible: si no existe 'cliente', al menos 'user'
        $pedido->loadMissing(['cliente','user','productos.producto']);

        $cliente = $pedido->cliente ?? $pedido->user;

        // Dirección / coords con fallback de nombres
        $dir = $pedido->direccion_entrega
            ?? $pedido->direccion
            ?? optional($cliente)->direccion
            ?? null;

        $lat = $pedido->latitud_entrega
            ?? $pedido->latitud
            ?? $pedido->lat
            ?? null;

        $lng = $pedido->longitud_entrega
            ?? $pedido->longitud
            ?? $pedido->lng
            ?? null;

        return response()->json([
            'id'       => $pedido->id,
            'codigo'   => $pedido->codigo ?? ('PED-'.$pedido->id),
            'estado'   => (string)$pedido->estado,
            'cliente'  => [
                'nombre'   => $cliente->name ?? $cliente->nombre ?? null,
                'telefono' => $cliente->telefono ?? null,
                'direccion'=> $dir,
            ],
            'direccion'=> $dir,
            'lat'      => $lat,
            'lng'      => $lng,
            'total'    => (float)$pedido->total,
            'productos'=> $pedido->productos->map(fn ($pp) => [
                'producto' => optional($pp->producto)->nombre ?? $pp->nombre ?? 'Producto',
                'cantidad' => (int)$pp->cantidad,
                'precio'   => (float)($pp->precio_unitario ?? 0),
            ])->values(),
            'updated_at'=> optional($pedido->updated_at)->toIso8601String(),
        ]);
    }

    /**
     * Asegura que el pedido pertenece al repartidor autenticado.
     */
    protected function ensureOwns(Pedido $pedido): void
    {
        abort_unless((int)$pedido->repartidor_id === (int)Auth::id(), 403, 'No autorizado');
    }
}
