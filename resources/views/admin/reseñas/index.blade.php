@extends('layouts.app')

@section('content')
    <div class="dashboard-container" style="display:flex;">
        <style>
            .dashboard-container{
                max-width:100%;
                box-sizing:border-box;
                font-family:Arial,sans-serif;
                background-color:#f5f6f8;
                color:#333;
                min-height:100vh;
                position:relative;
            }
            .sidebar{
                width:250px;
                background-color:#E6F4EA;
                border-right:1px solid #ddd;
                display:flex;
                flex-direction:column;
                justify-content:space-between;
                height:100vh;
                position:sticky;
                top:0;
                overflow-y:auto;
                transition:transform .25s ease;
                z-index:1001;
            }
            .sidebar-header{display:flex;align-items:center;padding:20px;border-bottom:1px solid #eee;flex-direction:column;text-align:center}
            .sidebar-header img{width:90px;height:70px;margin-bottom:10px}
            .sidebar-header .brand{font-weight:bold;color:#1e3a8a;font-size:18px}
            .sidebar-header .subtitle{font-size:12px;color:#999}
            .sidebar-header .admin-name{font-size:13px;color:#333;margin-top:5px}
            .menu{flex-grow:1;padding:10px}
            .menu a{display:block;padding:12px 20px;text-decoration:none;color:#333;border-left:4px solid transparent;transition:background .3s,border-color .3s}
            .menu a:hover,.menu a.active{background-color:#f0fdf4;border-left-color:#22c55e;color:#16a34a}
            .logout{padding:12px 20px;color:red;text-decoration:none;border-top:1px solid #eee}
            .main-dashboard{flex-grow:1;padding:24px 32px;overflow-y:auto;max-width:100%;box-sizing:border-box;display:flex;flex-direction:column;gap:20px;background:#f5f6f8;}
            .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,.06);}
            .card-header{padding:20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px}
            .card-body{padding:24px}
            .metric-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px}
            .metric-card{border-radius:12px;padding:20px;display:flex;flex-direction:column;gap:6px;font-weight:600}
            .metric-card span{font-size:13px;text-transform:uppercase;letter-spacing:.08em}
            .metric-value{font-size:32px;font-weight:800}
            table{width:100%;border-collapse:collapse;font-size:14px}
            table th,table td{padding:12px 16px;text-align:left;border-bottom:1px solid #e5e7eb}
            table thead th{background:#f9fafb;color:#6b7280;font-size:12px;text-transform:uppercase;letter-spacing:.06em}
            table tbody tr{animation:fadeInUp .45s ease both;animation-delay:calc(.04s*var(--row-index,0));}
            .rating-stars{display:flex;align-items:center;gap:4px;color:#f59e0b}
            .rating-stars i{font-size:14px}
            .badge-super{display:inline-block;padding:4px 8px;border-radius:999px;background:#e0f2fe;color:#1d4ed8;font-size:11px;font-weight:700;text-transform:uppercase}
            .empty-state{padding:40px;text-align:center;color:#6b7280}
            .pagination{display:flex;justify-content:flex-end;padding:16px}
            .pagination svg{height:18px}
            .sidebar-toggle{display:none;align-self:flex-start;background:#16a34a;color:#fff;border:none;border-radius:8px;padding:10px 12px;cursor:pointer;font-weight:600;margin-bottom:10px}
            .overlay{position:fixed;inset:0;background:rgba(0,0,0,.35);display:none;z-index:1000;}
            .overlay.show{display:block}
            .admin-review-gallery{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
            .admin-review-gallery a{display:block;width:70px;height:70px;overflow:hidden;border-radius:10px;border:1px solid #e5e7eb;box-shadow:0 6px 15px rgba(15,23,42,.08);transition:transform .25s ease,box-shadow .25s ease}
            .admin-review-gallery img{width:100%;height:100%;object-fit:cover}
            .admin-review-gallery a:hover{transform:translateY(-4px) scale(1.03);box-shadow:0 12px 24px rgba(15,23,42,.12)}
            @keyframes fadeInUp{0%{opacity:0;transform:translateY(12px);}100%{opacity:1;transform:translateY(0);}}
            @media(max-width:992px){
                .sidebar{position:fixed;left:0;top:0;bottom:0;transform:translateX(-100%);height:100dvh;width:260px;}
                .sidebar.open{transform:translateX(0)}
                .sidebar-toggle{display:inline-flex;gap:8px}
                .main-dashboard{padding:20px}
            }
            @media(max-width:576px){
                .metric-grid{grid-template-columns:repeat(1,minmax(0,1fr))}
                table th,table td{padding:10px 12px}
                .card-body{padding:16px}
                .admin-review-gallery{gap:6px}
                .admin-review-gallery a{width:60px;height:60px}
            }
        </style>

        <aside class="sidebar" aria-label="Menú lateral">
            <div>
                <div class="sidebar-header">
                    <img src="{{ asset('img/LogoAtlan.png') }}" alt="Logo">
                    <div class="brand">Supermercado Atlantia</div>
                    <div class="subtitle">Panel del Administrador</div>
                    <div class="admin-name">👤 {{ Auth::user()->name }}</div>
                </div>
                <nav class="menu">
                    <a href="{{ route('admin.panel') }}"><i class="fas fa-chart-pie"></i> Dashboard</a>
                    <a href="{{ route('admin.usuarios.index') }}"><i class="fas fa-user-cog"></i> Usuarios</a>
                    <a href="{{ route('admin.usuarios.create') }}"><i class="fas fa-user-plus"></i> Crear usuario</a>
                    <a href="{{ route('admin.productos.index') }}"><i class="fas fa-box"></i> Productos</a>
                    <a href="{{ route('admin.categorias.index') }}"><i class="fas fa-tags"></i> Categorías</a>
                    <a href="{{ route('admin.pedidos.index') }}"><i class="fas fa-truck"></i> Pedidos</a>
                    <a href="{{ route('admin.repartidores.index') }}"><i class="fas fa-users"></i> Repartidores</a>
                    <a href="{{ route('admin.vendedores.index') }}"><i class="fas fa-store"></i> Vendedores</a>
                    <a href="{{ route('admin.delivery-zones.index') }}"><i class="fas fa-map-marked-alt"></i> Área de servicio</a>
                    <a href="{{ route('admin.facturacion') }}"><i class="fas fa-file-invoice-dollar"></i> Facturación</a>
                    <a href="{{ route('admin.reportes.index') }}"><i class="fas fa-file-alt"></i> Reportes</a>
                    <a href="{{ route('admin.carritos') }}"><i class="fas fa-shopping-cart"></i> Carritos</a>
                    <a href="{{ route('admin.cupones') }}"><i class="fas fa-percentage"></i> Cupones y promociones</a>
                    <a href="{{ route('admin.reseñas.index') }}" class="active"><i class="fas fa-star"></i> Reseñas</a>
                    <a href="#"><i class="fas fa-cog"></i> Configuración</a>
                </nav>
            </div>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </aside>

        <div class="overlay" aria-hidden="true"></div>

        <main class="main-dashboard">
            <button class="sidebar-toggle" aria-label="Abrir menú lateral"><i class="fas fa-bars"></i> Menú</button>

            <div class="card">
                <div class="card-body">
                    <h1 style="font-size:26px;font-weight:800;color:#1f2937;">Reseñas del supermercado</h1>
                    <p style="color:#6b7280;margin-top:8px;">Controla la experiencia de los clientes y las respuestas de los vendedores en un solo lugar.</p>

                    <div class="metric-grid" style="margin-top:24px;">
                        <div class="metric-card" style="background:#ecfdf5;border:1px solid #bbf7d0;color:#15803d;">
                            <span>Promedio general</span>
                            <div class="metric-value">{{ number_format($promedioGeneral, 2) }}</div>
                            <small style="color:#166534;font-weight:600;">de 5.00 estrellas</small>
                        </div>
                        <div class="metric-card" style="background:#fffbeb;border:1px solid #fef3c7;color:#b45309;">
                            <span>Total de reseñas</span>
                            <div class="metric-value">{{ number_format($totalReseñas) }}</div>
                            <small style="color:#b45309;font-weight:600;">comentarios verificados</small>
                        </div>
                        <div class="metric-card" style="background:#eef2ff;border:1px solid #c7d2fe;color:#4338ca;">
                            <span>Última reseña</span>
                            <div class="metric-value" style="font-size:24px;">{{ optional($reseñas->first())->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
                            <small style="color:#4338ca;font-weight:600;">actualización reciente</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 style="font-size:20px;font-weight:700;color:#1f2937;">Listado de reseñas</h2>
                    <span style="color:#6b7280;font-size:14px;">Página {{ $reseñas->currentPage() }} de {{ $reseñas->lastPage() }}</span>
                </div>
                <div class="card-body" style="padding:0;">
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Vendedor</th>
                                <th>Calificación</th>
                                <th>Comentario</th>
                                <th>Fecha</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($reseñas as $reseña)
                                <tr style="--row-index: {{ $loop->index }};">
                                    <td>
                                        <div style="font-weight:700;color:#1f2937;">{{ $reseña->producto->nombre ?? 'Producto eliminado' }}</div>
                                        <div style="font-size:12px;color:#6b7280;margin-top:4px;">Pedido {{ $reseña->pedido?->codigo ?? ($reseña->pedido_id ? '#'.$reseña->pedido_id : '—') }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;color:#1f2937;">{{ $reseña->cliente->name ?? 'Cliente eliminado' }}</div>
                                        <div style="font-size:12px;color:#6b7280;">{{ $reseña->cliente->email ?? '—' }}</div>
                                    </td>
                                    <td>
                                        @if ($reseña->producto?->vendor)
                                            <div style="font-weight:600;color:#1f2937;">{{ $reseña->producto->vendor->name }}</div>
                                            <div style="font-size:12px;color:#6b7280;">{{ $reseña->producto->vendor->email }}</div>
                                        @else
                                            <span class="badge-super">Supermercado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $reseña->estrellas ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                            <span style="margin-left:6px;color:#4b5563;font-weight:700;">{{ $reseña->estrellas }}/5</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="color:#4b5563;line-height:1.5;">{{ $reseña->comentario ?: '—' }}</div>
                                        <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:6px;font-size:11px;font-weight:600;">
                                            @if ($reseña->uso_score)
                                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;background:#fce7f3;color:#9d174d;"><i class="fas fa-sun"></i> Uso {{ $reseña->uso_score }}/5</span>
                                            @endif
                                            @if ($reseña->comodidad_score)
                                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;background:#fce7f3;color:#9d174d;"><i class="fas fa-feather"></i> Comodidad {{ $reseña->comodidad_score }}/5</span>
                                            @endif
                                            @if ($reseña->duracion_score)
                                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;background:#fce7f3;color:#9d174d;"><i class="fas fa-hourglass-half"></i> Duración {{ $reseña->duracion_score }}/5</span>
                                            @endif
                                            @if ($reseña->talla_percebida_label)
                                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;border:1px solid #fbcfe8;background:#fff;color:#9d174d;"><i class="fas fa-ruler-horizontal"></i> {{ $reseña->talla_percebida_label }}</span>
                                            @endif
                                            @if ($reseña->reaccion_label)
                                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:999px;border:1px solid #fbcfe8;background:#fff;color:#9d174d;"><i class="fas fa-heart"></i> {{ $reseña->reaccion_label }}</span>
                                            @endif
                                        </div>
                                        @if ($reseña->imagenes->isNotEmpty())
                                            <div class="admin-review-gallery" role="list">
                                                @foreach ($reseña->imagenes as $imagen)
                                                    <a href="{{ asset('storage/' . $imagen->ruta) }}" target="_blank" rel="noopener" role="listitem">
                                                        <img src="{{ asset('storage/' . $imagen->ruta) }}" alt="Foto de reseña del producto {{ $reseña->producto->nombre ?? '' }}">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if ($reseña->respuesta_vendedor)
                                            <div style="margin-top:8px;font-size:12px;color:#4338ca;background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:10px;">
                                                <strong>Respuesta:</strong> {{ $reseña->respuesta_vendedor }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;color:#6b7280;font-size:13px;">{{ $reseña->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-star-half-alt" style="font-size:28px;margin-bottom:12px;display:block;"></i>
                                            Aún no hay reseñas registradas.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">
                        {{ $reseñas->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
