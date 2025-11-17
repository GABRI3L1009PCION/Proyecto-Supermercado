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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --vino: #6B0F1A;
            --vino-oscuro: #4A0B12;
            --vino-claro: #8B1F2A;
            --dorado: #D4AF37;
            --dorado-oscuro: #B8941F;
            --rosado: #E8C8C8;
            --gris-rosado: #F1EAEA;
            --gris-texto: #555;
            --blanco: #fff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(160deg, var(--gris-rosado) 0%, var(--rosado) 45%, #ffffff 100%);
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .rd-layout {
            padding: 24px 0;
            min-height: 100vh;
        }

        .rd-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ====== ALERTAS ====== */
        .alert {
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%);
            color: #a16207;
            border: 1px solid #fde047;
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* ====== TOPBAR ====== */
        .rd-topbar {
            background: linear-gradient(135deg, var(--vino) 0%, var(--vino-oscuro) 100%);
            color: var(--blanco);
            border-radius: 20px;
            padding: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 20px 50px rgba(74, 11, 18, 0.4);
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .rd-topbar::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(212, 175, 55, 0.2), transparent 60%);
            pointer-events: none;
        }

        .rd-topbar > * {
            position: relative;
            z-index: 1;
        }

        .rd-brand h1 {
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            color: var(--blanco);
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .rd-brand p {
            margin: 6px 0 0;
            font-size: 14px;
            color: var(--dorado);
            font-weight: 500;
        }

        .rd-user {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .rd-avatar {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            overflow: hidden;
            border: 3px solid var(--dorado);
            background: linear-gradient(135deg, var(--vino-claro), var(--vino-oscuro));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blanco);
            font-weight: 700;
            font-size: 20px;
            text-transform: uppercase;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .rd-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .rd-user-info strong {
            display: block;
            font-size: 17px;
            font-weight: 700;
            color: var(--blanco);
        }

        .rd-user-info span {
            font-size: 13px;
            color: var(--rosado);
        }

        .estado-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 6px;
        }

        .estado-disponible {
            background: rgba(34, 197, 94, 0.2);
            color: #bbf7d0;
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .estado-ocupado {
            background: rgba(250, 204, 21, 0.2);
            color: #fef08a;
            border: 1px solid rgba(250, 204, 21, 0.4);
        }

        .estado-offline {
            background: rgba(148, 163, 184, 0.2);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, 0.4);
        }

        /* ====== BOTONES ====== */
        .rd-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--dorado);
            color: var(--vino-oscuro);
            padding: 11px 18px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .rd-btn:hover {
            background: var(--dorado-oscuro);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(212, 175, 55, 0.4);
        }

        .rd-btn-ghost {
            background: rgba(255, 255, 255, 0.15);
            color: var(--blanco);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .rd-btn-ghost:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .rd-topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .rd-notification {
            position: relative;
        }

        .rd-notification .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: var(--blanco);
            border-radius: 999px;
            font-size: 11px;
            padding: 3px 7px;
            font-weight: 700;
            border: 2px solid var(--vino);
        }

        /* ====== ESTADÍSTICAS ====== */
        .rd-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .rd-stat-card {
            background: var(--blanco);
            border-radius: 18px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 25px rgba(107, 15, 26, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .rd-stat-card::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(212, 175, 55, 0.05));
            transform: skewX(-15deg);
        }

        .rd-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(107, 15, 26, 0.15);
        }

        .rd-stat-card span {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--gris-texto);
            text-transform: uppercase;
            font-weight: 600;
            position: relative;
        }

        .rd-stat-card span i {
            color: var(--vino);
            font-size: 18px;
        }

        .rd-stat-card strong {
            font-size: 32px;
            color: var(--vino-oscuro);
            display: block;
            margin-top: 8px;
            font-weight: 800;
            position: relative;
        }

        /* ====== SECCIONES ====== */
        .rd-section {
            background: var(--blanco);
            border-radius: 22px;
            padding: 28px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(107, 15, 26, 0.08);
            margin-bottom: 24px;
        }

        .rd-section h2 {
            font-size: 22px;
            font-weight: 700;
            color: var(--vino);
            margin: 0 0 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rd-section h2 i {
            color: var(--dorado);
        }

        /* ====== ESTADO VACÍO ====== */
        .rd-empty {
            border: 2px dashed var(--rosado);
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            background: linear-gradient(135deg, #fef8f2 0%, #f9f5f5 100%);
        }

        .rd-empty h3 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 10px;
            color: var(--vino);
        }

        .rd-empty p {
            margin: 0 0 24px;
            color: var(--gris-texto);
            font-size: 15px;
        }

        .rd-empty-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ====== TARJETAS DE PEDIDOS ====== */
        .rd-card {
            background: linear-gradient(135deg, #fef8f2 0%, var(--blanco) 100%);
            border-radius: 18px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 8px 25px rgba(107, 15, 26, 0.08);
            margin-bottom: 18px;
            transition: all 0.3s ease;
        }

        .rd-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(107, 15, 26, 0.15);
        }

        .rd-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .rd-card h3 {
            margin: 0 0 10px;
            font-size: 20px;
            color: var(--vino);
            font-weight: 700;
        }

        .rd-card p {
            margin: 6px 0;
            color: var(--gris-texto);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rd-card p i {
            color: var(--dorado);
            width: 16px;
        }

        .rd-card strong {
            color: var(--vino-oscuro);
        }

        .rd-card ul {
            margin: 10px 0 0 24px;
            padding: 0;
            color: var(--gris-texto);
            font-size: 14px;
        }

        .rd-card ul li {
            margin-bottom: 4px;
        }

        /* ====== CHIPS DE ESTADO ====== */
        .rd-chip {
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0.05em;
        }

        .chip-asignado {
            background: #ede9fe;
            color: #5b21b6;
            border: 1px solid #c4b5fd;
        }

        .chip-aceptado {
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #93c5fd;
        }

        .chip-en_camino {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .chip-incidencia {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* ====== ACCIONES DE TARJETA ====== */
        .rd-card-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .rd-card-actions form {
            display: inline-flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .rd-card-actions input[type="file"] {
            background: var(--blanco);
            border: 2px dashed var(--dorado);
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            color: var(--vino);
            font-weight: 500;
            cursor: pointer;
        }

        .rd-card-actions input[type="file"]:hover {
            border-color: var(--dorado-oscuro);
            background: #fef8f2;
        }

        /* ====== HISTORIAL ====== */
        .rd-history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: linear-gradient(135deg, #fef8f2 0%, var(--blanco) 100%);
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .rd-history-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(107, 15, 26, 0.1);
        }

        .rd-history-item strong {
            color: var(--vino);
            font-size: 15px;
            display: block;
            margin-bottom: 4px;
        }

        .rd-history-item span {
            color: var(--gris-texto);
            font-size: 13px;
        }

        /* ====== RESPONSIVE TABLETS ====== */
        @media (max-width: 1024px) {
            .rd-container {
                padding: 0 20px;
            }

            .rd-topbar {
                padding: 24px;
            }

            .rd-stats {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        /* ====== RESPONSIVE MÓVILES ====== */
        @media (max-width: 768px) {
            .rd-layout {
                padding: 16px 0;
            }

            .rd-container {
                padding: 0 16px;
            }

            .rd-topbar {
                flex-direction: column;
                align-items: stretch;
                padding: 20px;
                gap: 16px;
            }

            .rd-brand h1 {
                font-size: 22px;
            }

            .rd-user {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .rd-topbar-actions {
                flex-direction: column;
                width: 100%;
            }

            .rd-topbar-actions .rd-btn {
                width: 100%;
                justify-content: center;
            }

            .rd-stats {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .rd-stat-card {
                padding: 20px;
            }

            .rd-section {
                padding: 20px;
            }

            .rd-section h2 {
                font-size: 19px;
            }

            .rd-card {
                padding: 18px;
            }

            .rd-card-header {
                flex-direction: column;
            }

            .rd-card-actions {
                flex-direction: column;
            }

            .rd-card-actions .rd-btn,
            .rd-card-actions form {
                width: 100%;
            }

            .rd-card-actions .rd-btn {
                justify-content: center;
            }

            .rd-card-actions input[type="file"] {
                width: 100%;
            }

            .rd-history-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .rd-empty {
                padding: 30px 20px;
            }

            .rd-empty-actions {
                grid-template-columns: 1fr;
            }
        }

        /* ====== MÓVILES PEQUEÑOS ====== */
        @media (max-width: 480px) {
            .rd-brand h1 {
                font-size: 20px;
            }

            .rd-avatar {
                width: 48px;
                height: 48px;
            }

            .rd-stat-card strong {
                font-size: 28px;
            }

            .rd-card h3 {
                font-size: 18px;
            }

            .rd-btn {
                padding: 10px 14px;
                font-size: 13px;
            }
        }
    </style>

    <div class="rd-layout">
        <div class="rd-container">
            {{-- Alertas --}}
            @foreach (['success' => 'alert-success', 'warning' => 'alert-warning', 'error' => 'alert-error'] as $flashKey => $flashClass)
                @if(session($flashKey))
                    <div class="alert {{ $flashClass }}">
                        <i class="fas fa-{{ $flashKey === 'success' ? 'check-circle' : ($flashKey === 'warning' ? 'exclamation-triangle' : 'times-circle') }}"></i>
                        {{ session($flashKey) }}
                    </div>
                @endif
            @endforeach

            {{-- Topbar --}}
            <div class="rd-topbar">
                <div class="rd-brand">
                    <h1><i class="fas fa-store"></i> Supermercado Atlantia</h1>
                    <p>Panel del repartidor</p>
                </div>
                <div class="rd-user">
                    <div class="rd-avatar">
                        @if($usuario && $usuario->foto)
                            <img src="{{ $usuario->foto }}" alt="Avatar">
                        @else
                            {{ \Illuminate\Support\Str::of($usuario->name ?? 'R')->substr(0,2)->upper() }}
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
                <div class="rd-topbar-actions">
                    <a href="{{ route('repartidor.pedidos.asignados') }}" class="rd-btn rd-btn-ghost rd-notification">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Asignados</span>
                        @if($pedidos->count() > 0)
                            <span class="badge">{{ $pedidos->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('repartidor.panel') }}" class="rd-btn">
                        <i class="fas fa-rotate"></i> Refrescar
                    </a>
                </div>
            </div>

            {{-- Resumen de operación --}}
            <section class="rd-section">
                <h2><i class="fas fa-gauge-high"></i> Resumen de operación</h2>
                <div class="rd-stats">
                    @foreach(array_merge($metricasRapidas, $metricasExtra) as $m)
                        <div class="rd-stat-card">
                            <span><i class="fas {{ $m['icon'] }}"></i> {{ $m['label'] }}</span>
                            <strong>{{ $m['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Pedidos asignados --}}
            @if($pedidos->isNotEmpty())
                <section class="rd-section">
                    <h2><i class="fas fa-truck"></i> Pedidos asignados ({{ $pedidos->count() }})</h2>
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
                            <div class="rd-card-header">
                                <div style="flex: 1;">
                                    <h3>{{ $pedido->codigo ?? ('PED-' . $pedido->id) }}</h3>
                                    <p><i class="fas fa-user"></i> {{ optional($pedido->cliente)->name ?? 'Cliente no disponible' }}</p>
                                    <p><i class="fas fa-map-marker-alt"></i> {{ $pedido->direccion_formateada }}</p>
                                    <p><i class="fas fa-clock"></i> {{ optional($pedido->created_at)->format('d/m/Y H:i') }}</p>
                                    <p><i class="fas fa-wallet"></i> <strong>{{ ucfirst($pedido->metodo_pago ?? 'efectivo') }}</strong></p>
                                </div>
                                <span class="rd-chip {{ $chipClass }}">
                                    <i class="fas fa-circle"></i> {{ ucfirst(str_replace('_',' ', $estado)) }}
                                </span>
                            </div>

                            @if($pedido->productos->isNotEmpty())
                                <div style="margin-top:12px;">
                                    <strong style="color: var(--vino);">Productos:</strong>
                                    <ul>
                                        @foreach($pedido->productos as $producto)
                                            <li>{{ $producto->nombre }} <strong>(x{{ $producto->pivot->cantidad ?? 0 }})</strong></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="rd-card-actions">
                                <a href="{{ route('repartidor.pedidos.rastreo', $pedido) }}" class="rd-btn rd-btn-ghost">
                                    <i class="fas fa-search"></i> Detalle
                                </a>
                                @if($mapLink)
                                    <a href="{{ $mapLink }}" target="_blank" class="rd-btn rd-btn-ghost">
                                        <i class="fas fa-map"></i> Ver ruta
                                    </a>
                                @endif

                                @if($estado === 'asignado')
                                    <form action="{{ route('repartidor.pedidos.aceptar', $pedido) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rd-btn">
                                            <i class="fas fa-handshake"></i> Aceptar pedido
                                        </button>
                                    </form>
                                @endif

                                @if($estado === 'aceptado')
                                    <form action="{{ route('repartidor.pedidos.iniciar', $pedido) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rd-btn rd-btn-ghost">
                                            <i class="fas fa-route"></i> Iniciar ruta
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($estado, ['aceptado', 'en_camino']))
                                    <form action="{{ route('repartidor.pedidos.entregado', $pedido) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="evidencia_firma" accept="image/*" title="Sube evidencia opcional">
                                        <button type="submit" class="rd-btn">
                                            <i class="fas fa-check"></i> Confirmar entrega
                                        </button>
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
                        <p>Permanece atento o actualiza el panel para ver nuevas asignaciones.</p>
                        <div class="rd-empty-actions">
                            <a href="{{ route('repartidor.panel') }}" class="rd-btn">
                                <i class="fas fa-rotate"></i> Actualizar
                            </a>
                            <a href="{{ route('repartidor.pedidos.entregados') }}" class="rd-btn rd-btn-ghost">
                                <i class="fas fa-clock"></i> Ver historial
                            </a>
                            <a href="{{ route('repartidor.perfil') }}" class="rd-btn rd-btn-ghost">
                                <i class="fas fa-chart-line"></i> Desempeño
                            </a>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Historial de pedidos --}}
            <section class="rd-section">
                <h2><i class="fas fa-clock-rotate-left"></i> Historial de pedidos</h2>
                @if($historial->isEmpty())
                    <div class="rd-empty">
                        <p style="margin: 0;">Aún no tienes entregas completadas.</p>
                    </div>
                @else
                    @foreach($historial as $h)
                        <div class="rd-history-item">
                            <div>
                                <strong>{{ $h->codigo ?? ('PED-' . $h->id) }}</strong>
                                <span>{{ optional($h->cliente)->name }}</span>
                            </div>
                            <span style="color: var(--dorado); font-weight: 600;">
                                <i class="fas fa-calendar-check"></i>
                                {{ optional($h->fecha_entregado ?? $h->updated_at)->format('d/m H:i') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </section>
        </div>
    </div>

    <script>
        function reportarIncidencia(id) {
            const motivo = prompt('Describe brevemente la incidencia:');
            if (!motivo || motivo.trim() === '') {
                return;
            }
            const form = document.getElementById(`incidencia-form-${id}`);
            if (!form) {
                alert('Error: No se pudo encontrar el formulario.');
                return;
            }
            form.querySelector('input[name="motivo"]').value = motivo;
            form.submit();
        }

        // Auto-ocultar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
@endsection
