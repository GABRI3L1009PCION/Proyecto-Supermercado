<?php

namespace App\Http\Controllers\Repartidor;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RepartidorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // agrega 'verified' o 'role:repartidor' si lo usas
    }

    // --- VISTAS ---

    public function index()
    {
        $id = auth()->id();

        $stats = [
            'pendientes' => Pedido::where('repartidor_id', $id)->where('estado', 'asignado')->count(),
            'en_camino'  => Pedido::where('repartidor_id', $id)->where('estado', 'en_camino')->count(),
            'entregados' => Pedido::where('repartidor_id', $id)->where('estado', 'entregado')->count(),
        ];

        $pedidos = Pedido::with(['cliente:id,name,telefono', 'productos'])
            ->where('repartidor_id', $id)
            ->whereIn('estado', ['asignado', 'aceptado', 'en_camino'])
            ->orderByRaw("FIELD(estado,'asignado','aceptado','en_camino')")
            ->latest()
            ->get();

        $historial = Pedido::with('cliente:id,name')
            ->where('repartidor_id', $id)
            ->where('estado', 'entregado')
            ->latest('fecha_entregado')
            ->take(10)
            ->get();

        $metricas = [
            'entregadosHoy' => Pedido::where('repartidor_id', $id)
                ->where('estado', 'entregado')
                ->whereDate('fecha_entregado', now())->count(),
            'tiempoPromedioMin' => Pedido::where('repartidor_id', $id)
                ->where('estado', 'entregado')
                ->whereNotNull('fecha_salida')->whereNotNull('fecha_entregado')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, fecha_salida, fecha_entregado)) as m')->value('m'),
        ];

        $estadoUsuario = 'Disponible'; // cámbialo si tienes lógica de presencia

        return view('repartidor.panel', compact('stats', 'pedidos', 'metricas', 'estadoUsuario', 'historial'));
    }


    public function pedidosAsignados()
    {
        $id = Auth::id();

        $pedidos = Pedido::with(['cliente:id,name,telefono', 'productos'])
            ->where('repartidor_id', $id)
            ->whereIn('estado', ['asignado', 'aceptado', 'en_camino'])
            ->orderByRaw("FIELD(estado,'asignado','aceptado','en_camino')")
            ->latest()
            ->get();

        return view('repartidor.pedidos_asignados', compact('pedidos'));
    }

    public function pedidosEntregados()
    {
        $id = Auth::id();

        $pedidos = Pedido::with('cliente:id,name')
            ->where('repartidor_id', $id)
            ->where('estado', 'entregado')
            ->latest('fecha_entregado')
            ->get();

        return view('repartidor.pedidos_entregados', compact('pedidos'));
    }

    public function rastreo(Pedido $pedido)
    {
        $this->ensureOwns($pedido);
        return view('repartidor.rastreo', compact('pedido'));
    }

    public function perfil()
    {
        return view('repartidor.perfil');
    }

    public function soporte()
    {
        return view('repartidor.soporte');
    }

    // --- ACCIONES ESTADO ---

    public function aceptar(Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        if ($pedido->estado !== 'asignado') {
            return back()->with('warning', 'Este pedido ya fue aceptado o procesado.');
        }

        $pedido->forceFill([
            'estado' => 'aceptado',
            'fecha_aceptado' => now(),
        ])->save();

        return back()->with('success', 'Has aceptado el pedido correctamente.');
    }

    public function iniciarRuta(Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        if (!in_array($pedido->estado, ['aceptado', 'asignado'])) {
            return back()->with('warning', 'No se puede iniciar ruta en el estado actual.');
        }

        $pedido->forceFill([
            'estado' => 'en_camino',
            'fecha_salida' => now(),
        ])->save();

        return back()->with('success', 'Ruta iniciada.');
    }

    public function marcarProblema(Request $request, Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $pedido->forceFill([
            'estado' => 'incidencia',
            'motivo_incidencia' => $data['motivo'],
            'fecha_incidencia' => now(),
        ])->save();

        return back()->with('success', 'Incidencia registrada.');
    }

    public function confirmarEntrega(Request $request, Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        if (!in_array($pedido->estado, ['en_camino', 'aceptado'])) {
            return back()->with('warning', 'Solo se pueden entregar pedidos en ruta o aceptados.');
        }

        DB::transaction(function () use ($pedido, $request) {
            $pedido->estado = 'entregado';
            $pedido->fecha_entregado = now();
            $pedido->estado_global = 'entregado';

            if ($request->hasFile('evidencia_firma')) {
                $pedido->evidencia_firma = $request->file('evidencia_firma')->store('firmas', 'public');
            }

            $pedido->save();
        });

        return back()->with('success', 'Entrega confirmada con éxito.');
    }

    // --- API para tu JS (fetch /api/repartidor/*) ---

    public function apiDatos()
    {
        $u  = Auth::user();
        $id = $u->id;

        $pendientes = Pedido::where('repartidor_id', $id)->where('estado', 'asignado')->count();
        $enCamino   = Pedido::where('repartidor_id', $id)->where('estado', 'en_camino')->count();
        $entregados = Pedido::where('repartidor_id', $id)->where('estado', 'entregado')->count();

        $hoy = Carbon::today();
        $entregadosHoy = Pedido::where('repartidor_id', $id)
            ->where('estado', 'entregado')
            ->whereDate('fecha_entregado', $hoy)
            ->count();

        $tiempoProm = Pedido::where('repartidor_id', $id)
            ->where('estado', 'entregado')
            ->whereNotNull('fecha_salida')
            ->whereNotNull('fecha_entregado')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, fecha_salida, fecha_entregado)) as min_prom')
            ->value('min_prom');

        return response()->json([
            'nombre' => $u->name,
            'estado' => method_exists($u, 'is_online') && $u->is_online() ? 'Disponible' : 'No disponible',
            'pedidosPendientes' => $pendientes,
            'pedidosEnCamino'   => $enCamino,
            'pedidosEntregados' => $entregados,
            'metricas' => [
                'entregadosHoy' => $entregadosHoy,
                'tiempoPromedioMin' => $tiempoProm ? round($tiempoProm) : null,
                // 'kmRecorridos' y 'calificacion' si tienes esos campos
            ],
        ]);
    }

    public function apiPedidos()
    {
        $id = Auth::id();

        $pedidos = Pedido::with([
            'cliente:id,name',
            'productos:id'
        ])
            ->where('repartidor_id', $id)
            ->whereIn('estado', ['asignado', 'aceptado', 'en_camino'])
            ->latest()
            ->get()
            ->map(function (Pedido $p) {
                return [
                    'id'        => $p->id,
                    'codigo'    => $p->codigo ?? ('PED-' . $p->id),
                    'cliente'   => optional($p->cliente)->name,
                    'direccion' => $p->direccion_formateada,
                    'horaLimite'=> optional($p->hora_limite_entrega)?->format('H:i') ?? '--:--',
                    'urgente'   => (bool)($p->urgente ?? false),
                    'estado'    => $p->estado,
                    'items'     => $p->productos->map(fn($it) => [
                        'nombre'  => $it->nombre,
                        'cantidad'=> $it->pivot->cantidad ?? 0,
                    ]),
                ];
            });

        return response()->json($pedidos);
    }

    public function apiPedidoDetalle(Pedido $pedido)
    {
        $this->ensureOwns($pedido);

        $pedido->load(['cliente', 'productos']);

        return response()->json([
            'id' => $pedido->id,
            'codigo' => $pedido->codigo ?? ('PED-' . $pedido->id),
            'estado' => $pedido->estado,
            'cliente' => [
                'nombre'    => optional($pedido->cliente)->name,
                'telefono'  => optional($pedido->cliente)->telefono,
                'direccion' => $pedido->direccion_formateada,
                'lat'       => $pedido->latitud_entrega ?? data_get($pedido->direccion_envio, 'lat'),
                'lng'       => $pedido->longitud_entrega ?? data_get($pedido->direccion_envio, 'lng'),
            ],
            'totales' => [
                'subtotal' => $pedido->subtotal,
                'envio'    => $pedido->envio,
                'total'    => $pedido->total,
            ],
            'productos' => $pedido->productos->map(fn($it) => [
                'nombre'  => $it->nombre,
                'cantidad'=> $it->pivot->cantidad ?? 0,
                'precio'  => $it->pivot->precio_unitario ?? 0,
            ]),
        ]);
    }

    // --- Helpers ---

    private function ensureOwns(Pedido $pedido): void
    {
        abort_unless($pedido->repartidor_id === Auth::id(), 403, 'No autorizado');
    }
}
