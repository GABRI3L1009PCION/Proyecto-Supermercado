<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Empleado - Supermercado Online</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3490dc;
            --secondary: #6c757d;
            --success: #38c172;
            --danger: #e3342f;
            --warning: #f6993f;
            --info: #6cb2eb;
            --light: #f8f9fa;
            --dark: #343a40;
            --sidebar-width: 250px;
            --header-height: 60px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7f9;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--dark);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background: #2c3e50;
            text-align: center;
        }

        .sidebar-header h3 {
            color: white;
            margin: 0;
        }

        .sidebar-menu {
            padding: 10px 0;
        }

        .sidebar-menu ul {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            padding: 0;
        }

        .sidebar-menu a {
            padding: 12px 20px;
            display: block;
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 16px;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #2c3e50;
            color: white;
            border-left: 4px solid var(--primary);
        }

        .sidebar-menu i {
            margin-right: 10px;
            width: 25px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            position: relative;
        }

        /* Header */
        .header {
            background: white;
            height: var(--header-height);
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .header-left h1 {
            font-size: 24px;
            color: var(--dark);
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .notification-badge {
            position: relative;
            margin-right: 20px;
        }

        .notification-badge i {
            font-size: 20px;
            color: var(--secondary);
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        /* Dashboard */
        .dashboard {
            padding: 20px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: white;
        }

        .bg-primary { background: var(--primary); }
        .bg-success { background: var(--success); }
        .bg-warning { background: var(--warning); }
        .bg-danger { background: var(--danger); }

        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--secondary);
            margin: 0;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 18px;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-preparando {
            background: #fff0e5;
            color: var(--warning);
        }

        .status-en-camino {
            background: #e0f4ff;
            color: var(--primary);
        }

        .status-entregado {
            background: #e6f7ee;
            color: var(--success);
        }

        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #2779bd;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                text-align: center;
            }

            .sidebar-header h3, .sidebar-menu span {
                display: none;
            }

            .sidebar-menu i {
                margin-right: 0;
                font-size: 20px;
            }

            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3>SuperOnline</h3>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li><a href="#" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="#"><i class="fas fa-shopping-basket"></i> <span>Pedidos</span></a></li>
            <li><a href="#"><i class="fas fa-boxes"></i> <span>Inventario</span></a></li>
            <li><a href="#"><i class="fas fa-truck"></i> <span>Repartos</span></a></li>
            <li><a href="#"><i class="fas fa-users"></i> <span>Clientes</span></a></li>
            <li><a href="#"><i class="fas fa-chart-line"></i> <span>Reportes</span></a></li>
            <li><a href="#"><i class="fas fa-cog"></i> <span>Configuración</span></a></li>
            <li><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span></a></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <h1>Panel de Empleado</h1>
        </div>
        <div class="header-right">
            <div class="notification-badge">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </div>
            <div class="user-profile">
                <img src="https://ui-avatars.com/api/?name=Empleado+Ejemplo&background=3490dc&color=fff" alt="Usuario">
                <span>María García</span>
            </div>
        </div>
    </div>

    <!-- Dashboard -->
    <div class="dashboard">
        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <div class="stat-info">
                    <h3>42</h3>
                    <p>Pedidos Hoy</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>28</h3>
                    <p>Entregados</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>9</h3>
                    <p>En Preparación</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>5</h3>
                    <p>Incidencias</p>
                </div>
            </div>
        </div>

        <!-- Pedidos Recientes -->
        <div class="card">
            <div class="card-header">
                <h2>Pedidos Recientes</h2>
                <button class="btn btn-primary">Ver Todos</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Cliente</th>
                            <th>Fecha/Hora</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>#12345</td>
                            <td>Juan Pérez</td>
                            <td>10/05/2023 09:30</td>
                            <td>€85.90</td>
                            <td><span class="status status-preparando">En preparación</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Detalles</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#12344</td>
                            <td>Ana Rodríguez</td>
                            <td>10/05/2023 10:15</td>
                            <td>€112.50</td>
                            <td><span class="status status-en-camino">En camino</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Detalles</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#12343</td>
                            <td>Carlos López</td>
                            <td>10/05/2023 08:45</td>
                            <td>€67.80</td>
                            <td><span class="status status-entregado">Entregado</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Detalles</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#12342</td>
                            <td>Marta Sánchez</td>
                            <td>09/05/2023 19:20</td>
                            <td>€93.40</td>
                            <td><span class="status status-entregado">Entregado</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Detalles</button>
                            </td>
                        </tr>
                        <tr>
                            <td>#12341</td>
                            <td>Javier Gómez</td>
                            <td>09/05/2023 17:50</td>
                            <td>€124.90</td>
                            <td><span class="status status-entregado">Entregado</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Detalles</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tareas Pendientes -->
        <div class="card">
            <div class="card-header">
                <h2>Tareas Pendientes</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Prioridad</th>
                            <th>Fecha Límite</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Verificar stock de productos frescos</td>
                            <td>Alta</td>
                            <td>10/05/2023 12:00</td>
                            <td><span class="status status-preparando">Pendiente</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Realizar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Preparar pedidos para franja de 14-16h</td>
                            <td>Alta</td>
                            <td>10/05/2023 13:30</td>
                            <td><span class="status status-preparando">Pendiente</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Realizar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Contactar con proveedor de lácteos</td>
                            <td>Media</td>
                            <td>11/05/2023 10:00</td>
                            <td><span class="status status-preparando">Pendiente</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm">Realizar</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Funcionalidad básica para el panel
    document.addEventListener('DOMContentLoaded', function() {
        // Simular carga de datos
        console.log("Panel de empleado cargado");

        // Aquí iría la lógica para cargar datos desde el backend en Laravel
        // Por ejemplo: fetch('/api/pedidos').then(...)

        // Ejemplo de toggle para el sidebar en modo móvil
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        if(menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });
        }
    });
</script>
</body>
</html>
