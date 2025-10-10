<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Panel de Vendedor | Supermercado Atlantia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ===================== CSS (responsivo y ordenado) ===================== -->
    <style>
        /* =========================
           Variables y base
        ========================== */
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(180deg, #f6eef2 0%, #ffffff 35%);
            color: #3d1f2c;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        /* =========================
           Layout general
        ========================== */
        .dashboard-container { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: var(--sidebar-width); padding: 1rem 1rem 2rem; }

        /* =========================
           Sidebar
        ========================== */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary);
            color: #fff;
            position: fixed;
            inset: 0 auto 0 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transform: translateX(0);
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

        /* Overlay (móvil) */
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.35); z-index: 900; }

        /* =========================
           Topbar
        ========================== */
        .top-bar {
            display: flex; justify-content: space-between; align-items: center; gap: 1rem;
            background: rgba(255,255,255,0.92); padding: 1rem; border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07); margin-bottom: 1rem;
        }
        .top-left { display: flex; align-items: center; gap: .75rem; }
        .btn-icon { display: none; width: 40px; height: 40px; border: none; border-radius: 10px; background: var(--primary); color: #fff; cursor: pointer; }
        .welcome-message h1 { font-size: 1.25rem; color: var(--dark); }
        .welcome-message p { color: var(--gray); font-size: .95rem; }

        /* =========================
           Cards (KPIs)
        ========================== */
        .stats-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin-bottom: 1rem; }
        .stat-card { background: rgba(255,255,255,0.95); border-radius: 16px; padding: 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
        .stat-icon { font-size: 1.7rem; margin-bottom: .6rem; color: var(--secondary); }
        .stat-number { font-size: 1.6rem; font-weight: 700; color: var(--dark); line-height: 1; }
        .stat-title { margin-top: .35rem; color: var(--gray); font-size: .9rem; }

        /* =========================
           Perfil del vendedor
        ========================== */
        .profile-section .section-header {
            background: rgba(209, 107, 165, 0.1);
            border-bottom-color: rgba(209, 107, 165, 0.25);
        }
        .profile-wrapper {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 1.5rem;
        }
        .profile-card {
            background: linear-gradient(160deg, rgba(90,10,46,0.95), rgba(209,107,165,0.85));
            color: #fff;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 20px 35px rgba(90,10,46,0.18);
            position: relative;
            overflow: hidden;
        }
        .profile-card::after {
            content: "";
            position: absolute;
            inset: auto -35% -35% auto;
            width: 220px;
            height: 220px;
            background: rgba(255,255,255,0.12);
            border-radius: 40% 60% 70% 30%;
            filter: blur(0.5px);
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 32px;
            overflow: hidden;
            margin-bottom: 1rem;
            position: relative;
            border: 4px solid rgba(255,255,255,0.35);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .avatar-upload {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: rgba(255,255,255,0.18);
            color: #fff;
            border-radius: 999px;
            padding: .45rem 1.1rem;
            font-size: .9rem;
            cursor: pointer;
            transition: background .2s ease;
        }
        .avatar-upload:hover { background: rgba(255,255,255,0.3); }
        .mood-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: rgba(255,255,255,0.15);
            color: #fff;
            padding: .35rem .8rem;
            border-radius: 999px;
            margin-top: .75rem;
            font-size: .85rem;
            letter-spacing: .3px;
        }
        .profile-bio {
            margin-top: 1rem;
            font-size: .95rem;
            line-height: 1.6;
            color: rgba(255,255,255,0.82);
        }
        .profile-meta { margin-top: 1.2rem; display: grid; gap: .45rem; font-size: .85rem; }
        .profile-meta span { display: flex; align-items: center; gap: .45rem; color: rgba(255,255,255,0.85); }
        .profile-form {
            background: rgba(255,255,255,0.92);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 15px 25px rgba(58,8,33,0.08);
        }
        .profile-form h3 { margin-bottom: 1rem; color: var(--dark); font-size: 1.15rem; }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }
        .form-group { display: flex; flex-direction: column; gap: .4rem; }
        .form-group label {
            font-weight: 600;
            color: var(--dark);
            font-size: .9rem;
        }
        .form-control {
            width: 100%;
            padding: .75rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(90,10,46,0.15);
            background: rgba(255,255,255,0.95);
            color: #3d1f2c;
            font-size: .95rem;
            transition: border .2s ease, box-shadow .2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: rgba(209,107,165,0.75);
            box-shadow: 0 0 0 3px rgba(209,107,165,0.18);
        }
        textarea.form-control { resize: vertical; min-height: 120px; }
        .profile-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.25rem; }
        .reviews-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin: 2rem 0 1.25rem;
        }
        .reviews-summary {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }
        .reviews-score {
            display: grid;
            place-items: center;
            width: 90px;
            height: 90px;
            border-radius: 24px;
            background: linear-gradient(160deg, rgba(209,107,165,0.18), rgba(90,10,46,0.18));
            color: var(--primary);
            font-size: 1.8rem;
            font-weight: 700;
        }
        .reviews-score span { font-size: .85rem; display: block; color: var(--gray); font-weight: 500; }
        .reviews-details { color: var(--dark); }
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.25rem;
        }
        .review-card {
            background: rgba(255,255,255,0.9);
            border-radius: 18px;
            padding: 1.25rem;
            box-shadow: 0 12px 25px rgba(58,8,33,0.08);
            border: 1px solid rgba(90,10,46,0.08);
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }
        .review-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }
        .review-customer { font-weight: 600; color: var(--dark); }
        .review-product { font-size: .85rem; color: var(--gray); }
        .star-rating {
            display: inline-flex;
            gap: .2rem;
            color: #f7b733;
            font-size: 1rem;
        }
        .review-text {
            color: #5a3c4a;
            line-height: 1.55;
            font-size: .95rem;
        }
        .review-meta { font-size: .8rem; color: var(--gray); display: flex; align-items: center; gap: .35rem; }

        /* =========================
           Secciones
        ========================== */
        .dashboard-section { background: rgba(255,255,255,0.95); border-radius: 20px; box-shadow: 0 15px 35px rgba(58,8,33,0.08); margin-bottom: 1.5rem; overflow: hidden; }
        .section-header { display: flex; justify-content: space-between; align-items: center; gap: .75rem; padding: 1.1rem 1.5rem; background: var(--light); border-bottom: 1px solid rgba(90,10,46,0.12); }
        .section-header h2 { margin: 0; font-size: 1.05rem; color: var(--dark); display: flex; align-items: center; gap: .5rem; }
        .section-content { padding: 1.25rem; }

        /* =========================
           Tabs
        ========================== */
        .tabs { display: flex; flex-wrap: wrap; gap: .25rem; border-bottom: 1px solid #e5e7eb; margin-bottom: 1rem; }
        .tab { padding: .65rem 1rem; border-bottom: 3px solid transparent; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; user-select: none; color: #374151; transition: .2s ease; }
        .tab:hover { background: #f3f4f6; }
        .tab.active { border-bottom-color: var(--primary); color: var(--primary); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* =========================
           Tabla
        ========================== */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .8rem 1rem; border-bottom: 1px solid #eee; text-align: left; vertical-align: middle; }
        thead th { background: #f8fafc; color: #111827; font-weight: 700; }
        tr:hover { background: #f9fafb; }

        /* =========================
           Botones y Badges
        ========================== */
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .6rem 1.1rem; border: none; border-radius: 999px; font-weight: 600; text-decoration: none; cursor: pointer; transition: transform .15s ease, box-shadow .15s ease; }
        .btn-sm { padding: .35rem .7rem; font-size: .85rem; border-radius: 7px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-warning { background: var(--warning); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-secondary { background: var(--secondary); color: #fff; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 15px rgba(58,8,33,0.15); }
        .badge { padding: .25rem .55rem; border-radius: 999px; font-size: .8rem; font-weight: 700; }
        .badge-success { background: #e8f5e9; color: var(--success); }
        .badge-warning { background: #fff8e1; color: var(--warning); }
        .badge-danger  { background: #ffebee; color: var(--danger); }
        .badge-info    { background: #e3f2fd; color: var(--secondary); }

        /* =========================
           Responsivo
        ========================== */
        @media (max-width: 1200px) { .stats-cards { grid-template-columns: repeat(3,1fr); } }
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
            .top-bar { flex-direction: column; align-items: stretch; }
            .section-header { flex-direction: column; align-items: flex-start; }
            .tab { flex: 1; min-width: 140px; text-align: center; }
            .profile-card { text-align: center; }
            .profile-meta { justify-items: center; }
            .reviews-header { flex-direction: column; align-items: flex-start; }
            .reviews-summary { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" aria-label="Menú lateral">
        <div class="sidebar-header">
            <h2><i class="fas fa-store"></i> <span>Mi Panel</span></h2>
        </div>
        <nav class="sidebar-menu">
            <a href="#top" class="menu-item active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="#productos" class="menu-item"><i class="fas fa-box"></i><span>Mis Productos</span><span class="menu-badge">15</span></a>
            <a href="#surtido" class="menu-item"><i class="fas fa-shopping-cart"></i><span>Solicitudes de Surtido</span><span class="menu-badge">4</span></a>
            <a href="#mensajeria" class="menu-item"><i class="fas fa-envelope"></i><span>Mensajería</span><span class="menu-badge" id="badge-msg">0</span></a>
            <a href="#" class="menu-item"><i class="fas fa-cog"></i><span>Configuración</span></a>
        </nav>
    </aside>
    <div class="overlay" id="overlay" aria-hidden="true"></div>

    <!-- Main -->
    <main class="main-content" id="top">
        <div class="top-bar">
            <div class="top-left">
                <button class="btn-icon" id="btnToggleSidebar" aria-label="Abrir menú"><i class="fas fa-bars"></i></button>
                <div class="welcome-message">
                    <h1>Bienvenido, Tony</h1>
                    <p>Resumen de tu actividad comercial</p>
                </div>
            </div>
            <div>
                <button class="btn btn-primary" onclick="openNuevoProducto()"><i class="fas fa-plus"></i> Nuevo Producto</button>
            </div>
        </div>

        <!-- Perfil del vendedor -->
        <section id="perfil" class="dashboard-section profile-section" aria-label="Perfil del vendedor">
            <header class="section-header">
                <h2><i class="fas fa-user"></i> Perfil del vendedor</h2>
                <button class="btn btn-secondary"><i class="fas fa-save"></i> Guardar cambios</button>
            </header>
            <div class="section-content">
                <div class="profile-wrapper">
                    <aside class="profile-card">
                        <div class="profile-avatar" id="avatar-preview">
                            <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=400&q=80" alt="Foto del vendedor" id="avatar-image">
                        </div>
                        <label class="avatar-upload" for="avatar-input"><i class="fas fa-camera"></i> Actualizar foto</label>
                        <input type="file" id="avatar-input" accept="image/*" style="display:none;">

                        <div class="mood-badge"><i class="fas fa-sun"></i> Estado de ánimo: <span id="mood-text">Motivado</span></div>

                        <p class="profile-bio" id="bio-text">"Comprometido con ofrecer productos frescos y experiencias memorables en cada compra."</p>

                        <div class="profile-meta">
                            <span><i class="fas fa-box"></i> 15 productos activos</span>
                            <span><i class="fas fa-star"></i> 4.8 de calificación</span>
                            <span><i class="fas fa-calendar"></i> Vendedor desde 2021</span>
                        </div>
                    </aside>

                    <form class="profile-form">
                        <h3>Personaliza tu presentación</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nombre-vendedor">Nombre completo</label>
                                <input type="text" id="nombre-vendedor" class="form-control" placeholder="Ingresa tu nombre" value="Tony Stark" />
                            </div>
                            <div class="form-group">
                                <label for="estado-animo">Estado de ánimo</label>
                                <select id="estado-animo" class="form-control">
                                    <option value="Motivado" selected>Motivado</option>
                                    <option value="Creativo">Creativo</option>
                                    <option value="Enfocado">Enfocado</option>
                                    <option value="Agradecido">Agradecido</option>
                                </select>
                            </div>
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="descripcion-vendedor">Descripción breve</label>
                                <textarea id="descripcion-vendedor" class="form-control" placeholder="Cuéntale a tus clientes sobre tu forma de trabajar">Apasionado por seleccionar los mejores productos locales y asegurar que lleguen a tu mesa con la máxima calidad.</textarea>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <button type="button" class="btn btn-primary"><i class="fas fa-check"></i> Actualizar perfil</button>
                            <button type="button" class="btn btn-secondary"><i class="fas fa-share-alt"></i> Compartir perfil</button>
                        </div>

                        <div class="reviews-header">
                            <div>
                                <h3 style="margin:0;">Reseñas de tus clientes</h3>
                                <p style="margin-top:.35rem;color:var(--gray);font-size:.9rem;">Muestra cómo valoran tus productos los compradores, estilo Shein.</p>
                            </div>
                            <div class="reviews-summary">
                                <div class="reviews-score">4.8<span>promedio</span></div>
                                <div class="reviews-details">
                                    <div class="star-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <p style="margin-top:.25rem;font-size:.9rem;color:var(--gray);">Basado en 128 reseñas verificadas</p>
                                </div>
                            </div>
                        </div>

                        <div class="reviews-grid">
                            <article class="review-card">
                                <div class="review-header">
                                    <div>
                                        <div class="review-customer">Ana López</div>
                                        <div class="review-product">Pedido: Manzana Gala premium</div>
                                    </div>
                                    <div class="star-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="review-text">“Llega siempre fresca y bien presentada. La atención de Tony es impecable, responde rápido y resuelve dudas.”</p>
                                <div class="review-meta"><i class="fas fa-check-circle"></i> Compra verificada · 02 Ago 2025</div>
                            </article>

                            <article class="review-card">
                                <div class="review-header">
                                    <div>
                                        <div class="review-customer">Carlos Méndez</div>
                                        <div class="review-product">Pedido: Leche Entera Atlántia 1L</div>
                                    </div>
                                    <div class="star-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="review-text">“Buena relación calidad-precio y entregas puntuales. Podría incluir más promociones, pero el servicio es excelente.”</p>
                                <div class="review-meta"><i class="fas fa-smile"></i> Compra verificada · 28 Jul 2025</div>
                            </article>

                            <article class="review-card">
                                <div class="review-header">
                                    <div>
                                        <div class="review-customer">María Fernanda</div>
                                        <div class="review-product">Pedido: Cereal Integral 500g</div>
                                    </div>
                                    <div class="star-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <p class="review-text">“Me encantó que enviara una nota con tips de conservación. Definitivamente seguiré comprando en su tienda.”</p>
                                <div class="review-meta"><i class="fas fa-gift"></i> Compra verificada · 20 Jul 2025</div>
                            </article>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- KPIs -->
        <section class="stats-cards" aria-label="Indicadores">
            <article class="stat-card">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-number">15</div>
                <div class="stat-title">Productos Activos</div>
            </article>
            <article class="stat-card">
                <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-number">23</div>
                <div class="stat-title">Pedidos este Mes</div>
            </article>
            <article class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-number">Q 8,450.00</div>
                <div class="stat-title">Ventas Totales</div>
            </article>
            <article class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">3</div>
                <div class="stat-title">Pendientes de Revisión</div>
            </article>
        </section>

        <!-- Productos -->
        <section id="productos" class="dashboard-section">
            <header class="section-header">
                <h2><i class="fas fa-box"></i> Mis Productos</h2>
                <button class="btn btn-sm btn-primary" onclick="openNuevoProducto()"><i class="fas fa-plus"></i> Nuevo Producto</button>
            </header>
            <div class="section-content">
                <div class="tabs">
                    <div class="tab active" data-tab="activos">Activos (12)</div>
                    <div class="tab" data-tab="pendientes">Pendientes (2)</div>
                    <div class="tab" data-tab="rechazados">Rechazados (1)</div>
                    <div class="tab" data-tab="borradores">Borradores (6)</div>
                </div>

                <!-- Tabla Activos -->
                <div class="tab-content active" id="activos">
                    <div class="table-responsive">
                        <table>
                            <thead>
                            <tr>
                                <th>Producto</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Manzana Gala</td>
                                <td>Q 5.50</td>
                                <td>120</td>
                                <td><span class="badge badge-success">Aprobado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="openEditarProducto({nombre:'Manzana Gala',descripcion:'Fruta fresca',precio:'5.50',stock:'120',categoria_id:'1',update_url:'#'})">Editar</button>
                                    <button class="btn btn-sm btn-danger">Desactivar</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Leche Entera 1L</td>
                                <td>Q 9.25</td>
                                <td>54</td>
                                <td><span class="badge badge-success">Aprobado</span></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="openEditarProducto({nombre:'Leche Entera 1L',descripcion:'Marca Atlántia',precio:'9.25',stock:'54',categoria_id:'3',update_url:'#'})">Editar</button>
                                    <button class="btn btn-sm btn-danger">Desactivar</button>
                                </td>
                            </tr>
                            <!-- ...más filas -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla Pendientes -->
                <div class="tab-content" id="pendientes">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Producto</th><th>Enviado</th><th>Estado</th><th>Acciones</th></tr></thead>
                            <tbody>
                            <tr>
                                <td>Cereal Integral 500g</td>
                                <td>12/08/2025</td>
                                <td><span class="badge badge-warning">En Revisión</span></td>
                                <td><button class="btn btn-sm btn-secondary" onclick="openEditarProducto({nombre:'Cereal Integral 500g',precio:'18.90',stock:'30',categoria_id:'2',update_url:'#'})">Editar</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla Rechazados -->
                <div class="tab-content" id="rechazados">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Producto</th><th>Motivo</th><th>Acciones</th></tr></thead>
                            <tbody>
                            <tr>
                                <td>Yogurt Natural 200g</td>
                                <td>Etiqueta incompleta</td>
                                <td><button class="btn btn-sm btn-secondary" onclick="openEditarProducto({nombre:'Yogurt Natural 200g',precio:'4.50',stock:'0',categoria_id:'3',update_url:'#'})">Corregir</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla Borradores -->
                <div class="tab-content" id="borradores">
                    <div class="table-responsive">
                        <table>
                            <thead><tr><th>Producto</th><th>Última edición</th><th>Acciones</th></tr></thead>
                            <tbody>
                            <tr>
                                <td>Pan Integral 600g</td>
                                <td>03/08/2025</td>
                                <td><button class="btn btn-sm btn-secondary" onclick="openEditarProducto({nombre:'Pan Integral 600g',precio:'12.00',stock:'25',categoria_id:'4',update_url:'#'})">Continuar</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

        <!-- Solicitudes de Surtido -->
        <section id="surtido" class="dashboard-section">
            <header class="section-header">
                <h2><i class="fas fa-shopping-cart"></i> Solicitudes de Surtido</h2>
            </header>
            <div class="section-content">
                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th>Pedido #</th><th>Cliente</th><th>Producto</th><th>Cant.</th>
                            <th>Unitario</th><th>Total</th><th>Fecha</th><th>Estado</th><th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>#1045</td>
                            <td>Ana López</td>
                            <td>Manzana Gala</td>
                            <td>6</td>
                            <td>Q 5.50</td>
                            <td>Q 33.00</td>
                            <td>10/08/2025</td>
                            <td><span class="badge badge-info">Pendiente</span></td>
                            <td style="display:flex;gap:.25rem;flex-wrap:wrap;">
                                <button class="btn btn-sm btn-success">Aceptar</button>
                                <button class="btn btn-sm btn-secondary">Preparando</button>
                                <button class="btn btn-sm btn-warning">Listo</button>
                                <button class="btn btn-sm btn-primary">Entregado</button>
                                <button class="btn btn-sm btn-danger">Rechazar</button>
                            </td>
                        </tr>
                        <!-- ...más filas -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Mensajería -->
        <section id="mensajeria" class="dashboard-section">
            <header class="section-header">
                <h2><i class="fas fa-envelope"></i> Mensajería</h2>
            </header>
            <div class="section-content">
                <p>Pendiente de integrar (endpoint admin ↔ vendedor).</p>
            </div>
        </section>
    </main>
</div>

<!-- Modal Nuevo/Editar Producto (dialog nativo) -->
<dialog id="modal-producto" style="max-width:680px;width:100%;border:none;border-radius:12px;padding:0;">
    <form id="form-producto" method="POST" action="#" style="padding:1.25rem;">
        <input type="hidden" name="_method" id="method-field" value="POST">
        <h3 style="margin-bottom:1rem;" id="titulo-modal">Nuevo Producto</h3>

        <div class="form-group" style="margin-bottom:1rem;">
            <label>Nombre</label>
            <input class="form-control" name="nombre" id="f-nombre" required style="width:100%;padding:.7rem;border:1px solid #ddd;border-radius:8px;">
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label>Descripción (opcional)</label>
            <textarea class="form-control" name="descripcion" id="f-descripcion" rows="3" style="width:100%;padding:.7rem;border:1px solid #ddd;border-radius:8px;"></textarea>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label>Precio (Q)</label>
            <input class="form-control" name="precio" id="f-precio" type="number" min="0" step="0.01" required style="width:100%;padding:.7rem;border:1px solid #ddd;border-radius:8px;">
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label>Stock</label>
            <input class="form-control" name="stock" id="f-stock" type="number" min="0" required style="width:100%;padding:.7rem;border:1px solid #ddd;border-radius:8px;">
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label>Categoría</label>
            <input class="form-control" name="categoria_id" id="f-categoria" type="number" min="1" required placeholder="ID de categoría" style="width:100%;padding:.7rem;border:1px solid #ddd;border-radius:8px;">
        </div>

        <div style="display:flex;gap:.5rem;justify-content:flex-end;">
            <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
            <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
    </form>
</dialog>

<!-- ===================== JS (tabs, sidebar y modal) ===================== -->
<script>
    // Sidebar móvil
    (function(){
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('overlay');
        var btn = document.getElementById('btnToggleSidebar');

        function openSidebar(){
            if(sidebar) sidebar.classList.add('open');
            if(overlay){ overlay.classList.add('show'); overlay.setAttribute('aria-hidden','false'); }
        }
        function closeSidebar(){
            if(sidebar) sidebar.classList.remove('open');
            if(overlay){ overlay.classList.remove('show'); overlay.setAttribute('aria-hidden','true'); }
        }
        if(btn){ btn.addEventListener('click', openSidebar); }
        if(overlay){ overlay.addEventListener('click', closeSidebar); }
        window.addEventListener('hashchange', closeSidebar);
    })();

    // Tabs
    (function () {
        var tabs = document.querySelectorAll('.tab');
        var contents = document.querySelectorAll('.tab-content');

        function showTab(id) {
            for (var i=0;i<contents.length;i++) contents[i].classList.remove('active');
            var el = document.getElementById(id);
            if (el) el.classList.add('active');
        }
        for (var t=0;t<tabs.length;t++) {
            tabs[t].addEventListener('click', function () {
                for (var j=0;j<tabs.length;j++) tabs[j].classList.remove('active');
                this.classList.add('active');
                showTab(this.getAttribute('data-tab'));
            });
        }
    })();

    // Modal (dialog)
    var modal = document.getElementById('modal-producto');
    var form  = document.getElementById('form-producto');
    var methodField = document.getElementById('method-field');

    function openNuevoProducto(){
        if (form) form.reset();
        if (form) form.action = "#";
        if (methodField) methodField.value = "POST";
        var titulo = document.getElementById('titulo-modal');
        if (titulo) titulo.innerText = 'Nuevo Producto';
        if (modal && modal.showModal) modal.showModal();
    }

    function openEditarProducto(prod){
        if (form) form.reset();
        if (form) form.action = (prod && prod.update_url) ? prod.update_url : "#";
        if (methodField) methodField.value = "PUT";

        var titulo = document.getElementById('titulo-modal'); if(titulo) titulo.innerText = 'Editar Producto';
        var fNombre = document.getElementById('f-nombre'); if(fNombre) fNombre.value = (prod && prod.nombre) ? prod.nombre : '';
        var fDesc   = document.getElementById('f-descripcion'); if(fDesc) fDesc.value = (prod && prod.descripcion) ? prod.descripcion : '';
        var fPrecio = document.getElementById('f-precio'); if(fPrecio) fPrecio.value = (prod && prod.precio) ? prod.precio : '';
        var fStock  = document.getElementById('f-stock'); if(fStock) fStock.value = (prod && prod.stock) ? prod.stock : '';
        var fCat    = document.getElementById('f-categoria'); if(fCat) fCat.value = (prod && prod.categoria_id) ? prod.categoria_id : '';

        if (modal && modal.showModal) modal.showModal();
    }

    function closeModal(){ if (modal && modal.close) modal.close(); }

    // Actualización simple del perfil visual
    (function(){
        var moodSelect = document.getElementById('estado-animo');
        var moodText = document.getElementById('mood-text');
        var bioField = document.getElementById('descripcion-vendedor');
        var bioText = document.getElementById('bio-text');
        var nameField = document.getElementById('nombre-vendedor');
        var welcomeTitle = document.querySelector('.welcome-message h1');
        var avatarInput = document.getElementById('avatar-input');
        var avatarImage = document.getElementById('avatar-image');

        if(moodSelect && moodText){
            moodSelect.addEventListener('change', function(){
                moodText.textContent = this.value;
            });
        }
        if(bioField && bioText){
            bioField.addEventListener('input', function(){
                bioText.textContent = this.value;
            });
        }
        if(nameField && welcomeTitle){
            nameField.addEventListener('input', function(){
                welcomeTitle.textContent = this.value ? 'Bienvenido, ' + this.value.split(' ')[0] : 'Bienvenido';
            });
        }
        if(avatarInput && avatarImage){
            avatarInput.addEventListener('change', function(){
                if(this.files && this.files[0]){
                    var reader = new FileReader();
                    reader.onload = function(e){
                        avatarImage.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    })();
</script>
</body>
</html>
