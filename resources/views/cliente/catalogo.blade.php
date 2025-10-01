<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Supermercado Atlantia</title>
    <link rel="icon" href="{{ asset('storage/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root{
            --vino:#800020;--vino-oscuro:#4d0014;--vino-claro:#f9e5eb;
            --blanco:#fff;--gris:#f4f4f4;--gris-texto:#6b7280;
        }
        body{font-family:'Segoe UI',Arial,sans-serif;background:var(--gris);color:#222;margin:0;min-height:100vh;display:flex;flex-direction:column}

        /* HEADER Y NAVEGACIÓN */
        .navbar{background:var(--blanco);box-shadow:0 2px 8px #0002;display:flex;align-items:center;justify-content:space-between;padding:.7rem 2rem;position:sticky;top:0;z-index:99;flex-wrap:wrap}

        /* Contenedor del logo - SOLO PARA MÓVIL */
        .navbar .logo{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .navbar .logo img{height:65px;width:auto;object-fit:contain;background:transparent}

        /* Menú hamburguesa - OCULTO POR DEFECTO (solo visible en móviles) */
        .menu-toggle{
            display:none; /* Oculto en desktop */
            flex-direction:column;
            justify-content:space-around;
            width:30px;
            height:25px;
            background:transparent;
            border:none;
            cursor:pointer;
            padding:0;
            z-index:10;
        }
        .menu-toggle span{width:100%;height:3px;background:var(--vino-oscuro);border-radius:5px;transition:all 0.3s}

        /* Menú principal - VISIBLE EN DESKTOP */
        .navbar nav{display:flex;align-items:center;gap:1rem;overflow-x:auto;white-space:nowrap;scrollbar-width:none}
        .navbar nav::-webkit-scrollbar{display:none}
        .navbar nav a{text-decoration:none;color:var(--vino-oscuro);font-weight:500;padding:.4rem 1rem;border-radius:8px;transition:.2s}
        .navbar nav a:hover,.navbar nav .activo{background:var(--vino-claro);color:var(--vino)}

        /* Botones especiales */
        .estado-btn{background:var(--vino);color:#fff!important;border-radius:8px;padding:.45rem 1rem;font-weight:700;white-space:nowrap}
        .estado-btn:hover{background:var(--vino-oscuro)}
        .carrito{position:relative;display:inline-block;padding:0 5px}
        .carrito-icon-wrapper{position:relative;display:inline-block;font-size:1.3rem;color:var(--vino-oscuro)}
        .carrito-count{position:absolute;top:-8px;right:-12px;background:var(--vino);color:#fff;font-size:.75rem;min-width:18px;height:18px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:bold;border:2px solid #fff;box-sizing:border-box;padding:0 2px;line-height:1}
        .login-btn{background:var(--vino);color:#fff;border:none;border-radius:8px;padding:.45rem 1.3rem;font-weight:bold;font-size:1rem;cursor:pointer;transition:.2s;white-space:nowrap}
        .login-btn:hover{background:var(--vino-oscuro)}

        /* BANNER - CORREGIDO: sin fondo de color no deseado */
        .banner-ofertas{
            position:relative;
            width:100%;
            overflow:hidden;
            margin:0 auto;
            display:flex;
            align-items:center;
            justify-content: center;
            background-color: transparent;
            max-height: 180px;
        }
        .banner-ofertas img{
            width:100%;
            height:auto;
            display:block;
            object-fit: cover;
        }

        /* BÚSQUEDA */
        .busqueda-section{background:var(--vino-claro);padding:2rem 1rem;text-align:center}
        .busqueda-section h1{color:var(--vino);margin-bottom:1rem}
        .busqueda-form{max-width:550px;margin:auto;display:flex;overflow:hidden;border-radius:12px;box-shadow:0 2px 12px #80002011}
        .busqueda-form input{flex:1;border:none;padding:1rem;font-size:1rem;outline:none}
        .busqueda-form button{background:var(--vino);color:#fff;border:none;padding:0 1.5rem;font-weight:bold;font-size:1.1rem;cursor:pointer}
        .busqueda-form button:hover{background:var(--vino-oscuro)}

        /* CATÁLOGO */
        .catalogo-section{max-width:1400px;margin:2rem auto;padding:0 1.5rem}
        .catalogo-section h2{color:var(--vino);text-align:center;margin-bottom:2rem}

        /* Grilla responsiva de productos */
        .productos-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1.5rem}

        /* Producto individual */
        .producto-card{background:var(--blanco);border-radius:12px;box-shadow:0 1px 10px #0001;display:flex;flex-direction:column;overflow:hidden;border:1px solid var(--vino-claro);transition:transform .2s,box-shadow .2s;font-size:.92rem}
        .producto-card:hover{transform:translateY(-4px);box-shadow:0 6px 20px #80002022;border-color:var(--vino)}
        .producto-img{background:var(--vino-claro);display:flex;align-items:center;justify-content:center;height:180px;padding:1rem}
        .producto-img img{max-width:100%;max-height:100%;object-fit:contain;transition:transform .3s}
        .producto-card:hover .producto-img img{transform:scale(1.05)}
        .producto-info{padding:1rem 1.25rem 1.1rem;display:flex;flex-direction:column;flex-grow:1}
        .producto-info h3{margin:0 0 8px;font-size:1rem;font-weight:800;color:var(--vino-oscuro);line-height:1.3}
        .producto-categoria{font-size:.82rem;font-weight:700;color:var(--vino);margin-bottom:8px}
        .producto-info p{font-size:.88rem;color:var(--gris-texto);flex-grow:1;margin-bottom:12px;line-height:1.4}

        /* Formulario de agregar producto */
        .producto-add-form{display:flex;flex-direction:column;gap:.6rem;margin-top:auto}
        .price-qty{display:flex;justify-content:space-between;align-items:center;gap:.75rem}
        .producto-precio{font-size:1.1rem;color:var(--vino);font-weight:900}
        .producto-add-form input[type="number"]{
            width:72px;font-size:.95rem;padding:8px 10px;border:1px solid var(--vino-claro);
            border-radius:8px;text-align:center;background:#fff
        }
        .producto-add-form input[type="number"]:focus{outline:none;border-color:var(--vino);box-shadow:0 0 0 3px rgba(128,0,32,.12)}
        .producto-add-form button{
            background:var(--vino);color:#fff;border:none;border-radius:10px;padding:10px 14px;
            font-size:.95rem;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
            width:100%;box-shadow:0 6px 16px rgba(128,0,32,.18);transition:transform .15s,background .15s
        }
        .producto-add-form button:hover{background:var(--vino-oscuro);transform:translateY(-1px)}

        /* FOOTER */
        footer{background:var(--vino);color:#fff;text-align:center;font-size:1rem;padding:1.5rem 0;margin-top:3rem}

        /* Carrusel categorías */
        .categorias-carousel-container{position:relative;display:flex;align-items:center;justify-content:center;background:#fff;padding:14px 0;box-shadow:0 2px 8px #0000000d;overflow:hidden}
        .categorias-carousel{display:flex;gap:10px;overflow-x:hidden;scroll-behavior:smooth;padding:0 40px;max-width:100%}
        .categoria-btn{white-space:nowrap;background:var(--vino-claro);color:var(--vino-oscuro);padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:.9rem;transition:.3s;flex-shrink:0}
        .categoria-btn:hover,.categoria-btn.activo{background:var(--vino);color:#fff}
        .carousel-btn{position:absolute;top:50%;transform:translateY(-50%);background:var(--vino);color:#fff;border:none;padding:10px;border-radius:50%;cursor:pointer;z-index:10;font-size:1.1rem;box-shadow:0 2px 6px #0001}
        .carousel-btn.left{left:10px}.carousel-btn.right{right:10px}

        /* ============================
           MODAL DE AUTENTICACIÓN
           ============================ */
        .auth-modal-backdrop{
            position:fixed; inset:0; background:rgba(0,0,0,.45);
            display:none; align-items:center; justify-content:center; z-index:999;
        }
        .auth-modal-backdrop.show{ display:flex; }
        .auth-modal{
            position:relative; background:#fff; width:min(520px,92vw);
            border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.2);
            padding:1.25rem; text-align:center;
        }
        .auth-modal img{ height:56px; margin-bottom:.25rem; }
        .auth-modal h3{ margin:.25rem 0 .25rem; color:var(--vino-oscuro); font-size:1.3rem; }
        .auth-modal p{ color:var(--gris-texto); margin:.25rem 0 1rem; }
        .auth-actions{ display:flex; gap:.75rem; flex-wrap:wrap; justify-content:center; }
        .auth-btn{ border:none; border-radius:10px; padding:.7rem 1rem; font-weight:800; cursor:pointer; }
        .auth-btn.primary{ background:var(--vino); color:#fff; }
        .auth-btn.primary:hover{ background:var(--vino-oscuro); }
        .auth-btn.secondary{ background:var(--vino-claro); color:var(--vino-oscuro); }
        .auth-btn.ghost{ background:transparent; color:var(--gris-texto); }
        .auth-close{ position:absolute; top:10px; right:12px; background:transparent; border:0; font-size:1.3rem; color:#777; cursor:pointer; }

        /* ============================
           MEDIA QUERIES RESPONSIVAS
           ============================ */
        @media (max-width:1199px){
            .productos-grid{grid-template-columns:repeat(4,1fr);gap:1.2rem}
            .navbar{padding:.7rem 1.5rem}
            .banner-ofertas{max-height: 160px;}
        }
        @media (max-width:991px){
            .productos-grid{grid-template-columns:repeat(3,1fr);gap:1.2rem}
            .producto-img{height:160px}
            .busqueda-form{flex-direction:row}
            .navbar nav{gap:.8rem}
            .navbar nav a{padding:.3rem .8rem;font-size:.9rem}
            .login-btn{padding:.4rem 1rem}
            .banner-ofertas{max-height: 140px;}
        }
        @media (max-width:767px){
            .productos-grid{grid-template-columns:repeat(2,1fr);gap:0.8rem}
            .catalogo-section{padding:0 0.8rem; margin:1.5rem auto}
            .busqueda-section{padding:1.5rem 0.8rem}
            .busqueda-section h1{font-size:1.5rem}
            .banner-ofertas{max-height: 120px;}
            .producto-card{font-size:.85rem; margin-bottom:0}
            .producto-img{height:130px; padding:0.8rem}
            .producto-info{padding:0.8rem}
            .producto-info h3{font-size:0.9rem; margin:0 0 6px}
            .producto-categoria{font-size:.78rem; margin-bottom:6px}
            .producto-info p{font-size:.8rem; margin-bottom:10px; line-height:1.3}
            .producto-precio{font-size:1rem}
            .producto-add-form input[type="number"]{width:60px; padding:6px 8px; font-size:.9rem}
            .producto-add-form button{padding:8px 10px; font-size:.9rem}

            .navbar{
                position:relative;
                padding:.7rem 1rem;
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar .logo{
                display:flex;
                align-items:center;
                justify-content: space-between;
                width: 100%;
                margin-bottom: 0;
            }
            .menu-toggle{
                display:flex;
                margin-left: 15px;
            }
            .navbar nav{
                display:none;
                flex-direction:column;
                position:absolute;
                top:100%;
                left:0;
                right:0;
                background:var(--blanco);
                padding:1.5rem 1rem;
                box-shadow:0 5px 15px rgba(0,0,0,.1);
                gap:.8rem;
                align-items:center;
                z-index:100;
                text-align: center;
            }
            .navbar nav.active{display:flex}
            .navbar nav a{
                width:90%;
                padding:.8rem 1rem;
                border-radius:6px;
                text-align: center;
            }
            .navbar nav .login-wrapper,
            .navbar nav .carrito,
            .navbar nav .estado-btn {
                display: flex;
                justify-content: center;
                width: 90%;
            }
            .carousel-btn{padding:8px;font-size:1rem}
            .categoria-btn{font-size:.85rem;padding:8px 14px}
            .categorias-carousel{padding:0 35px}
        }
        @media (max-width:575px){
            .productos-grid{grid-template-columns:repeat(2,1fr);gap:0.7rem}
            .busqueda-form{flex-direction:column;border-radius:8px}
            .busqueda-form input,.busqueda-form button{width:100%;border-radius:0;padding:.8rem}
            .busqueda-form button{padding:.8rem}
            .banner-ofertas{max-height: 100px;}
            .busqueda-section h1{font-size:1.3rem;margin-bottom:.8rem}
            .catalogo-section h2{font-size:1.2rem}
            .producto-img{height:120px}
            .producto-info{padding:0.7rem}
            .producto-info h3{font-size:0.85rem}
            .producto-info p{font-size:.78rem}
            .producto-add-form input[type="number"]{width:55px; padding:5px 7px}
            .menu-toggle{margin-left: 15px;}
            .categorias-carousel-container{padding:10px 0}
            .carousel-btn{padding:6px;font-size:.9rem}
            .categoria-btn{font-size:.8rem;padding:6px 10px}
            .categorias-carousel{padding:0 30px;gap:6px}
            footer{padding:1rem 0;font-size:.9rem}
        }
        @media (max-width:480px){
            .productos-grid{grid-template-columns:repeat(2,1fr);gap:0.6rem}
            .banner-ofertas{max-height: 90px;}
            .producto-img{height:110px; padding:0.6rem}
            .producto-info{padding:0.6rem}
            .producto-info h3{font-size:0.82rem}
            .producto-info p{font-size:.76rem; margin-bottom:8px}
            .producto-precio{font-size:0.95rem}
            .producto-add-form input[type="number"]{width:50px; padding:4px 6px; font-size:.85rem}
            .producto-add-form button{padding:7px 9px; font-size:.85rem}
            .categoria-btn{font-size:.75rem;padding:5px 8px}
            .menu-toggle{margin-left: 10px;}
            .navbar nav a{width:85%;}
        }
        @media (max-width:360px){
            .navbar .logo img{height:50px}
            .busqueda-section{padding:1rem .5rem}
            .busqueda-section h1{font-size:1.2rem}
            .banner-ofertas{max-height: 80px;}
            .productos-grid{gap:0.5rem}
            .producto-img{height:100px}
            .producto-info h3{font-size:0.8rem}
            .producto-info p{font-size:.74rem}
            .producto-add-form input[type="number"]{width:45px}
            .categoria-btn{font-size:.7rem;padding:4px 7px}
            .menu-toggle{width:25px;height:20px;margin-left: 8px;}
            .navbar nav{padding:1rem 0.5rem;}
            .navbar nav a{width:80%;padding:.6rem 0.8rem;font-size:0.9rem;}
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- HEADER -->
<div class="navbar">
    <div class="logo">
        <img src="{{ asset('img/Atlantia.png') }}" alt="Logo">
        <!-- Menú hamburguesa solo visible en móviles -->
        <button class="menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <nav id="mainNav">
        <a href="{{ route('cliente.productos') }}" class="activo">Inicio</a>
        <a href="{{ route('cliente.categorias') }}">Categorías</a>
        <a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.index') ? 'activo' : '' }}">
            Contacto
        </a>

        @auth
            @if(!empty($pedidoActivo))
                <a href="{{ route('cliente.estado.pedido', $pedidoActivo) }}" class="estado-btn" title="Ver estado del pedido">
                    Ver estado del pedido
                </a>
            @endif
        @endauth

        <a href="{{ route('carrito.ver') }}" class="carrito" title="Ver carrito">
            <div class="carrito-icon-wrapper">
                <i class="fa fa-shopping-cart"></i>
                <span class="carrito-count">{{ session('carrito') ? count(session('carrito')) : 0 }}</span>
            </div>
        </a>

        @auth
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <span style="font-weight:bold;color:var(--vino);">Hola, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="login-btn">Cerrar sesión</button>
                </form>
            </div>
        @else
            <div class="login-wrapper">
                <a href="{{ route('login') }}" class="login-btn">Iniciar sesión</a>
            </div>
        @endauth
    </nav>
</div>

<!-- BANNER -->
<div class="banner-ofertas">
    <img src="{{ asset('storage/baner.png') }}" alt="Ofertas especiales" onerror="this.style.display='none'">
</div>

<!-- BÚSQUEDA -->
<section class="busqueda-section">
    <h1>¡Encuentra todo para tu hogar!</h1>
    <form class="busqueda-form" method="GET" action="{{ route('cliente.productos') }}">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar en todo el supermercado...">
        <button type="submit"><i class="fa fa-search"></i> Buscar</button>
    </form>
</section>

<!-- CATEGORÍAS EN CARRUSEL -->
<div class="categorias-carousel-container">
    <button class="carousel-btn left" onclick="scrollCategorias('left')"><i class="fas fa-chevron-left"></i></button>
    <div class="categorias-carousel" id="categoriasCarousel">
        <a href="{{ route('cliente.productos') }}" class="categoria-btn {{ request('categoria') ? '' : 'activo' }}">Todas las categorías</a>
        @foreach ($categorias as $categoria)
            <a href="{{ route('cliente.productos', ['categoria' => $categoria->id]) }}" class="categoria-btn {{ request('categoria') == $categoria->id ? 'activo' : '' }}">
                {{ $categoria->nombre }}
            </a>
        @endforeach
    </div>
    <button class="carousel-btn right" onclick="scrollCategorias('right')"><i class="fas fa-chevron-right"></i></button>
</div>

<!-- CATÁLOGO -->
<section class="catalogo-section" id="catalogo">
    <h2>Catálogo de productos</h2>
    <div class="productos-grid">
        @forelse ($productos as $producto)
            <div class="producto-card">
                <div class="producto-img">
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}">
                </div>
                <div class="producto-info">
                    <h3>{{ $producto->nombre }}</h3>
                    <div class="producto-categoria">{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</div>
                    <p>{{ $producto->descripcion }}</p>

                    {{-- Footer de la tarjeta: precio + cantidad arriba, botón ABAJO --}}
                    <form class="producto-add-form" method="POST" action="{{ route('carrito.agregar') }}">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                        <div class="price-qty">
                            <span class="producto-precio">Q{{ number_format($producto->precio, 2) }}</span>
                            <input type="number" name="cantidad" min="1" value="1">
                        </div>

                        <button type="submit"><i class="fa fa-cart-plus"></i> Agregar</button>
                    </form>
                </div>
            </div>
        @empty
            <p style="grid-column: 1 / -1; text-align: center; padding: 2rem;">No hay productos para mostrar.</p>
        @endforelse
    </div>
</section>

<!-- FOOTER -->
<footer>
    <b>Supermercado Atlantia</b> &copy; 2025. Todos los derechos reservados.
</footer>

<!-- MODAL: Requiere autenticación -->
<div id="authModal" class="auth-modal-backdrop" aria-hidden="true">
    <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="authTitle">
        <button class="auth-close" aria-label="Cerrar">&times;</button>
        <img src="{{ asset('img/Atlantia.png') }}" alt="">
        <h3 id="authTitle">Para agregar productos al carrito</h3>
        <p>Por favor, inicia sesión. Si no tienes una cuenta, regístrate en un minuto.</p>
        <div class="auth-actions">
            <a class="auth-btn secondary" href="{{ route('register') }}">Registrarme</a>
            <a class="auth-btn primary" href="{{ route('login') }}">Iniciar sesión</a>
            <button type="button" class="auth-btn ghost" data-close>Cancelar</button>
        </div>
    </div>
</div>

<script>
    function scrollCategorias(direction){
        const container=document.getElementById('categoriasCarousel');
        const scrollAmount=220;
        container.scrollBy({left:direction==='left'?-scrollAmount:scrollAmount,behavior:'smooth'});
    }

    // Toggle del menú hamburguesa
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.getElementById('mainNav').classList.toggle('active');
    });

    // Cerrar menú al hacer clic fuera de él
    document.addEventListener('click', function(event) {
        const nav = document.getElementById('mainNav');
        const toggle = document.querySelector('.menu-toggle');
        if (!nav.contains(event.target) && !toggle.contains(event.target) && nav.classList.contains('active')) {
            nav.classList.remove('active');
        }
    });
</script>

<!-- JS del modal / interceptar envíos de invitados -->
<script>
    // ¿Hay usuario autenticado?
    const IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};

    // Modal helpers
    const authModal = document.getElementById('authModal');
    const openAuthModal = () => {
        authModal.classList.add('show');
        authModal.setAttribute('aria-hidden','false');
        document.body.style.overflow = 'hidden';
    };
    const closeAuthModal = () => {
        authModal.classList.remove('show');
        authModal.setAttribute('aria-hidden','true');
        document.body.style.overflow = '';
    };

    // Cerrar modal (fondo, botón X, botón Cancelar, ESC)
    authModal.addEventListener('click', (e) => {
        if (e.target.id === 'authModal' || e.target.closest('[data-close]') || e.target.classList.contains('auth-close')) {
            closeAuthModal();
        }
    });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAuthModal(); });

    // Interceptar todos los formularios "Agregar" si NO hay sesión
    document.querySelectorAll('.producto-add-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!IS_AUTH) {
                e.preventDefault();
                openAuthModal();
            }
        });
    });

    // Opcional: también mostrar modal si un invitado abre el carrito
    document.querySelectorAll('.carrito').forEach(link => {
        link.addEventListener('click', (e) => {
            if (!IS_AUTH) {
                e.preventDefault();
                openAuthModal();
            }
        });
    });
</script>

</body>
</html>
