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

            /* ===== Sidebar ===== */
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
                z-index:1001; /* sobre overlay */
            }

            .sidebar-header{
                display:flex;align-items:center;padding:20px;border-bottom:1px solid #eee;
                flex-direction:column;text-align:center
            }
            .sidebar-header img{width:40px;height:40px;margin-bottom:10px}
            .sidebar-header .brand{font-weight:bold;color:#1e3a8a;font-size:18px}
            .sidebar-header .subtitle{font-size:12px;color:#999}
            .sidebar-header .admin-name{font-size:13px;color:#333;margin-top:5px}

            .menu{flex-grow:1;padding:10px}
            .menu a{
                display:block;padding:12px 20px;text-decoration:none;color:#333;
                border-left:4px solid transparent;transition:background .3s,border-color .3s
            }
            .menu a:hover,.menu a.active{background-color:#f0fdf4;border-left-color:#22c55e;color:#16a34a}

            .logout{padding:12px 20px;color:red;text-decoration:none;border-top:1px solid #eee}

            /* ===== Main ===== */
            .main-dashboard{
                flex-grow:1;padding:20px 30px;overflow-y:auto;max-width:100%;box-sizing:border-box;
                display:flex;flex-direction:column;gap:15px;
            }

            .sidebar-toggle{
                display:none; /* visible en móvil */
                align-self:flex-start;
                background:#16a34a;color:#fff;border:none;border-radius:8px;
                padding:10px 12px;cursor:pointer;font-weight:600;
            }

            .metrics{
                display:grid !important;
                grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
                gap:15px;margin-bottom:5px;width:100%;
            }
            .metric{background:#fff;padding:15px;border:2px solid #ddd;border-radius:5px}
            .metric.green{border-color:#16a34a}
            .metric.yellow{border-color:#eab308}
            .metric.red{border-color:#dc2626}
            .metric .title{font-size:12px;color:#666;margin-bottom:8px}
            .metric .value{font-size:24px;font-weight:bold;color:#333}

            .row-dashboard{
                display:grid !important;
                grid-template-columns:1fr 1fr;
                gap:15px;margin-bottom:5px;width:100%;
            }

            .card{
                background:#fff;padding:15px;border:1px solid #ddd;border-radius:5px;
                display:flex;flex-direction:column;min-width:0;
            }
            .card h2{margin-bottom:15px;font-size:16px;color:#333}

            /* tablas */
            .card.pedidos{overflow-x:auto}
            table{width:100%;border-collapse:collapse;font-size:14px;min-width:640px}
            table th,table td{padding:8px;border-bottom:1px solid #eee;text-align:left}

            .badge{display:inline-block;padding:3px 7px;border-radius:12px;font-size:12px;background:#ccc;color:#fff}
            .badge.pending{background:#fbbf24}
            .badge.shipped{background:#3b82f6}
            .badge.delivered{background:#22c55e}

            .notifications li,.repartidores li{padding:6px 0;font-size:14px}
            .notifications .warning{color:#eab308}
            .notifications .info{color:#3b82f6}
            .notifications .success{color:#22c55e}

            .chart{height:300px}

            /* ===== Overlay para sidebar móvil ===== */
            .overlay{
                position:fixed;inset:0;background:rgba(0,0,0,.35);
                display:none;z-index:1000;
            }
            .overlay.show{display:block}

            /* ====== Responsive ====== */

            /* <= 1200: un poco más compacto */
            @media (max-width:1200px){
                .metric .value{font-size:22px}
                .main-dashboard{padding:18px}
            }

            /* <= 992px: sidebar en off-canvas y grid a 1 columna */
            @media (max-width:992px){
                .sidebar{
                    position:fixed;left:0;top:0;bottom:0;transform:translateX(-100%);
                    height:100dvh; /* móvil moderno */
                    width:260px;
                }
                .sidebar.open{transform:translateX(0)}
                .sidebar-toggle{display:inline-flex;gap:8px}
                .row-dashboard{grid-template-columns:1fr}
                .metrics{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}
                .main-dashboard{padding:16px}
                .chart{height:260px}
            }

            /* <= 576px: tipografía y bloques más pequeños */
            @media (max-width:576px){
                .metrics{grid-template-columns:repeat(2,minmax(0,1fr))}
                .metric .value{font-size:20px}
                .card h2{font-size:15px}
                .chart{height:220px}
            }
        </style>

        <!-- Sidebar -->
        <aside class="sidebar" aria-label="Menú lateral">
            <div>
                <div class="sidebar-header">
                    <img src="{{ asset('img/LogoAtlan.png') }}" alt="Logo" style="width:90px;height:70px;">
                    <div class="brand">Supermercado Atlantia</div>
                    <div class="subtitle">Panel del Administrador</div>
                    <div class="admin-name">👤 {{ Auth::user()->name }}</div>
                </div>

                <nav class="menu">
                    <a href="{{ route('admin.panel') }}" class="active"><i class="fas fa-chart-pie"></i> Dashboard</a>
                    <a href="{{ route('admin.productos.index') }}"><i class="fas fa-box"></i> Productos</a>
                    <a href="{{ route('admin.categorias.index') }}"><i class="fas fa-tags"></i> Categorías</a>
                    <a href="{{ route('admin.clientes') }}"><i class="fas fa-users"></i> Clientes</a>
                    <a href="{{ route('admin.repartidores.index') }}"><i class="fas fa-users"></i> Repartidores</a>
                    <a href="{{ route('admin.pedidos.index') }}"><i class="fas fa-truck"></i> Pedidos</a>
                    <a href="{{ route('admin.vendedores.index') }}"><i class="fas fa-store"></i> Vendedores</a>
                    <a href="{{ route('admin.facturacion') }}"><i class="fas fa-file-alt"></i> Facturación</a>
                    <a href="{{ route('admin.reportes.index') }}"><i class="fas fa-file-alt"></i> Reportes</a>
                    <a href="{{ route('admin.carritos') }}"><i class="fas fa-shopping-cart"></i> Carritos</a>
                    <a href="{{ route('admin.cupones') }}"><i class="fas fa-percentage"></i> Cupones y promociones</a>
                    <a href="#"><i class="fas fa-cog"></i> Configuración</a>
                </nav>
            </div>

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="logout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </aside>

        <!-- Overlay para móvil -->
        <div class="overlay" aria-hidden="true"></div>

        <!-- Main Content -->
        <main class="main-dashboard">
            <!-- Botón hamburguesa (solo móvil) -->
            <button class="sidebar-toggle" aria-label="Abrir menú lateral">
                <i class="fas fa-bars"></i> Menú
            </button>

            <h1>Dashboard General</h1>

            <!-- Métricas -->
            <div class="metrics">
                <div class="metric green">
                    <div class="title">Clientes Registrados</div>
                    <div class="value">1,240</div>
                </div>
                <div class="metric green">
                    <div class="title">Ingresos Totales</div>
                    <div class="value">Q125,300.00</div>
                </div>
                <div class="metric yellow">
                    <div class="title">Productos Disponibles</div>
                    <div class="value">362</div>
                </div>
                <div class="metric red">
                    <div class="title">Órdenes Completadas</div>
                    <div class="value">893</div>
                </div>
            </div>

            <!-- Segunda fila -->
            <div class="row-dashboard">
                <!-- Últimos pedidos -->
                <div class="card pedidos">
                    <h2><i class="fas fa-list"></i> Últimos pedidos</h2>
                    <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>#1023</td>
                            <td>Luis Mendoza</td>
                            <td>Q95.00</td>
                            <td><span class="badge pending">Pendiente</span></td>
                        </tr>
                        <tr>
                            <td>#1024</td>
                            <td>María Hernández</td>
                            <td>Q180.00</td>
                            <td><span class="badge shipped">Enviado</span></td>
                        </tr>
                        <tr>
                            <td>#1025</td>
                            <td>Kevin Rodríguez</td>
                            <td>Q220.00</td>
                            <td><span class="badge delivered">Entregado</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico -->
                <div class="card chart">
                    <h2><i class="fas fa-chart-bar"></i> Ventas recientes</h2>
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>

            <!-- Tercera fila -->
            <div class="row-dashboard">
                <!-- Notificaciones -->
                <div class="card">
                    <h2><i class="fas fa-bell"></i> Notificaciones administrativas</h2>
                    <ul class="notifications">
                        <li class="warning">Producto "Coca Cola 600ml" está por agotarse</li>
                        <li class="info">Nuevo pedido sin asignar</li>
                        <li class="success">Juan Pérez completó 12 entregas hoy</li>
                    </ul>
                </div>

                <!-- Repartidores activos -->
                <div class="card">
                    <h2><i class="fas fa-truck"></i> Repartidores activos</h2>
                    <ul class="repartidores">
                        <li>Juan Pérez - <span class="badge">4 entregas</span></li>
                        <li>María López - <span class="badge">3 entregas</span></li>
                        <li>Carlos Jiménez - <span class="badge">1 entrega</span></li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <!-- Font Awesome (CSS) para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

    <script>
        // Toggle sidebar en móvil
        const btnToggle = document.querySelector('.sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay');

        function closeSidebar(){
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        }
        function openSidebar(){
            sidebar.classList.add('open');
            overlay.classList.add('show');
        }

        if(btnToggle){
            btnToggle.addEventListener('click', () => {
                if(sidebar.classList.contains('open')) closeSidebar(); else openSidebar();
            });
        }
        if(overlay){
            overlay.addEventListener('click', closeSidebar);
        }
        // Cerrar con ESC
        document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeSidebar(); });
        // Cerrar al navegar por un link del menú (útil en móvil)
        document.querySelectorAll('.menu a').forEach(a => a.addEventListener('click', closeSidebar));

        // Chart
        const ctx = document.getElementById('ventasChart').getContext('2d');
        const ventasChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Ene','Feb','Mar','Abr','May','Jun'],
                datasets: [{
                    label: 'Ventas por mes',
                    data: [3100,4200,3700,4600,5200,6100],
                    backgroundColor:'#3b82f6'
                }]
            },
            options: {
                responsive:true,
                maintainAspectRatio:false,
                scales:{ y:{ beginAtZero:true } }
            }
        });
    </script>
@endsection
