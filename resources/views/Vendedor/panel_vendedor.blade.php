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
            --primary: #2c3e50;
            --primary-hover: #1a252f;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --sidebar-width: 260px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f8f9fa; color: #333; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }

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
            background: #fff; padding: 1rem; border-radius: 12px;
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
        .stat-card { background: #fff; border-radius: 12px; padding: 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
        .stat-icon { font-size: 1.7rem; margin-bottom: .6rem; color: var(--secondary); }
        .stat-number { font-size: 1.6rem; font-weight: 700; color: var(--dark); line-height: 1; }
        .stat-title { margin-top: .35rem; color: var(--gray); font-size: .9rem; }

        /* =========================
           Secciones
        ========================== */
        .dashboard-section { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,.07); margin-bottom: 1rem; overflow: hidden; }
        .section-header { display: flex; justify-content: space-between; align-items: center; gap: .75rem; padding: 1rem 1.25rem; background: var(--light); border-bottom: 1px solid #ddd; }
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
        .btn { display: inline-flex; align-items: center; gap: .5rem; padding: .55rem 1rem; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; cursor: pointer; }
        .btn-sm { padding: .35rem .7rem; font-size: .85rem; border-radius: 7px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-warning { background: var(--warning); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-secondary { background: var(--secondary); color: #fff; }
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
        }
        @media (max-width: 576px) {
            .stats-cards { grid-template-columns: 1fr; }
            .top-bar { flex-direction: column; align-items: stretch; }
            .section-header { flex-direction: column; align-items: flex-start; }
            .tab { flex: 1; min-width: 140px; text-align: center; }
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
</script>
</body>
</html>
