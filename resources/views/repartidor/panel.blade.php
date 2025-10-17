@extends('layouts.app')

@section('content')
    @php
        use Carbon\Carbon;

        $usuario = auth()->user();
        $estadoActual = $estadoUsuario ?? ($usuario?->estado === 'activo' ? 'Disponible' : 'Fuera de servicio');
        $items = $items ?? collect();
        $historial = $historial ?? collect();

        $estadoClases = [
            'disponible' => ['label' => 'Disponible', 'class' => 'estado-pill estado-disponible'],
            'ocupado' => ['label' => 'Ocupado', 'class' => 'estado-pill estado-ocupado'],
            'fuera de servicio' => ['label' => 'Fuera de servicio', 'class' => 'estado-pill estado-offline'],
        ];

        $estadoLookupKey = strtolower($estadoActual);
        $estadoConfig = $estadoClases[$estadoLookupKey] ?? $estadoClases['disponible'];

        $stats = $stats ?? ['pendientes' => 0, 'listos' => 0, 'entregados' => 0];
        $entregadosHoy = $historial->filter(fn($i) => $i->updated_at && Carbon::parse($i->updated_at)->isToday())->count();
        $entregadosTotales = $stats['entregados'] ?? 0;

        $promedioTiempo = $historial->map(function($i){
            $inicio = $i->created_at ? Carbon::parse($i->created_at) : null;
            $fin = $i->updated_at ? Carbon::parse($i->updated_at) : null;
            return ($inicio && $fin) ? $inicio->diffInMinutes($fin) : null;
        })->filter()->avg();

        $metricasRapidas = [
            ['icon'=>'fa-box','label'=>'Pedidos pendientes','value'=>$stats['pendientes']??0],
            ['icon'=>'fa-check-circle','label'=>'Pedidos listos','value'=>$stats['listos']??0],
            ['icon'=>'fa-truck-fast','label'=>'Entregados hoy','value'=>$entregadosHoy],
            ['icon'=>'fa-flag-checkered','label'=>'Total entregados','value'=>$entregadosTotales],
        ];

        $metricasExtra = [
            ['icon'=>'fa-road','label'=>'Kilómetros recorridos','value'=>$usuario->kilometros_recorridos??'—'],
            ['icon'=>'fa-star','label'=>'Calificación promedio','value'=>$usuario->calificacion_promedio??'N/D'],
            ['icon'=>'fa-stopwatch','label'=>'Tiempo prom. entrega','value'=>$promedioTiempo ? round($promedioTiempo).' min':'—'],
        ];
    @endphp

    <style>
        /* ====== DISEÑO PRINCIPAL ====== */
        .rd-layout{background:#f4f6fb;min-height:100vh;padding:24px 0;}
        .rd-container{max-width:1200px;margin:0 auto;padding:0 24px;}
        .rd-topbar{background:#111827;color:#fff;border-radius:20px;padding:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;box-shadow:0 20px 45px rgba(15,23,42,0.25);margin-bottom:28px;position:relative;overflow:hidden;}
        .rd-topbar::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at top right,rgba(59,130,246,0.35),transparent 55%);}
        .rd-topbar>*{position:relative;z-index:1;}
        .rd-brand h1{font-size:24px;font-weight:800;margin:0;color:#fff;}
        .rd-brand p{margin:6px 0 0;font-size:14px;color:rgba(255,255,255,0.75);}
        .rd-user{display:flex;align-items:center;gap:16px;}
        .rd-avatar{width:48px;height:48px;border-radius:14px;overflow:hidden;border:2px solid rgba(255,255,255,0.4);background:#1f2937;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px;text-transform:uppercase;}
        .rd-user-info strong{display:block;font-size:16px;font-weight:700;}
        .rd-user-info span{font-size:13px;color:rgba(255,255,255,0.7);}
        .estado-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;}
        .estado-disponible{background:rgba(34,197,94,0.15);color:#bbf7d0;border:1px solid rgba(34,197,94,0.35);}
        .estado-ocupado{background:rgba(250,204,21,0.15);color:#facc15;border:1px solid rgba(250,204,21,0.35);}
        .estado-offline{background:rgba(148,163,184,0.15);color:#cbd5f5;border:1px solid rgba(148,163,184,0.35);}
        .rd-btn{display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:#fff;padding:10px 16px;border-radius:12px;font-weight:600;font-size:14px;border:none;cursor:pointer;text-decoration:none;}
        .rd-btn:hover{opacity:.9;}
        .rd-btn-ghost{background:rgba(255,255,255,0.15);}
        .rd-notification{position:relative;}
        .rd-notification .badge{position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border-radius:999px;font-size:11px;padding:2px 6px;font-weight:700;}
        .rd-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;}
        .rd-stat-card{background:#fff;border-radius:18px;padding:20px;border:1px solid #e5e7eb;box-shadow:0 16px 35px rgba(15,23,42,0.08);}
        .rd-stat-card span{display:flex;align-items:center;gap:10px;font-size:13px;color:#6b7280;text-transform:uppercase;font-weight:600;}
        .rd-stat-card strong{font-size:28px;color:#111827;}
        .rd-section{background:#fff;border-radius:22px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 10px 25px rgba(15,23,42,0.06);margin-bottom:24px;}
        .rd-empty{border:2px dashed #cbd5f5;border-radius:20px;padding:40px;text-align:center;background:#fff;}
        .rd-empty h3{font-size:22px;font-weight:800;margin-bottom:8px;color:#111827;}
        .rd-empty p{margin:0;color:#6b7280;}
        .rd-card{background:#f9fafb;border-radius:18px;padding:18px;border:1px solid #e5e7eb;box-shadow:0 12px 25px rgba(15,23,42,0.06);}
        .rd-chip{padding:6px 12px;border-radius:999px;font-size:12px;font-weight:600;text-transform:uppercase;}
        .chip-preparando{background:#ede9fe;color:#5b21b6;}
        .chip-camino{background:#dcfce7;color:#166534;}
        .chip-entregado{background:#e0f2fe;color:#0369a1;}
        .chip-rechazado{background:#fee2e2;color:#b91c1c;}
        .rd-map{background:#0f172a;color:#fff;border-radius:20px;padding:24px;margin-top:10px;}
        .rd-map h3{font-size:18px;margin-bottom:8px;}
        .rd-map p{color:rgba(255,255,255,0.7);}
        .rd-history-item{display:flex;justify-content:space-between;padding:14px 16px;background:#f9fafb;border-radius:14px;border:1px solid #e5e7eb;margin-bottom:10px;}
        .rd-history-item span{color:#6b7280;font-size:14px;}
        @media(max-width:640px){.rd-topbar{flex-direction:column;align-items:flex-start;}.rd-stats{grid-template-columns:1fr;}}
    </style>

    <div class="rd-layout">
        <div class="rd-container">

            {{-- HEADER --}}
            <div class="rd-topbar">
                <div class="rd-brand">
                    <h1>Supermercado Atlantia 🛒</h1>
                    <p>Panel del repartidor</p>
                </div>
                <div class="rd-user">
                    <div class="rd-avatar">
                        {{ $usuario && $usuario->foto ? '' : \Illuminate\Support\Str::of($usuario->name ?? 'R')->substr(0,2)->upper() }}
                        @if($usuario && $usuario->foto)
                            <img src="{{ $usuario->foto }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                    <div class="rd-user-info">
                        <strong>{{ $usuario->name ?? 'Repartidor' }}</strong>
                        <span>{{ $usuario->email ?? 'sin correo' }}</span>
                        <div class="{{ $estadoConfig['class'] }}"><i class="fas fa-circle"></i> {{ $estadoConfig['label'] }}</div>
                    </div>
                </div>
                <div style="display:flex;gap:10px;">
                    <button class="rd-btn rd-btn-ghost rd-notification"><i class="fas fa-bell"></i> <span>Notificaciones</span> <span class="badge">{{ $items->count() }}</span></button>
                    <a href="{{ route('repartidor.panel') }}" class="rd-btn"><i class="fas fa-rotate"></i> Refrescar</a>
                </div>
            </div>

            {{-- DASHBOARD --}}
            <section class="rd-section">
                <h2><i class="fas fa-gauge-high"></i> Resumen de operación</h2>
                <div class="rd-stats">
                    @foreach($metricasRapidas as $m)
                        <div class="rd-stat-card"><span><i class="fas {{ $m['icon'] }}"></i> {{ $m['label'] }}</span><strong>{{ $m['value'] }}</strong></div>
                    @endforeach
                    @foreach($metricasExtra as $m)
                        <div class="rd-stat-card"><span><i class="fas {{ $m['icon'] }}"></i> {{ $m['label'] }}</span><strong>{{ $m['value'] }}</strong></div>
                    @endforeach
                </div>
            </section>

            {{-- PEDIDOS ASIGNADOS --}}
            @if($items->isNotEmpty())
                <section class="rd-section">
                    <h2><i class="fas fa-truck"></i> Pedidos asignados</h2>
                    @foreach($items as $item)
                        @php
                            $pedido = $item->pedido;
                            $cliente = optional($pedido)->cliente;
                            $estado = $item->fulfillment_status ?? 'preparando';
                            $claseEstado = [
                                'preparando'=>'chip-preparando',
                                'en_camino'=>'chip-camino',
                                'entregado'=>'chip-entregado',
                                'rechazado'=>'chip-rechazado'
                            ][$estado] ?? 'chip-preparando';
                        @endphp
                        <div class="rd-card">
                            <strong>{{ $pedido?->codigo ?? 'PED-'.$item->id }}</strong>
                            <p><i class="fas fa-user"></i> {{ $cliente?->name ?? 'Cliente no disponible' }}</p>
                            <p><i class="fas fa-map-marker-alt"></i> {{ $pedido?->direccion ?? 'Sin dirección' }}</p>
                            <p><i class="fas fa-clock"></i> {{ optional($item->created_at)->format('d/m H:i') }}</p>
                            <p><i class="fas fa-wallet"></i> {{ ucfirst($pedido?->metodo_pago ?? 'Efectivo') }}</p>
                            <span class="rd-chip {{ $claseEstado }}"><i class="fas fa-circle"></i> {{ ucfirst(str_replace('_',' ',$estado)) }}</span>
                            <div style="display:flex;gap:10px;margin-top:10px;flex-wrap:wrap;">
                                <button class="rd-btn rd-btn-ghost"><i class="fas fa-search"></i> Detalle</button>
                                <a href="https://www.google.com/maps?q={{ $pedido->lat ?? 0 }},{{ $pedido->lng ?? 0 }}" target="_blank" class="rd-btn rd-btn-ghost"><i class="fas fa-map"></i> Ver ruta</a>
                                <button class="rd-btn"><i class="fas fa-check"></i> Confirmar entrega</button>
                                <button class="rd-btn rd-btn-ghost"><i class="fas fa-ban"></i> Rechazar</button>
                            </div>
                        </div>
                    @endforeach
                </section>

                {{-- MAPA INTERACTIVO --}}
                <section class="rd-map">
                    <h3><i class="fas fa-map-location-dot"></i> Mapa de entregas</h3>
                    <p>Aquí puedes visualizar tu ubicación actual y las rutas de entrega activas.</p>
                    <div style="background:rgba(255,255,255,0.1);border-radius:14px;padding:20px;text-align:center;">[Mapa interactivo aquí]</div>
                </section>
            @else

                {{-- SIN PEDIDOS --}}
                <section class="rd-section">
                    <h2><i class="fas fa-box-open"></i> Sin pedidos asignados</h2>
                    <div class="rd-empty">
                        <h3>🚫 No tienes pedidos asignados</h3>
                        <p>Permanece atento o actualiza el panel.</p>
                        <div style="margin-top:20px;display:grid;gap:10px;">
                            <a href="{{ route('repartidor.panel') }}" class="rd-btn"><i class="fas fa-rotate"></i> Actualizar</a>
                            <a href="{{ route('repartidor.pedidos.entregados') }}" class="rd-btn rd-btn-ghost"><i class="fas fa-clock"></i> Ver historial</a>
                            <a href="{{ route('repartidor.perfil') }}" class="rd-btn rd-btn-ghost"><i class="fas fa-chart-line"></i> Desempeño</a>
                        </div>
                    </div>
                </section>
            @endif

            {{-- HISTORIAL --}}
            <section class="rd-section">
                <h2><i class="fas fa-clock-rotate-left"></i> Historial de pedidos</h2>
                @if($historial->isEmpty())
                    <div class="rd-empty"><p>Aún no tienes entregas completadas.</p></div>
                @else
                    @foreach($historial as $h)
                        <div class="rd-history-item">
                            <div><strong>{{ $h->pedido?->codigo ?? 'PED-'.$h->id }}</strong> <span>{{ $h->pedido?->cliente?->name }}</span></div>
                            <span>{{ optional($h->updated_at)->format('d/m H:i') }}</span>
                        </div>
                    @endforeach
                @endif
            </section>

            {{-- RECURSOS Y HERRAMIENTAS --}}
            <section class="rd-section">
                <h2><i class="fas fa-toolbox"></i> Recursos y herramientas</h2>
                <div class="rd-stats">
                    <div class="rd-card"><strong><i class="fas fa-comments"></i> Chat interno</strong><p>Comunícate con administradores o vendedores.</p></div>
                    <div class="rd-card"><strong><i class="fas fa-calendar"></i> Rutas programadas</strong><p>Consulta tus próximas entregas.</p></div>
                    <div class="rd-card"><strong><i class="fas fa-coins"></i> Comisiones y pagos</strong><p>Consulta tus montos acumulados.</p></div>
                    <div class="rd-card"><strong><i class="fas fa-life-ring"></i> Soporte y ayuda</strong><p>Accede a políticas y contacto técnico.</p></div>
                </div>
            </section>
        </div>
    </div>
@endsection
