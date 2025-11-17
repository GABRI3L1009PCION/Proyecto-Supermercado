@extends('layouts.app')

@section('content')
    <style>
        /* 🎨 Paleta de colores vino tinto Atlantia */
        :root {
            --vino: #6B0F1A;
            --vino-oscuro: #4A0B12;
            --dorado: #D4AF37;
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
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .vd-wrap {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        /* ====== ENCABEZADO ====== */
        .vd-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .vd-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--vino);
            text-shadow: 1px 1px 0 var(--dorado);
            margin: 0;
        }

        .vd-title i {
            margin-right: 0.5rem;
        }

        /* ====== BOTONES ====== */
        .vd-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .vd-btn {
            padding: 0.65rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            color: var(--blanco);
            transition: all 0.25s ease;
            cursor: pointer;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            font-size: 0.9rem;
        }

        .vd-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.15);
        }

        .vd-btn--vino { background-color: var(--vino); }
        .vd-btn--dorado { background-color: var(--dorado); color: var(--vino-oscuro); font-weight: 600; }
        .vd-btn--gris { background-color: #555; }
        .vd-btn--rojo { background-color: #C0392B; }
        .vd-btn--verde { background-color: #1E8449; }

        /* ====== TARJETAS KPI ====== */
        .vd-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 2.5rem;
        }

        .vd-card {
            background: var(--vino);
            color: var(--blanco);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .vd-card:hover {
            transform: translateY(-5px);
        }

        .vd-card::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transform: skewX(-20deg);
        }

        .vd-card__label {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--dorado);
            font-weight: 600;
        }

        .vd-card__value {
            font-size: 2.3rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        /* ====== TABLA ====== */
        .vd-box {
            background: var(--blanco);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 25px;
            overflow-x: auto;
        }

        .vd-box__head {
            border-bottom: 2px solid var(--vino);
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--vino-oscuro);
        }

        .vd-box__head h3 {
            margin: 0;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table.vd-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .vd-table th, .vd-table td {
            padding: 14px 10px;
            text-align: center;
        }

        .vd-table th {
            background: var(--vino);
            color: var(--blanco);
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .vd-table tr:nth-child(even) {
            background: #fafafa;
        }

        .vd-table td {
            color: var(--gris-texto);
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }

        /* ====== BADGES ====== */
        .vd-badge {
            padding: 0.4em 0.8em;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            display: inline-block;
        }

        .vd-badge--info { background-color: #5DADE2; }
        .vd-badge--warning { background-color: #F5B041; color: #222; }
        .vd-badge--primary { background-color: var(--vino); }
        .vd-badge--success { background-color: #27AE60; }
        .vd-badge--danger { background-color: #E74C3C; }
        .vd-badge--muted { background-color: #95A5A6; }

        /* ====== ACCIONES TABLA ====== */
        .vd-btn--small {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            background-color: var(--vino);
            color: var(--blanco);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
        }

        .vd-btn--small:hover {
            background-color: var(--vino-oscuro);
            transform: scale(1.05);
        }

        .vd-table__empty {
            text-align: center;
            padding: 30px 20px;
            color: #888;
            font-style: italic;
        }

        /* ====== RESPONSIVE TABLETS (768px - 1024px) ====== */
        @media (max-width: 1024px) {
            .vd-wrap {
                padding: 15px;
                margin: 15px auto;
            }

            .vd-title {
                font-size: 1.6rem;
            }

            .vd-kpis {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 15px;
            }

            .vd-card {
                padding: 20px;
            }

            .vd-card__value {
                font-size: 2rem;
            }

            .vd-box {
                padding: 20px;
            }
        }

        /* ====== RESPONSIVE MÓVILES (max-width: 767px) ====== */
        @media (max-width: 767px) {
            .vd-wrap {
                padding: 12px;
                margin: 10px auto;
            }

            /* Encabezado apilado */
            .vd-topbar {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .vd-title {
                font-size: 1.5rem;
                text-align: center;
            }

            /* Botones en columna completa */
            .vd-actions {
                flex-direction: column;
                width: 100%;
                gap: 8px;
            }

            .vd-btn {
                width: 100%;
                justify-content: center;
                padding: 0.75rem 1rem;
                font-size: 0.95rem;
            }

            /* KPIs apilados */
            .vd-kpis {
                grid-template-columns: 1fr;
                gap: 12px;
                margin-bottom: 1.5rem;
            }

            .vd-card {
                padding: 20px 15px;
            }

            .vd-card__label {
                font-size: 0.9rem;
            }

            .vd-card__value {
                font-size: 2rem;
            }

            /* Tabla responsive con scroll */
            .vd-box {
                padding: 15px;
                border-radius: 10px;
            }

            .vd-box__head h3 {
                font-size: 1.1rem;
            }

            .table-responsive {
                margin: 0 -15px;
                padding: 0 15px;
            }

            .vd-table th, .vd-table td {
                padding: 10px 8px;
                font-size: 0.8rem;
            }

            .vd-badge {
                font-size: 0.75rem;
                padding: 0.3em 0.6em;
            }

            .vd-btn--small {
                font-size: 0.8rem;
                padding: 0.35rem 0.6rem;
            }

            .vd-table__empty {
                padding: 20px 10px;
                font-size: 0.9rem;
            }
        }

        /* ====== RESPONSIVE MÓVILES PEQUEÑOS (max-width: 480px) ====== */
        @media (max-width: 480px) {
            .vd-title {
                font-size: 1.3rem;
            }

            .vd-card__value {
                font-size: 1.8rem;
            }

            .vd-card__label {
                font-size: 0.85rem;
            }

            .vd-btn {
                font-size: 0.9rem;
                padding: 0.7rem 0.9rem;
            }

            .vd-table {
                min-width: 650px;
            }

            .vd-table th, .vd-table td {
                padding: 8px 6px;
                font-size: 0.75rem;
            }

            .vd-btn--small {
                font-size: 0.75rem;
                padding: 0.3rem 0.5rem;
            }

            .vd-btn--small i {
                font-size: 0.7rem;
            }
        }

        /* ====== LANDSCAPE MÓVILES ====== */
        @media (max-width: 767px) and (orientation: landscape) {
            .vd-kpis {
                grid-template-columns: repeat(3, 1fr);
            }

            .vd-card {
                padding: 15px;
            }

            .vd-card__value {
                font-size: 1.6rem;
            }
        }

        /* ====== PANTALLAS MUY GRANDES (1400px+) ====== */
        @media (min-width: 1400px) {
            .vd-wrap {
                max-width: 1400px;
            }

            .vd-title {
                font-size: 2.3rem;
            }

            .vd-kpis {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Mejoras de accesibilidad */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Scroll suave en tabla */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--vino);
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--vino-oscuro);
        }
    </style>

    <div class="vd-wrap">
        {{-- Encabezado --}}
        <div class="vd-topbar">
            <h1 class="vd-title"><i class="fas fa-store"></i> Panel del vendedor</h1>
            <div class="vd-actions">
                <a href="{{ route('vendedor.productos.create') }}" class="vd-btn vd-btn--verde">
                    <i class="fas fa-plus"></i> Nuevo producto
                </a>
                <a href="{{ route('vendedor.productos.index') }}" class="vd-btn vd-btn--gris">
                    <i class="fas fa-box"></i> Mis productos
                </a>

                {{-- 🔹 NUEVO: módulo Zonas de reparto --}}
                <a href="{{ route('vendedor.zonas.index') }}" class="vd-btn vd-btn--vino">
                    <i class="fas fa-map-marked-alt"></i> Zonas de reparto
                </a>

                <a href="{{ route('vendedor.reseñas.index') }}" class="vd-btn vd-btn--dorado">
                    <i class="fas fa-star"></i> Reseñas
                </a>
                <a href="{{ route('vendedor.perfil') }}" class="vd-btn vd-btn--vino">
                    <i class="fas fa-user"></i> Perfil
                </a>
                <a href="{{ route('logout') }}" class="vd-btn vd-btn--rojo"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
            </div>
        </div>

        {{-- KPIs --}}
        <section class="vd-kpis">
            <article class="vd-card">
                <div class="vd-card__label">Mis productos</div>
                <div class="vd-card__value">{{ $kpis['mis_productos'] ?? 0 }}</div>
            </article>

            <article class="vd-card">
                <div class="vd-card__label">Ítems pendientes</div>
                <div class="vd-card__value">{{ $kpis['pendientes'] ?? 0 }}</div>
            </article>

            <article class="vd-card">
                <div class="vd-card__label">Ventas del mes (Q)</div>
                <div class="vd-card__value">Q{{ number_format($kpis['vendidos_mes'] ?? 0, 2) }}</div>
            </article>
        </section>

        {{-- Órdenes recientes --}}
        <section class="vd-box">
            <div class="vd-box__head">
                <h3><i class="fas fa-shopping-bag"></i> Órdenes recientes</h3>
            </div>
            <div class="table-responsive">
                <table class="vd-table">
                    <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Cant</th>
                        <th>Estado ítem</th>
                        <th>Total ítem</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $it)
                        @php
                            $estado = $it->fulfillment_status;
                            $badge  = [
                                'accepted'  => 'vd-badge--info',
                                'preparing' => 'vd-badge--warning',
                                'ready'     => 'vd-badge--primary',
                                'delivered' => 'vd-badge--success',
                                'rejected'  => 'vd-badge--danger',
                            ][$estado] ?? 'vd-badge--muted';
                        @endphp
                        <tr>
                            <td><strong>#{{ $it->pedido_id }}</strong></td>
                            <td>{{ optional($it->pedido->cliente)->name ?? '—' }}</td>
                            <td>{{ optional($it->producto)->nombre ?? '—' }}</td>
                            <td>{{ $it->cantidad }}</td>
                            <td><span class="vd-badge {{ $badge }}">{{ ucfirst($estado) }}</span></td>
                            <td><strong>Q{{ number_format($it->cantidad * $it->precio_unitario, 2) }}</strong></td>
                            <td>
                                <a href="{{ route('vendedor.pedidos.show', ['pedido' => $it->pedido_id]) }}" class="vd-btn--small">
                                    <i class="fas fa-eye"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="vd-table__empty">
                                <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                Sin órdenes aún.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
