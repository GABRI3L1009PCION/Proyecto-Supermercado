@extends('layouts.app')

@section('content')
    <style>
        .vd-wrap {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .vd-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .vd-title {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .vd-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .vd-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            color: #fff;
            background-color: #6c757d;
            transition: all 0.2s ease-in-out;
            border: none;
            cursor: pointer;
        }

        .vd-btn i {
            font-size: 0.9rem;
        }

        .vd-btn:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        .vd-btn--primary {
            background-color: #198754;
        }

        .vd-btn--danger {
            background-color: #dc3545;
        }

        .vd-btn--info {
            background-color: #0dcaf0;
            color: #000;
        }

        .vd-btn--warning {
            background-color: #ffc107;
            color: #000;
        }

        .vd-btn--small {
            padding: 0.35rem 0.75rem;
            font-size: 0.9rem;
        }

        .vd-kpis {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .vd-card {
            flex: 1 1 250px;
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .vd-card:nth-child(1) { background-color: #0d6efd; }
        .vd-card:nth-child(2) { background-color: #ffc107; color: #000; }
        .vd-card:nth-child(3) { background-color: #198754; }

        .vd-card__label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .vd-card__value {
            font-size: 2rem;
            font-weight: bold;
        }

        .vd-box {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .vd-box__head {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .vd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .vd-table th,
        .vd-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .vd-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .vd-badge {
            padding: 0.4em 0.8em;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            color: white;
        }

        .vd-badge--info { background-color: #0dcaf0; }
        .vd-badge--warning { background-color: #ffc107; color: black; }
        .vd-badge--primary { background-color: #0d6efd; }
        .vd-badge--success { background-color: #198754; }
        .vd-badge--danger { background-color: #dc3545; }
        .vd-badge--muted { background-color: #6c757d; }

        .vd-table__empty {
            text-align: center;
            color: #999;
            padding: 20px;
        }
    </style>

    <div class="vd-wrap">
        {{-- Encabezado --}}
        <div class="vd-topbar">
            <h1 class="vd-title">Panel del vendedor</h1>
            <div class="vd-actions">
                {{-- Botones de acción --}}
                <a href="{{ route('vendedor.reseñas.index') }}" class="vd-btn vd-btn--info">
                    <i class="fas fa-star"></i> Ver reseñas
                </a>
                <a href="{{ route('vendedor.perfil') }}" class="vd-btn vd-btn--warning">
                    <i class="fas fa-user"></i> Ver perfil
                </a>
                <a href="{{ route('logout') }}"
                   class="vd-btn vd-btn--danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <a href="{{ route('vendedor.productos.create') }}" class="vd-btn vd-btn--primary">
                    <i class="fas fa-plus"></i> Nuevo producto
                </a>
                <a href="{{ route('vendedor.productos.index') }}" class="vd-btn">
                    <i class="fas fa-box"></i> Ver mis productos
                </a>
                <a href="{{ route('vendedor.zonas.index') }}" class="vd-btn vd-btn--info">
                    <i class="fas fa-map-marked-alt"></i> Mis zonas de reparto
                </a>
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
                <div class="vd-card__value">Q{{ number_format($kpis['vendidos_mes'] ?? 0,2) }}</div>
            </article>
        </section>

        {{-- Órdenes recientes --}}
        <section class="vd-box">
            <div class="vd-box__head">
                <h3>Órdenes recientes</h3>
            </div>

            <div class="vd-table-wrap">
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
                            <td>#{{ $it->pedido_id }}</td>
                            <td>{{ optional($it->pedido->cliente)->name ?? '—' }}</td>
                            <td>{{ optional($it->producto)->nombre ?? '—' }}</td>
                            <td>{{ $it->cantidad }}</td>
                            <td><span class="vd-badge {{ $badge }}">{{ ucfirst($estado) }}</span></td>
                            <td>Q{{ number_format($it->cantidad * $it->precio_unitario, 2) }}</td>
                            <td>
                                <a href="{{ route('vendedor.pedidos.show', ['pedido' => $it->pedido_id]) }}" class="vd-btn vd-btn--small">
                                    <i class="fas fa-eye"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="vd-table__empty">Sin órdenes aún.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
