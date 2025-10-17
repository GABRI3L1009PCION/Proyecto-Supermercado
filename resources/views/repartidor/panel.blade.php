@extends('layouts.app')

@section('content')
    @php
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
        $entregadosHoy = $historial->filter(fn ($item) => optional($item->updated_at)->isToday())->count();
        $entregadosTotales = $stats['entregados'] ?? 0;
        $promedioTiempo = $historial
            ->map(function ($item) {
                $inicio = $item->created_at;
                $fin = $item->updated_at;

                if (! $inicio || ! $fin) {
                    return null;
                }

                return $inicio->diffInMinutes($fin);
            })
            ->filter()
            ->avg();

        $metricasRapidas = [
            ['icon' => 'fa-box', 'label' => 'Pedidos pendientes', 'value' => $stats['pendientes'] ?? 0],
            ['icon' => 'fa-check-circle', 'label' => 'Pedidos listos', 'value' => $stats['listos'] ?? 0],
            ['icon' => 'fa-truck-fast', 'label' => 'Entregados hoy', 'value' => $entregadosHoy],
            ['icon' => 'fa-flag-checkered', 'label' => 'Total entregados', 'value' => $entregadosTotales],
        ];

        $metricasExtra = [
            ['icon' => 'fa-road', 'label' => 'Kilómetros recorridos', 'value' => $usuario->kilometros_recorridos ?? '—'],
            ['icon' => 'fa-star', 'label' => 'Calificación promedio', 'value' => $usuario->calificacion_promedio ?? 'N/D'],
            ['icon' => 'fa-stopwatch', 'label' => 'Tiempo prom. entrega', 'value' => $promedioTiempo ? round($promedioTiempo) . ' min' : '—'],
        ];
    @endphp

    <style>
        .rd-layout {background:#f4f6fb;min-height:100vh;padding:24px 0;}
        .rd-container{max-width:1200px;margin:0 auto;padding:0 24px;}
        .rd-topbar{background:#111827;color:#fff;border-radius:20px;padding:24px;display:flex;align-items:center;justify-content:space-between;gap:16px;box-shadow:0 20px 45px rgba(15,23,42,0.25);margin-bottom:28px;position:relative;overflow:hidden;}
        .rd-topbar::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at top right,rgba(59,130,246,0.35),transparent 55%);z-index:0;}
        .rd-topbar > *{position:relative;z-index:1;}
        .rd-brand h1{font-size:24px;font-weight:800;margin:0;color:#fff;display:flex;align-items:center;gap:12px;}
        .rd-brand p{margin:6px 0 0;font-size:14px;color:rgba(255,255,255,0.75);}
        .rd-user{display:flex;align-items:center;gap:16px;}
        .rd-avatar{width:48px;height:48px;border-radius:14px;overflow:hidden;border:2px solid rgba(255,255,255,0.4);background:#1f2937;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px;text-transform:uppercase;}
        .rd-user-info strong{display:block;font-size:16px;font-weight:700;}
        .rd-user-info span{font-size:13px;color:rgba(255,255,255,0.7);}
        .estado-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;}
        .estado-disponible{background:rgba(34,197,94,0.15);color:#bbf7d0;border:1px solid rgba(34,197,94,0.35);}
        .estado-ocupado{background:rgba(250,204,21,0.15);color:#facc15;border:1px solid rgba(250,204,21,0.35);}
        .estado-offline{background:rgba(148,163,184,0.15);color:#cbd5f5;border:1px solid rgba(148,163,184,0.35);}
        .rd-top-actions{display:flex;align-items:center;gap:14px;}
        .rd-btn{display:inline-flex;align-items:center;gap:8px;background:#1f2937;color:#fff;padding:10px 16px;border-radius:12px;font-weight:600;font-size:14px;text-decoration:none;transition:transform .2s,box-shadow .2s;border:none;cursor:pointer;}
        .rd-btn:hover{transform:translateY(-1px);box-shadow:0 12px 20px rgba(15,23,42,0.18);}        
        .rd-btn-secondary{background:#2563eb;}
        .rd-btn-ghost{background:rgba(255,255,255,0.1);}
        .rd-notification{position:relative;}
        .rd-notification .badge{position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border-radius:999px;font-size:11px;padding:2px 6px;font-weight:700;}
        .rd-grid{display:grid;gap:24px;}
        .rd-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;}
        .rd-stat-card{background:#fff;border-radius:18px;padding:20px;border:1px solid #e5e7eb;box-shadow:0 16px 35px rgba(15,23,42,0.08);}
        .rd-stat-card span{display:flex;align-items:center;gap:10px;font-size:13px;color:#6b7280;text-transform:uppercase;font-weight:600;letter-spacing:.05em;margin-bottom:6px;}
        .rd-stat-card strong{font-size:30px;color:#111827;display:block;}
        .rd-section{background:#fff;border-radius:22px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 16px 35px rgba(15,23,42,0.06);}        
        .rd-section h2{font-size:20px;font-weight:800;margin:0 0 4px;color:#111827;display:flex;align-items:center;gap:10px;}
        .rd-section > p{margin:0 0 18px;color:#6b7280;font-size:14px;}
        .pedido-card{border:1px solid #e5e7eb;border-radius:18px;padding:20px;display:flex;flex-direction:column;gap:18px;box-shadow:0 14px 24px rgba(15,23,42,0.06);}
        .pedido-header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;}
        .pedido-header strong{font-size:18px;color:#1f2937;}
        .pedido-meta{display:flex;flex-wrap:wrap;gap:12px;font-size:13px;color:#6b7280;}
        .pedido-body{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;font-size:14px;color:#374151;}
        .pedido-body span{display:flex;flex-direction:column;background:#f9fafb;border-radius:12px;padding:12px;}
        .pedido-body span small{font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;}
        .pedido-actions{display:flex;flex-wrap:wrap;gap:10px;}
        .rd-chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;padding:6px 12px;border-radius:999px;text-transform:uppercase;letter-spacing:.05em;}
        .chip-preparing{background:#ede9fe;color:#5b21b6;}
        .chip-ready{background:#dcfce7;color:#166534;}
        .chip-accepted{background:#fef3c7;color:#92400e;}
        .chip-entregado{background:#e0f2fe;color:#0369a1;}
        .chip-rechazado{background:#fee2e2;color:#b91c1c;}
        .rd-map{background:#0f172a;color:#fff;border-radius:20px;padding:24px;display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:center;position:relative;overflow:hidden;}
        .rd-map::after{content:"";position:absolute;inset:0;background:radial-gradient(circle at top left,rgba(37,99,235,0.3),transparent 60%);}
        .rd-map > *{position:relative;z-index:1;}
        .rd-map h3{margin:0 0 10px;font-size:18px;font-weight:700;}
        .rd-map p{margin:0 0 18px;color:rgba(255,255,255,0.75);}
        .rd-map-preview{background:rgba(15,23,42,0.65);border-radius:16px;min-height:220px;display:flex;align-items:center;justify-content:center;font-size:14px;color:rgba(255,255,255,0.65);text-align:center;padding:16px;border:1px dashed rgba(148,163,184,0.6);}        
        .rd-map-actions{display:flex;flex-direction:column;gap:10px;}
        .rd-empty{border:2px dashed #cbd5f5;border-radius:20px;padding:40px;text-align:center;background:#fff;}
        .rd-empty h3{font-size:22px;font-weight:800;margin-bottom:8px;color:#111827;}
        .rd-empty p{margin:0;color:#6b7280;}
        .rd-options{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:24px;}
        .rd-option-card{background:#fff;border-radius:18px;padding:18px;border:1px solid #e5e7eb;display:flex;flex-direction:column;gap:8px;box-shadow:0 12px 30px rgba(15,23,42,0.05);}
        .rd-option-card strong{font-size:16px;color:#1f2937;}
        .rd-option-card p{margin:0;font-size:13px;color:#6b7280;}
        .rd-option-card a{margin-top:auto;font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;}
        .rd-history{display:flex;flex-direction:column;gap:14px;}
        .rd-history-item{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-radius:14px;background:#f9fafb;border:1px solid #e5e7eb;font-size:14px;}
        .rd-history-item strong{color:#111827;}
        .rd-history-item span{color:#6b7280;}
        .rd-history-empty{padding:26px;border-radius:18px;text-align:center;background:#f9fafb;color:#6b7280;border:1px dashed #d1d5db;}
        .rd-sections-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;}
        .rd-section-card{background:#fff;border-radius:18px;padding:20px;border:1px solid #e5e7eb;box-shadow:0 10px 26px rgba(15,23,42,0.05);display:flex;flex-direction:column;gap:10px;}
        .rd-section-card h4{margin:0;font-size:16px;font-weight:700;color:#1f2937;}
        .rd-section-card p{margin:0;color:#6b7280;font-size:13px;}
        .rd-section-card a{margin-top:auto;font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;}
        @media(max-width:960px){.rd-map{grid-template-columns:1fr;}.rd-map-actions{flex-direction:row;flex-wrap:wrap;}}
        @media(max-width:640px){.rd-topbar{flex-direction:column;align-items:flex-start;}.rd-user{width:100%;justify-content:space-between;}.rd-top-actions{width:100%;justify-content:space-between;flex-wrap:wrap;}.rd-container{padding:0 16px;}}
    </style>

    <div class="rd-layout">
        <div class="rd-container rd-grid">
            <div class="rd-topbar">
                <div class="rd-brand">
                    <h1>Supermercado Atlantia 🛒</h1>
                    <p>Panel del repartidor para monitorear asignaciones y desempeño en tiempo real.</p>
                </div>

                <div class="rd-user">
                    <div class="rd-avatar">
                        {{ $usuario && $usuario->foto ? '' : \Illuminate\Support\Str::of($usuario->name ?? 'R')->substr(0, 2)->upper() }}
                        @if($usuario && $usuario->foto)
                            <img src="{{ $usuario->foto }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                    <div class="rd-user-info">
                        <strong>{{ $usuario->name ?? 'Repartidor' }}</strong>
                        <span>{{ $usuario->email ?? 'sin correo' }}</span>
                        <div class="{{ $estadoConfig['class'] }}">
                            <i class="fas fa-circle"></i> {{ $estadoConfig['label'] }}
                        </div>
                    </div>
                </div>

                <div class="rd-top-actions">
                    <button class="rd-btn rd-btn-ghost rd-notification" type="button">
                        <i class="fas fa-bell"></i>
                        <span>Notificaciones</span>
                        <span class="badge">{{ max(0, $items->count() - ($stats['listos'] ?? 0)) }}</span>
                    </button>
                    <a href="{{ route('repartidor.panel') }}" class="rd-btn rd-btn-secondary">
                        <i class="fas fa-rotate"></i> Actualizar pedidos
                    </a>
                    <div class="dropdown">
                        <button class="rd-btn" type="button" id="rd-menu-trigger" onclick="document.getElementById('rd-menu').classList.toggle('show');">
                            <i class="fas fa-user"></i> {{ __('Cuenta') }}
                        </button>
                        <div id="rd-menu" class="dropdown-menu">
                            <a href="{{ route('repartidor.perfil') }}"><i class="fas fa-id-card"></i> Perfil</a>
                            <a href="{{ route('repartidor.soporte') }}"><i class="fas fa-life-ring"></i> Soporte</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"><i class="fas fa-sign-out-alt"></i> {{ __('Cerrar sesión') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <section class="rd-section">
                <h2><i class="fas fa-gauge-high"></i> Resumen de operación</h2>
                <p>Visualiza los indicadores clave de tus entregas actuales y el progreso del día.</p>
                <div class="rd-stats">
                    @foreach($metricasRapidas as $metric)
                        <div class="rd-stat-card">
                            <span><i class="fas {{ $metric['icon'] }}"></i> {{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                        </div>
                    @endforeach
                    @foreach($metricasExtra as $metric)
                        <div class="rd-stat-card">
                            <span><i class="fas {{ $metric['icon'] }}"></i> {{ $metric['label'] }}</span>
                            <strong>{{ $metric['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            @if($items->isNotEmpty())
                <section class="rd-section">
                    <h2><i class="fas fa-clipboard-list"></i> Pedidos asignados</h2>
                    <p>Gestiona cada entrega con acciones rápidas para mantener informados a clientes y supervisores.</p>
                    <div class="rd-grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));">
                        @foreach($items as $item)
                            @php
                                $pedido = $item->pedido;
                                $cliente = optional($pedido)->cliente;
                                $direccion = $pedido->direccion_envio ?? [];
                                if (is_string($direccion)) {
                                    $direccion = json_decode($direccion, true) ?: [];
                                }
                                $lat = data_get($direccion, 'lat');
                                $lng = data_get($direccion, 'lng');
                                $mapLink = ($lat && $lng) ? sprintf('https://www.google.com/maps?q=%s,%s', $lat, $lng) : null;
                                $direccionTexto = $pedido?->direccion_formateada ?? (data_get($direccion, 'descripcion') ?? 'Dirección no disponible');
                                $metodoPago = $pedido?->metodo_pago ? ucfirst($pedido->metodo_pago) : 'Efectivo';
                                $estado = $item->fulfillment_status;
                                $chipClass = [
                                    \App\Models\PedidoItem::ESTADO_PREPARANDO => 'chip-preparing',
                                    \App\Models\PedidoItem::ESTADO_ACEPTADO => 'chip-accepted',
                                    \App\Models\PedidoItem::ESTADO_LISTO => 'chip-ready',
                                    \App\Models\PedidoItem::ESTADO_ENTREGADO => 'chip-entregado',
                                    \App\Models\PedidoItem::ESTADO_RECHAZADO => 'chip-rechazado',
                                ][$estado] ?? 'chip-accepted';
                            @endphp
                            <article class="pedido-card">
                                <div class="pedido-header">
                                    <div>
                                        <strong>{{ $pedido?->codigo ?? 'PED-'.$item->pedido_id }}</strong>
                                        <div class="pedido-meta">
                                            <span><i class="fas fa-user"></i> {{ $cliente?->name ?? 'Cliente no disponible' }}</span>
                                            @if($cliente?->telefono)
                                                <span><i class="fas fa-phone"></i> {{ $cliente->telefono }}</span>
                                            @endif
                                            <span><i class="fas fa-clock"></i> {{ optional($item->created_at)->format('d/m H:i') }}</span>
                                        </div>
                                    </div>
                                    <span class="rd-chip {{ $chipClass }}">
                                        <i class="fas fa-circle"></i> {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                    </span>
                                </div>

                                <div class="pedido-body">
                                    <span>
                                        <small>Dirección</small>
                                        {{ $direccionTexto }}
                                    </span>
                                    <span>
                                        <small>Método de pago</small>
                                        {{ $metodoPago }}
                                    </span>
                                    <span>
                                        <small>Total pedido</small>
                                        Q{{ number_format((float) ($pedido?->total ?? 0), 2) }}
                                    </span>
                                    <span>
                                        <small>Tarifa entrega</small>
                                        Q{{ number_format((float) $item->delivery_fee, 2) }}
                                    </span>
                                </div>

                                <div class="pedido-actions">
                                    <button type="button" class="rd-btn rd-btn-ghost" data-detalle="{{ $item->id }}">
                                        <i class="fas fa-search"></i> Ver detalle
                                    </button>
                                    @if($mapLink)
                                        <a class="rd-btn rd-btn-ghost" href="{{ $mapLink }}" target="_blank" rel="noopener">
                                            <i class="fas fa-map-location-dot"></i> Ver ruta
                                        </a>
                                    @endif
                                    @if($item->fulfillment_status === \App\Models\PedidoItem::ESTADO_LISTO)
                                        <form method="POST" action="{{ route('repartidor.items.entregar', $item->id) }}">
                                            @csrf
                                            <button class="rd-btn rd-btn-secondary" type="submit">
                                                <i class="fas fa-check"></i> Confirmar entrega
                                            </button>
                                        </form>
                                    @else
                                        <span class="rd-btn" style="background:#d1d5db;color:#374151;cursor:not-allowed;">
                                            <i class="fas fa-hourglass-half"></i> Esperando preparación
                                        </span>
                                    @endif
                                    <button type="button" class="rd-btn rd-btn-ghost" data-rechazar="{{ $item->id }}">
                                        <i class="fas fa-ban"></i> Rechazar pedido
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="rd-map">
                    <div>
                        <h3><i class="fas fa-map"></i> Seguimiento de rutas</h3>
                        <p>Visualiza tu ubicación actual y las direcciones activas. Integra tu proveedor de mapas favorito (Google Maps, Leaflet) conectándolo con la API `repartidor/api/datos`.</p>
                        <div class="rd-map-actions">
                            <a href="{{ route('repartidor.pedidos.asignados') }}" class="rd-btn rd-btn-ghost"><i class="fas fa-layer-group"></i> Ver lista completa</a>
                            <button type="button" class="rd-btn rd-btn-ghost"><i class="fas fa-location-crosshairs"></i> Centrar en mi posición</button>
                            <button type="button" class="rd-btn rd-btn-ghost"><i class="fas fa-map-marked-alt"></i> Exportar ruta</button>
                        </div>
                    </div>
                    <div class="rd-map-preview">
                        Panel listo para incrustar mapa interactivo con tus puntos de entrega activos.
                    </div>
                </section>
            @else
                <section class="rd-section">
                    <h2><i class="fas fa-compass"></i> Sin pedidos asignados</h2>
                    <div class="rd-empty">
                        <h3>🚫 No tienes pedidos asignados actualmente.</h3>
                        <p>Permanece atento a nuevas asignaciones o refresca el panel para obtener actualizaciones en tiempo real.</p>
                    </div>
                    <div class="rd-options">
                        <div class="rd-option-card">
                            <strong>Actualizar pedidos</strong>
                            <p>Comprueba si existen nuevas órdenes pendientes en segundos.</p>
                            <a href="{{ route('repartidor.panel') }}"><i class="fas fa-rotate"></i> Actualizar ahora</a>
                        </div>
                        <div class="rd-option-card">
                            <strong>Historial de entregas</strong>
                            <p>Consulta tus entregas previas y verifica pagos o incidencias registradas.</p>
                            <a href="{{ route('repartidor.pedidos.entregados') }}"><i class="fas fa-clock-rotate-left"></i> Ver historial</a>
                        </div>
                        <div class="rd-option-card">
                            <strong>Resumen de desempeño</strong>
                            <p>Analiza tus métricas semanales y mensualizadas para optimizar tiempos.</p>
                            <a href="{{ route('repartidor.perfil') }}"><i class="fas fa-chart-line"></i> Revisar estadísticas</a>
                        </div>
                        <div class="rd-option-card">
                            <strong>Estado de disponibilidad</strong>
                            <p>Cambia entre Disponible u Fuera de servicio cuando lo necesites.</p>
                            <a href="#" onclick="alert('Configura aquí la lógica para actualizar tu estado.'); return false;"><i class="fas fa-toggle-on"></i> Gestionar estado</a>
                        </div>
                    </div>
                </section>
            @endif

            <section class="rd-section">
                <h2><i class="fas fa-clock"></i> Historial reciente</h2>
                <p>Últimos movimientos registrados para que lleves control de tus entregas.</p>
                @if($historial->isEmpty())
                    <div class="rd-history-empty">
                        Aún no registras entregas completadas. Cuando confirmes entregas aparecerán aquí.
                    </div>
                @else
                    <div class="rd-history">
                        @foreach($historial as $item)
                            @php
                                $pedido = $item->pedido;
                                $cliente = optional($pedido)->cliente;
                            @endphp
                            <div class="rd-history-item">
                                <div>
                                    <strong>{{ $pedido?->codigo ?? 'PED-'.$item->pedido_id }}</strong>
                                    <span> · {{ $cliente?->name ?? 'Cliente' }}</span>
                                </div>
                                <span>{{ optional($item->updated_at)->format('d/m H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="rd-section">
                <h2><i class="fas fa-toolbox"></i> Recursos y herramientas</h2>
                <p>Accesos rápidos para dar seguimiento a pagos, incidencias y soporte.</p>
                <div class="rd-sections-grid">
                    <div class="rd-section-card">
                        <h4><i class="fas fa-receipt"></i> Historial de pedidos</h4>
                        <p>Listado completo de entregas con detalle, método de pago y evaluaciones.</p>
                        <a href="{{ route('repartidor.pedidos.entregados') }}">Abrir historial</a>
                    </div>
                    <div class="rd-section-card">
                        <h4><i class="fas fa-comments"></i> Mensajería interna</h4>
                        <p>Canal rápido para comunicarte con administradores o vendedores.</p>
                        <a href="#" onclick="alert('Integra aquí tu módulo de mensajería.'); return false;">Iniciar chat</a>
                    </div>
                    <div class="rd-section-card">
                        <h4><i class="fas fa-calendar-day"></i> Rutas programadas</h4>
                        <p>Planifica entregas futuras y coordina horarios con el centro de distribución.</p>
                        <a href="#" onclick="alert('Integra aquí la planificación de rutas.'); return false;">Ver agenda</a>
                    </div>
                    <div class="rd-section-card">
                        <h4><i class="fas fa-coins"></i> Comisiones y pagos</h4>
                        <p>Consulta montos acumulados por comisiones y fechas de depósito.</p>
                        <a href="#" onclick="alert('Conecta este enlace con tu módulo de finanzas.'); return false;">Ver comisiones</a>
                    </div>
                    <div class="rd-section-card">
                        <h4><i class="fas fa-life-ring"></i> Soporte y ayuda</h4>
                        <p>Accede a políticas de entrega, manuales y contacto directo con soporte.</p>
                        <a href="{{ route('repartidor.soporte') }}">Ir a soporte</a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <style>
        .dropdown{position:relative;}
        .dropdown-menu{position:absolute;top:calc(100% + 8px);right:0;background:#fff;border-radius:14px;border:1px solid #e5e7eb;box-shadow:0 14px 30px rgba(15,23,42,0.18);display:none;flex-direction:column;min-width:200px;overflow:hidden;z-index:20;}
        .dropdown-menu.show{display:flex;}
        .dropdown-menu a,.dropdown-menu button{padding:12px 16px;font-size:14px;color:#1f2937;text-decoration:none;display:flex;align-items:center;gap:10px;border:none;background:transparent;width:100%;text-align:left;cursor:pointer;}
        .dropdown-menu a:hover,.dropdown-menu button:hover{background:#f3f4f6;}
    </style>
    <script>
        document.addEventListener('click', function (event) {
            const trigger = document.getElementById('rd-menu-trigger');
            const menu = document.getElementById('rd-menu');
            if (!trigger || !menu) {
                return;
            }

            const clickedInside = trigger.contains(event.target) || menu.contains(event.target);
            if (!clickedInside) {
                menu.classList.remove('show');
            }
        });
    </script>
@endsection
