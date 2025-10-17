@extends('layouts.app')

@section('content')
    @php
        use Carbon\Carbon;

        $usuario = auth()->user();
        $estadoActual = $estadoUsuario ?? ($usuario?->estado === 'activo' ? 'Disponible' : 'Fuera de servicio');
        $pedidos = $pedidos ?? collect();
        $historial = $historial ?? collect();
        $stats = array_merge(['pendientes' => 0, 'en_camino' => 0, 'entregados' => 0], $stats ?? []);
        $metricas = array_merge(['entregadosHoy' => 0, 'tiempoPromedioMin' => null], $metricas ?? []);

        $estadoClases = [
            'disponible' => ['label' => 'Disponible', 'class' => 'estado-pill estado-disponible'],
            'ocupado' => ['label' => 'Ocupado', 'class' => 'estado-pill estado-ocupado'],
            'fuera de servicio' => ['label' => 'Fuera de servicio', 'class' => 'estado-pill estado-offline'],
        ];

        $estadoLookupKey = strtolower($estadoActual);
        $estadoConfig = $estadoClases[$estadoLookupKey] ?? $estadoClases['disponible'];

        $metricasRapidas = [
            ['icon' => 'fa-box', 'label' => 'Pedidos pendientes', 'value' => $stats['pendientes']],
            ['icon' => 'fa-truck-moving', 'label' => 'Pedidos en camino', 'value' => $stats['en_camino']],
            ['icon' => 'fa-truck-fast', 'label' => 'Entregados hoy', 'value' => $metricas['entregadosHoy']],
            ['icon' => 'fa-flag-checkered', 'label' => 'Total entregados', 'value' => $stats['entregados']],
        ];

        $metricasExtra = [
            ['icon' => 'fa-stopwatch', 'label' => 'Tiempo prom. entrega', 'value' => $metricas['tiempoPromedioMin'] ? round($metricas['tiempoPromedioMin']) . ' min' : '—'],
            ['icon' => 'fa-road', 'label' => 'Kilómetros recorridos', 'value' => $usuario->kilometros_recorridos ?? '—'],
            ['icon' => 'fa-star', 'label' => 'Calificación promedio', 'value' => $usuario->calificacion_promedio ?? 'N/D'],
        ];
    @endphp

    <style>
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
        .rd-btn-ghost{background:rgba(255,255,255,0.15);color:#fff;}
        .rd-notification{position:relative;}
        .rd-notification .badge{position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border-radius:999px;font-size:11px;padding:2px 6px;font-weight:700;}
        .rd-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;}
        .rd-stat-card{background:#fff;border-radius:18px;padding:20px;border:1px solid #e5e7eb;box-shadow:0 16px 35px rgba(15,23,42,0.08);}
        .rd-stat-card span{display:flex;align-items:center;gap:10px;font-size:13px;color:#6b7280;text-transform:uppercase;font-weight:600;}
        .rd-stat-card strong{font-size:28px;color:#111827;display:block;margin-top:6px;}
        .rd-section{background:#fff;border-radius:22px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 10px 25px rgba(15,23,42,0.06);margin-bottom:24px;}
        .rd-empty{border:2px dashed #cbd5f5;border-radius:20px;padding:40px;text-align:center;background:#fff;}
        .rd-empty h3{font-size:22px;font-weight:800;margin-bottom:8px;color:#111827;}
        .rd-empty p{margin:0;color:#6b7280;}
        .rd-card{background:#f9fafb;border-radius:18px;padding:18px;border:1px solid #e5e7eb;box-shadow:0 12px 25px rgba(15,23,42,0.06);margin-bottom:16px;}
        .rd-card h3{margin:0 0 6px;font-size:18px;color:#111827;}
        .rd-card p{margin:4px 0;color:#374151;font-size:14px;}
        .rd-chip{padding:6px 12px;border-radius:999px;font-size:12px;font-weight:600;text-transform:uppercase;display:inline-flex;align-items:center;gap:6px;}
        .chip-asignado{background:#ede9fe;color:#5b21b6;}
        .chip-aceptado{background:#dbeafe;color:#1d4ed8;}
        .chip-en_camino{background:#dcfce7;color:#166534;}
        .chip-incidencia{background:#fee2e2;color:#b91c1c;}
        .rd-card-actions{display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;}
        .rd-card-actions form{display:inline-flex;gap:8px;align-items:center;}
        .rd-card-actions input[type="file"]{background:#fff;border:1px dashed #93c5fd;padding:6px 8px;border-radius:10px;font-size:12px;color:#1d4ed8;}
        .rd-history-item{display:flex;justify-content:space-between;padding:14px 16px;background:#f9fafb;border-radius:14px;border:1px solid #e5e7eb;margin-bottom:10px;}
        .rd-history-item span{color:#6b7280;font-size:14px;}
        .alert{padding:12px 16px;border-radius:12px;margin-bottom:20px;}
        .alert-success{background:#ecfdf5;color:#047857;}
        .alert-warning{background:#fef9c3;color:#a16207;}
        .alert-error{background:#fee2e2;color:#b91c1c;}
        @media(max-width:640px){.rd-topbar{flex-direction:column;align-items:flex-start;}.rd-stats{grid-template-columns:1fr;}}
    </style>

    <div class="rd-layout">
        <div class="rd-container">
            @foreach (['success' => 'alert-success', 'warning' => 'alert-warning', 'error' => 'alert-error'] as $flashKey => $flashClass)
                @if(session($flashKey))
                    <div class="alert {{ $flashClass }}">
                        {{ session($flashKey) }}
                    </div>
                @endif
            @endforeach

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
                    <a href="{{ route('repartidor.pedidos.asignados') }}" class="rd-btn rd-btn-ghost rd-notification">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Asignados</span>
                        <span class="badge">{{ $pedidos->count() }}</span>
                    </a>
                    <a href="{{ route('repartidor.panel') }}" class="rd-btn"><i class="fas fa-rotate"></i> Refrescar</a>
                </div>
            </div>

            <section class="rd-section">
                <h2><i class="fas fa-gauge-high"></i> Resumen de operación</h2>
                <div class="rd-stats">
                    @foreach($metricasRapidas as $m)
                        <div class="rd-stat-card">
                            <span><i class="fas {{ $m['icon'] }}"></i> {{ $m['label'] }}</span>
                            <strong>{{ $m['value'] }}</strong>
                        </div>
                    @endforeach
                    @foreach($metricasExtra as $m)
                        <div class="rd-stat-card">
                            <span><i class="fas {{ $m['icon'] }}"></i> {{ $m['label'] }}</span>
                            <strong>{{ $m['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            @if($pedidos->isNotEmpty())
                <section class="rd-section">
                    <h2><i class="fas fa-truck"></i> Pedidos asignados</h2>
                    @foreach($pedidos as $pedido)
                        @php
                            $estado = $pedido->estado;
                            $chipClass = [
                                'asignado' => 'chip-asignado',
                                'aceptado' => 'chip-aceptado',
                                'en_camino' => 'chip-en_camino',
                                'incidencia' => 'chip-incidencia',
                            ][$estado] ?? 'chip-asignado';
                            $lat = $pedido->latitud_entrega ?? data_get($pedido->direccion_envio, 'lat');
                            $lng = $pedido->longitud_entrega ?? data_get($pedido->direccion_envio, 'lng');
                            $destinoMapa = $lat && $lng
                                ? $lat . ',' . $lng
                                : ($pedido->direccion_formateada ?: null);
                            $mapLink = $destinoMapa
                                ? (str_contains($destinoMapa, ',')
                                    ? 'https://www.google.com/maps/search/?api=1&query=' . $destinoMapa
                                    : 'https://www.google.com/maps/dir/?api=1&origin=Mi+Ubicacion&destination=' . urlencode($destinoMapa))
                                : null;
                        @endphp
                        <div class="rd-card">
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                                <div>
                                    <h3>{{ $pedido->codigo ?? ('PED-' . $pedido->id) }}</h3>
                                    <p><i class="fas fa-user"></i> {{ optional($pedido->cliente)->name ?? 'Cliente no disponible' }}</p>
                                    <p><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada }}</p>
                                    <p><i class="fas fa-clock"></i> {{ optional($pedido->created_at)->format('d/m/Y H:i') }}</p>
                                    <p><i class="fas fa-wallet"></i> {{ ucfirst($pedido->metodo_pago ?? 'efectivo') }}</p>
                                </div>
                                <span class="rd-chip {{ $chipClass }}"><i class="fas fa-circle"></i> {{ ucfirst(str_replace('_',' ', $estado)) }}</span>
                            </div>

                            @if($pedido->productos->isNotEmpty())
                                <div style="margin-top:12px;">
                                    <strong>Productos:</strong>
                                    <ul style="margin:8px 0 0 18px;padding:0;color:#374151;font-size:14px;">
                                        @foreach($pedido->productos as $producto)
                                            <li>{{ $producto->nombre }} (x{{ $producto->pivot->cantidad ?? 0 }})</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="rd-card-actions">
                                <a href="{{ route('repartidor.pedidos.rastreo', $pedido) }}" class="rd-btn rd-btn-ghost"><i class="fas fa-search"></i> Detalle</a>
                                @if($mapLink)
                                    <a href="{{ $mapLink }}" target="_blank" class="rd-btn rd-btn-ghost"><i class="fas fa-map"></i> Ver ruta</a>
                                @endif

                                @if($estado === 'asignado')
                                    <form action="{{ route('repartidor.pedidos.aceptar', $pedido) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rd-btn"><i class="fas fa-handshake"></i> Aceptar pedido</button>
                                    </form>
                                @endif

                                @if($estado === 'aceptado')
                                    <form action="{{ route('repartidor.pedidos.iniciar', $pedido) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rd-btn rd-btn-ghost"><i class="fas fa-route"></i> Iniciar ruta</button>
                                    </form>
                                @endif

                                @if(in_array($estado, ['aceptado', 'en_camino']))
                                    <form action="{{ route('repartidor.pedidos.entregado', $pedido) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="evidencia_firma" accept="image/*" title="Sube evidencia opcional">
                                        <button type="submit" class="rd-btn"><i class="fas fa-check"></i> Confirmar entrega</button>
                                    </form>
                                @endif

                                <button type="button" class="rd-btn rd-btn-ghost" onclick="reportarIncidencia({{ $pedido->id }})">
                                    <i class="fas fa-triangle-exclamation"></i> Reportar incidencia
                                </button>
                                <form id="incidencia-form-{{ $pedido->id }}" action="{{ route('repartidor.pedidos.incidencia', $pedido) }}" method="POST" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="motivo" value="">
                                </form>
                            </div>
                        </div>
                    @endforeach
                </section>
            @else
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

            <section class="rd-section">
                <h2><i class="fas fa-clock-rotate-left"></i> Historial de pedidos</h2>
                @if($historial->isEmpty())
                    <div class="rd-empty"><p>Aún no tienes entregas completadas.</p></div>
                @else
                    @foreach($historial as $h)
                        <div class="rd-history-item">
                            <div>
                                <strong>{{ $h->codigo ?? ('PED-' . $h->id) }}</strong>
                                <span>{{ optional($h->cliente)->name }}</span>
                            </div>
                            <span>{{ optional($h->fecha_entregado ?? $h->updated_at)->format('d/m H:i') }}</span>
                        </div>
                    @endforeach
                @endif
            </section>
        </div>
    </div>

    <script>
        function reportarIncidencia(id) {
            const motivo = prompt('Describe brevemente la incidencia:');
            if (!motivo) {
                return;
            }
            const form = document.getElementById(`incidencia-form-${id}`);
            if (!form) {
                return;
            }
            form.querySelector('input[name="motivo"]').value = motivo;
            form.submit();
        }
    </script>
@endsection
