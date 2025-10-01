<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías - Supermercado Atlantia</title>
    <link rel="icon" href="{{ asset('storage/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root{
            --vino:#800020;--vino-oscuro:#4d0014;--vino-claro:#f9e5eb;
            --blanco:#fff;--gris:#f4f4f4;--gris-texto:#6b7280;
            --dorado: #d4af37; --dorado-oscuro: #b8911d;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body{
            font-family:'Segoe UI',Arial,sans-serif;
            background:var(--gris);
            color:#222;
            margin:0;
            min-height:100vh;
            display:flex;
            flex-direction:column;
            line-height: 1.6;
        }

        /* HEADER Y NAVEGACIÓN */
        .navbar{
            background:var(--blanco);
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0.7rem 1.5rem;
            position:sticky;
            top:0;
            z-index:99;
            flex-wrap:wrap;
        }

        /* Logo */
        .navbar .logo{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .navbar .logo img{
            height:55px;
            width:auto;
            object-fit:contain;
            background:transparent;
        }

        /* Menú hamburguesa - OCULTO POR DEFECTO */
        .menu-toggle{
            display:none;
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
        .menu-toggle span{
            width:100%;
            height:3px;
            background:var(--vino-oscuro);
            border-radius:5px;
            transition:all 0.3s;
        }

        /* Menú principal - VISIBLE EN DESKTOP */
        .navbar nav{
            display:flex;
            align-items:center;
            gap:1rem;
            overflow-x:auto;
            white-space:nowrap;
            scrollbar-width:none;
        }
        .navbar nav::-webkit-scrollbar{display:none}
        .navbar nav a{
            text-decoration:none;
            color:var(--vino-oscuro);
            font-weight:500;
            padding:0.4rem 1rem;
            border-radius:8px;
            transition:0.2s;
        }
        .navbar nav a:hover,.navbar nav .activo{
            background:var(--vino-claro);
            color:var(--vino);
        }

        /* Botones especiales */
        .carrito{
            position:relative;
            display:inline-block;
            padding:0 5px;
        }
        .carrito-icon-wrapper{
            position:relative;
            display:inline-block;
            font-size:1.3rem;
            color:var(--vino-oscuro);
        }
        .carrito-count{
            position:absolute;
            top:-8px;
            right:-12px;
            background:var(--vino);
            color:#fff;
            font-size:0.75rem;
            min-width:18px;
            height:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:50%;
            font-weight:bold;
            border:2px solid #fff;
            box-sizing:border-box;
            padding:0 2px;
            line-height:1;
        }
        .login-btn{
            background:var(--vino);
            color:#fff;
            border:none;
            border-radius:8px;
            padding:0.45rem 1.3rem;
            font-weight:bold;
            font-size:1rem;
            cursor:pointer;
            transition:0.2s;
            white-space:nowrap;
        }
        .login-btn:hover{
            background:var(--vino-oscuro);
        }

        /* CONTENIDO PRINCIPAL */
        .container{
            max-width:1200px;
            margin:2rem auto;
            padding:0 1.5rem;
            width:100%;
            flex: 1;
        }

        /* Botón de regresar */
        .btn-regresar {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--vino-claro);
            color: var(--vino);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
            border: 1px solid var(--vino-claro);
        }

        .btn-regresar:hover {
            background: var(--vino);
            color: white;
        }

        /* Título con líneas */
        .titulo-linea {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            width: 100%;
        }

        .titulo-linea .linea {
            flex: 1;
            height: 2px;
            background-color: var(--vino);
            border-radius: 5px;
            min-width: 30px;
        }

        .titulo-linea h2 {
            color: var(--vino);
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
            flex-shrink: 0;
        }

        /* Grid de categorías - CORREGIDO */
        .categoria-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            width: 100%;
            padding: 0 0.5rem;
        }

        .categoria-box {
            background: var(--vino-claro);
            border: 1px solid #ffd9e0;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 200px;
            width: 100%;
        }

        .categoria-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 16px rgba(128, 0, 32, 0.2);
        }

        .categoria-box h5 {
            margin: 1rem 0;
            font-size: 1.1rem;
            color: var(--vino);
            font-weight: 700;
            line-height: 1.3;
        }

        .icono {
            font-size: 2.5rem;
            color: var(--vino);
            margin-bottom: 0.5rem;
        }

        .btn-ver {
            margin-top: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(to bottom right, var(--dorado), var(--dorado-oscuro));
            color: #fff;
            padding: 0.5rem 1.2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
            border: none;
            width: auto;
            min-width: 140px;
        }

        .btn-ver:hover {
            transform: scale(1.04);
            background: linear-gradient(to bottom right, #e6c75a, #a4781c);
            box-shadow: 0 6px 16px rgba(182, 141, 32, 0.5);
        }

        /* FOOTER */
        footer{
            background:var(--vino);
            color:#fff;
            text-align:center;
            font-size:1rem;
            padding:1.5rem 0;
            margin-top:3rem;
            width: 100%;
        }

        /* ============================
           MEDIA QUERIES RESPONSIVAS
           ============================ */

        /* Tablets (768px - 991px) */
        @media (max-width:991px){
            .navbar{
                padding:0.7rem 1.2rem;
            }
            .navbar .logo img{
                height: 50px;
            }
            .navbar nav{
                gap:0.8rem;
            }
            .navbar nav a{
                padding:0.3rem 0.8rem;
                font-size:0.9rem;
            }
            .login-btn{
                padding:0.4rem 1rem;
            }

            .titulo-linea h2{
                font-size:1.6rem;
            }
            .categoria-grid{
                gap:1.2rem;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            }
            .categoria-box{
                min-height: 190px;
                padding: 1.2rem;
            }
        }

        /* Móviles Grandes/Tablets Pequeñas (576px - 767px) */
        @media (max-width:767px){
            /* Navbar para móviles */
            .navbar{
                position:relative;
                padding:0.7rem 1rem;
                flex-direction: column;
                align-items: flex-start;
            }

            /* Logo y hamburguesa en misma línea */
            .navbar .logo{
                display:flex;
                align-items:center;
                justify-content: space-between;
                width: 100%;
                margin-bottom: 0;
            }

            /* Mostrar menú hamburguesa solo en móviles */
            .menu-toggle{
                display:flex;
                margin-left: 15px;
            }

            /* Ocultar menú normal en móviles */
            .navbar nav{
                display:none;
                flex-direction:column;
                position:absolute;
                top:100%;
                left:0;
                right:0;
                background:var(--blanco);
                padding:1.2rem 1rem;
                box-shadow:0 5px 15px rgba(0,0,0,.1);
                gap:0.8rem;
                align-items:center;
                z-index:100;
                text-align: center;
            }
            .navbar nav.active{
                display:flex;
            }
            .navbar nav a{
                width:90%;
                padding:0.8rem 1rem;
                border-radius:6px;
                text-align: center;
            }

            /* Contenido */
            .container{
                margin:1.5rem auto;
                padding:0 1rem;
            }
            .titulo-linea{
                flex-direction: column;
                gap: 0.8rem;
                margin-bottom:2rem;
            }
            .titulo-linea .linea{
                width: 80%;
            }
            .titulo-linea h2{
                font-size:1.4rem;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }
            .categoria-grid{
                grid-template-columns:repeat(2,1fr);
                gap:1rem;
                padding: 0;
            }
            .categoria-box{
                padding:1.2rem 0.8rem;
                min-height: 180px;
            }
            .icono{
                font-size:2rem;
            }
            .categoria-box h5{
                font-size:1rem;
                margin: 0.8rem 0;
            }
            .btn-ver{
                min-width: 120px;
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }

            /* Botón de regresar */
            .btn-regresar {
                margin-bottom: 1.2rem;
                padding: 0.5rem 1rem;
            }
        }

        /* Móviles Medianos (481px - 575px) */
        @media (max-width:575px){
            .navbar .logo img{
                height:45px;
            }

            .titulo-linea{
                gap:0.7rem;
                margin-bottom:1.8rem;
            }
            .titulo-linea h2{
                font-size:1.3rem;
            }
            .titulo-linea .linea{
                width: 70%;
            }

            .categoria-grid{
                grid-template-columns:1fr;
                gap:1rem;
                max-width:400px;
                margin:0 auto;
            }
            .categoria-box{
                padding:1.2rem;
                min-height: auto;
            }

            .menu-toggle{
                margin-left: 12px;
            }

            /* Botón de regresar */
            .btn-regresar {
                margin-bottom: 1rem;
            }
        }

        /* Móviles Pequeños (≤480px) */
        @media (max-width:480px){
            .navbar .logo img{
                height:40px;
            }

            .titulo-linea h2{
                font-size:1.2rem;
                gap: 0.3rem;
            }
            .titulo-linea .linea{
                width: 60%;
            }

            .categoria-box{
                padding:1rem;
            }
            .icono{
                font-size:1.8rem;
            }
            .categoria-box h5{
                font-size:0.95rem;
                margin: 0.6rem 0;
            }
            .btn-ver{
                padding:0.4rem 0.8rem;
                font-size:0.9rem;
                min-width: 110px;
            }

            .menu-toggle{
                margin-left: 10px;
            }
            .navbar nav a{
                width:85%;
            }

            /* Botón de regresar */
            .btn-regresar {
                font-size: 0.9rem;
                padding: 0.4rem 0.8rem;
            }
        }

        /* Móviles muy pequeños (≤360px) */
        @media (max-width:360px){
            .navbar .logo img{
                height:35px;
            }

            .titulo-linea h2{
                font-size:1.1rem;
            }
            .categoria-box{
                padding:0.8rem;
            }
            .icono{
                font-size:1.6rem;
            }
            .categoria-box h5{
                font-size:0.9rem;
                margin: 0.5rem 0;
            }
            .btn-ver{
                padding:0.35rem 0.7rem;
                font-size:0.85rem;
                min-width: 100px;
            }

            .menu-toggle{
                width:25px;
                height:20px;
                margin-left: 8px;
            }
            .navbar nav{
                padding:1rem 0.5rem;
            }
            .navbar nav a{
                width:80%;
                padding:0.6rem 0.8rem;
                font-size:0.9rem;
            }

            /* Botón de regresar */
            .btn-regresar {
                font-size: 0.85rem;
                padding: 0.4rem 0.7rem;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- HEADER -->
<div class="navbar">
    <div class="logo">
        <img src="{{ asset('img/Atlantia.png') }}" alt="Supermercado Atlantia">
        <button class="menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <nav id="mainNav">
        <a href="{{ route('cliente.productos') }}">Inicio</a>
        <a href="{{ route('cliente.categorias') }}" class="activo">Categorías</a>
        <a href="#contacto">Contacto</a>

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
            <a href="{{ route('login') }}" class="login-btn">Iniciar sesión</a>
        @endauth
    </nav>
</div>

<!-- CONTENIDO PRINCIPAL -->
<section class="container">
    <!-- Botón de regresar -->
    <a href="{{ route('cliente.productos') }}" class="btn-regresar">
        <i class="fas fa-arrow-left"></i> Volver al Inicio
    </a>

    <div class="titulo-linea">
        <span class="linea"></span>
        <h2><i class="fas fa-th-large me-2"></i> Categorías del supermercado</h2>
        <span class="linea"></span>
    </div>

    <div class="categoria-grid">
        @foreach ($categorias as $categoria)
            @php
                $iconos = [
                    'Lácteos y Derivados' => 'fa-cheese',
                    'Abarrotes y Despensa' => 'fa-box',
                    'Snacks y Golosinas' => 'fa-candy-cane',
                    'Enlatados y Conservas' => 'fa-box',
                    'Bebés y Maternidad' => 'fa-baby',
                    'Limpieza del hogar' => 'fa-broom',
                    'Higiene Personal' => 'fa-soap',
                    'Bebidas' => 'fa-wine-glass-alt',
                    'Carnes y Embutidos' => 'fa-drumstick-bite',
                    'Frutas y verduras' => 'fa-apple-alt',
                    'Panadería y Pastelería' => 'fa-bread-slice',
                    'Desechables y Empaques' => 'fa-box-open',
                    'Fiestas y Celebraciones' => 'fa-gift',
                    'Misceláneos y Hogar' => 'fa-toolbox',
                    'Medicamentos y Cuidado Básico' => 'fa-capsules',
                ];
                $icono = $iconos[$categoria->nombre] ?? 'fa-tag';
            @endphp

            <div class="categoria-box">
                <i class="fas {{ $icono }} icono"></i>
                <h5>{{ $categoria->nombre }}</h5>
                <a href="{{ route('cliente.productos', ['categoria' => $categoria->id]) }}" class="btn-ver">
                    <i class="fas fa-arrow-right"></i> Ver productos
                </a>
            </div>
        @endforeach
    </div>
</section>

<!-- FOOTER -->
<footer>
    <b>Supermercado Atlantia</b> &copy; 2025. Todos los derechos reservados.
</footer>

<script>
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

</body>
</html>
