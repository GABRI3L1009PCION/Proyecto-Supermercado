<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Panel de Vendedor | Supermercado Atlantia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ===================== CSS (corregido y limpio) ===================== -->
    <style>
        /* =======================================================
           🎨 VARIABLES GLOBALES (definidas primero)
           ======================================================= */
        :root {
            --primary: #5a0a2e;
            --primary-hover: #430724;
            --secondary: #d16ba5;
            --success: #68b684;
            --warning: #f4a259;
            --danger: #e94f64;
            --light: #f6eef2;
            --dark: #3a0821;
            --gray: #9c8c92;
            --sidebar-width: 260px;
        }

        /* =======================================================
           🌐 RESET Y BASE
           ======================================================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            background: linear-gradient(180deg, #f6eef2 0%, #ffffff 35%);
            color: #3d1f2c;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        /* =======================================================
           ⚙️ LAYOUT GENERAL
           ======================================================= */
        .dashboard-container { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-width); padding: 1rem 1rem 2rem; }

        /* =======================================================
           📂 SIDEBAR
           ======================================================= */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            color: #fff;
            position: fixed;
            inset: 0 auto 0 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        .sidebar-header { padding: 1.25rem 1rem; text-align: center; border-bottom: 1px solid rgba(255,255,255,.12); }
        .sidebar-header h2 { font-size: 1.25rem; display: flex; align-items: center; justify-content: center; gap: .5rem; }
        .sidebar-menu { padding: .5rem 0; }
        .menu-item {
            display: flex; align-items: center; gap: .8rem;
            padding: .85rem 1.25rem; color: rgba(255,255,255,.85); text-decoration: none;
            border-left: 4px solid transparent;
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }
        .menu-item i { width: 20px; text-align: center; }
        .menu-item:hover, .menu-item.active { background: rgba(255,255,255,.12); color: #fff; border-left-color: var(--secondary); }
        .menu-badge { margin-left: auto; background: var(--secondary); padding: .15rem .5rem; border-radius: 12px; font-size: .8rem; color: #fff; }

        /* Overlay (para móvil) */
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 900; }

        /* =======================================================
           🔝 TOPBAR
           ======================================================= */
        .top-bar {
            display: flex; justify-content: space-between; align-items: center; gap: 1rem;
            background: rgba(255,255,255,0.92); padding: 1rem; border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07); margin-bottom: 1rem;
        }
        .top-left { display: flex; align-items: center; gap: .75rem; }
        .btn-icon { display: none; width: 40px; height: 40px; border: none; border-radius: 10px; background: var(--primary); color: #fff; cursor: pointer; }
        .welcome-message h1 { font-size: 1.25rem; color: var(--dark); }
        .welcome-message p { color: var(--gray); font-size: .95rem; }

        /* =======================================================
           📊 TARJETAS DE KPIs
           ======================================================= */
        .stats-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1rem; }
        .stat-card { background: rgba(255,255,255,0.95); border-radius: 16px; padding: 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
        .stat-icon { font-size: 1.7rem; margin-bottom: .6rem; color: var(--secondary); }
        .stat-number { font-size: 1.6rem; font-weight: 700; color: var(--dark); line-height: 1; }
        .stat-title { margin-top: .35rem; color: var(--gray); font-size: .9rem; }

        /* =======================================================
           👤 PERFIL DEL VENDEDOR
           ======================================================= */
        .profile-wrapper { display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; }
        .profile-card {
            background: linear-gradient(160deg, rgba(90,10,46,0.95), rgba(209,107,165,0.85));
            color: #fff;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 20px 35px rgba(90,10,46,0.18);
            position: relative;
            overflow: hidden;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 32px;
            overflow: hidden;
            margin-bottom: 1rem;
            border: 4px solid rgba(255,255,255,0.35);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-upload {
            display: inline-flex; align-items: center; gap: .45rem;
            background: rgba(255,255,255,0.18); color: #fff;
            border-radius: 999px; padding: .45rem 1.1rem; font-size: .9rem;
            cursor: pointer; transition: background .2s ease;
        }
        .avatar-upload:hover { background: rgba(255,255,255,0.3); }
        .mood-badge {
            display: inline-flex; align-items: center; gap: .35rem;
            background: rgba(255,255,255,0.15); padding: .35rem .8rem;
            border-radius: 999px; margin-top: .75rem; font-size: .85rem;
        }
        .profile-form {
            background: rgba(255,255,255,0.92);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 15px 25px rgba(58,8,33,0.08);
        }
        .form-control {
            width: 100%;
            padding: .75rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(90,10,46,0.15);
            background: rgba(255,255,255,0.95);
            color: #3d1f2c;
            font-size: .95rem;
        }
        .form-control:focus {
            outline: none;
            border-color: rgba(209,107,165,0.75);
            box-shadow: 0 0 0 3px rgba(209,107,165,0.18);
        }

        /* =======================================================
           🧱 SECCIONES GENERALES
           ======================================================= */
        .dashboard-section {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(58,8,33,0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .section-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.1rem 1.5rem;
            background: var(--light);
            border-bottom: 1px solid rgba(90,10,46,0.12);
        }
        .section-header h2 {
            font-size: 1.05rem;
            color: var(--dark);
            display: flex; align-items: center; gap: .5rem;
        }
        .section-content { padding: 1.25rem; }

        /* =======================================================
           📋 TABLAS
           ======================================================= */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .8rem 1rem; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        thead th { background: #f8fafc; color: #111827; font-weight: 700; }
        tr:hover { background: #f9fafb; }

        /* =======================================================
           🔘 BOTONES Y BADGES
           ======================================================= */
        .btn {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .6rem 1.1rem;
            border: none; border-radius: 999px;
            font-weight: 600; cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .btn-sm { padding: .35rem .7rem; font-size: .85rem; border-radius: 7px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: var(--secondary); color: #fff; }
        .btn-success { background: var(--success); color: #fff; }
        .btn-warning { background: var(--warning); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 15px rgba(58,8,33,0.15); }

        /* =======================================================
           📱 RESPONSIVO
           ======================================================= */
        @media (max-width: 992px) {
            .stats-cards { grid-template-columns: repeat(2,1fr); }
            .btn-icon { display: inline-flex; align-items: center; justify-content: center; }
            .main-content { margin-left: 0; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: none; }
            .overlay.show { display: block; }
            .profile-wrapper { grid-template-columns: 1fr; }
        }
        @media (max-width: 576px) {
            .stats-cards { grid-template-columns: 1fr; }
            .top-bar { flex-direction: column; }
            .section-header { flex-direction: column; align-items: flex-start; }
            .profile-card { text-align: center; }
        }
    </style>
</head>
<body>

<!-- ===================== DASHBOARD VENDEDOR ===================== -->
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-store"></i> Mi Panel</h2>
        </div>
        <nav class="sidebar-menu">
            <a href="#top" class="menu-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="#productos" class="menu-item"><i class="fas fa-box"></i> Mis Productos</a>
            <a href="#surtido" class="menu-item"><i class="fas fa-shopping-cart"></i> Surtido</a>
            <a href="#mensajeria" class="menu-item"><i class="fas fa-envelope"></i> Mensajería</a>
        </nav>
    </aside>

    <!-- Contenido principal -->
    <main class="main-content" id="top">
        <div class="top-bar">
            <div class="top-left">
                <button class="btn-icon" id="btnToggleSidebar"><i class="fas fa-bars"></i></button>
                <div class="welcome-message">
                    <h1>Bienvenido, Tony</h1>
                    <p>Resumen de tu actividad comercial</p>
                </div>
            </div>
            <button class="btn btn-primary" onclick="openNuevoProducto()"><i class="fas fa-plus"></i> Nuevo Producto</button>
        </div>

        <!-- Sección ejemplo: KPIs -->
        <section class="stats-cards">
            <article class="stat-card"><div class="stat-icon"><i class="fas fa-box"></i></div><div class="stat-number">15</div><div class="stat-title">Productos Activos</div></article>
            <article class="stat-card"><div class="stat-icon"><i class="fas fa-shopping-cart"></i></div><div class="stat-number">23</div><div class="stat-title">Pedidos este Mes</div></article>
            <article class="stat-card"><div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div><div class="stat-number">Q 8,450</div><div class="stat-title">Ventas Totales</div></article>
            <article class="stat-card"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-number">3</div><div class="stat-title">Pendientes</div></article>
        </section>
    </main>
</div>

<!-- JS Sidebar -->
<script>
    const sidebar = document.getElementById('sidebar');
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    btnToggleSidebar.addEventListener('click', () => sidebar.classList.toggle('open'));
</script>

</body>
</html>
