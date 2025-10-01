<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de compras - Supermercado Atlantia</title>
    <link rel="icon" href="{{ asset('storage/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --vino: #800020;
            --vino-oscuro: #66001a;
            --vino-claro: #fbe9ec;
            --blanco: #fff;
            --gris: #f4f4f4;
            --gris-texto: #6b7280;
        }

        *{box-sizing:border-box;margin:0;padding:0}

        body{
            font-family:'Segoe UI',Arial,sans-serif;
            background:var(--gris);
            color:#222;
            min-height:100vh;
            display:flex;
            flex-direction:column;
            line-height:1.6;
        }

        /* NAVBAR (PC en una sola línea) */
        .navbar{
            background:var(--blanco);
            box-shadow:0 2px 8px #0002;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0.7rem 1.5rem;
            position:sticky;
            top:0;
            z-index:99;
            flex-wrap:nowrap;              /* <— clave para que no baje a 2 filas */
        }
        .navbar .logo{
            display:flex;
            align-items:center;
            gap:10px;
            width:auto;                    /* <— evita ocupar toda la fila */
        }
        .navbar .logo img{
            height:55px;
            width:auto;
            object-fit:contain;
            background:transparent;
        }
        /* Empuja el nav a la derecha en PC */
        #mainNav{ margin-left:auto; }

        /* Botón hamburguesa (oculto en desktop) */
        .menu-toggle{
            margin-left:auto;
            display:none;
            flex-direction:column;
            justify-content:space-around;
            width:30px;height:25px;
            background:transparent;border:none;cursor:pointer;padding:0;
        }
        .menu-toggle span{
            width:100%;height:3px;background:var(--vino-oscuro);
            border-radius:5px;transition:all .3s;
        }

        .navbar nav{
            display:flex;align-items:center;gap:1rem;
            overflow-x:auto;white-space:nowrap;scrollbar-width:none;
        }
        .navbar nav::-webkit-scrollbar{display:none}
        .navbar nav a{
            text-decoration:none;color:var(--vino-oscuro);
            font-weight:500;padding:.4rem 1rem;border-radius:8px;transition:.2s;
        }
        .navbar nav a:hover,.navbar nav .activo{background:var(--vino-claro);color:var(--vino)}

        /* CONTENIDO */
        .carrito-container{
            max-width:1100px;margin:2rem auto;background:var(--blanco);
            padding:2rem;border-radius:12px;box-shadow:0 2px 12px #0001;
            border:2px solid var(--vino-claro);width:100%;
        }

        /* Botón Regresar */
        .btn-regresar{
            display:inline-flex;align-items:center;gap:.5rem;
            background:var(--vino-claro);color:var(--vino);
            padding:.6rem 1.2rem;border-radius:8px;text-decoration:none;
            font-weight:600;margin:1.5rem auto 0;transition:all .2s ease;
            border:1px solid var(--vino-claro);max-width:1100px;width:calc(100% - 3rem);
        }
        .btn-regresar:hover{background:var(--vino);color:#fff}

        .carrito-title{
            color:var(--vino);text-align:center;font-size:2rem;
            font-weight:bold;margin-bottom:1.5rem;
        }

        .alert{border-radius:8px;padding:1rem 1.2rem;margin-bottom:1rem;font-weight:500}
        .alert-success{background:#e7fce9;color:#116334;border:1px solid #a7f3d0}
        .alert-error{background:#ffe4e6;color:#b91c1c;border:1px solid #fca5a5}

        .carrito-table{width:100%;border-collapse:collapse;margin-bottom:1rem}
        .carrito-table th,.carrito-table td{
            padding:1rem;border-bottom:1px solid #e5e5e5;text-align:center
        }
        .carrito-table th{background:var(--vino-claro);color:var(--vino-oscuro);font-weight:bold}
        .carrito-table td{font-size:1rem;color:#333}
        .carrito-table td.text-vino{color:var(--vino);font-weight:bold}

        .carrito-total{
            text-align:right;font-weight:bold;color:var(--vino-oscuro);
            padding:1rem 0;font-size:1.2rem
        }

        .btn{
            display:inline-block;background:var(--vino);color:#fff;padding:.8rem 2rem;
            border-radius:8px;text-decoration:none;font-weight:bold;font-size:1rem;
            border:none;cursor:pointer;transition:background .2s;margin:.5rem;
        }
        .btn:hover{background:var(--vino-oscuro)}

        .carrito-empty{text-align:center;color:#666;font-size:1.1rem;padding:2rem 0}
        .botonera{text-align:center;margin-top:2rem}

        footer{
            background:var(--vino);color:#fff;text-align:center;
            font-size:1rem;padding:1.1rem 0;margin-top:auto;
        }

        /* =========================
           RESPONSIVE (igual al ejemplo)
           ========================= */
        @media (max-width:767px){
            .navbar{
                position:relative;padding:.7rem 1rem;
                flex-direction:column;align-items:flex-start;
            }
            .navbar .logo{width:100%;justify-content:space-between;margin-bottom:0}
            .menu-toggle{display:flex}

            .navbar nav{
                display:none;flex-direction:column;position:absolute;top:100%;left:0;right:0;
                background:var(--blanco);padding:1.2rem 1rem;box-shadow:0 5px 15px rgba(0,0,0,.1);
                gap:.8rem;align-items:center;z-index:100;text-align:center;
            }
            .navbar nav.active{display:flex}
            .navbar nav a{width:90%;padding:.8rem 1rem;border-radius:6px}

            .carrito-container{margin:1.5rem auto;padding:1.2rem}
            .btn-regresar{width:calc(100% - 2rem);margin:1rem auto 0}
        }

        @media (max-width:480px){
            .navbar .logo img{height:40px}
            .carrito-title{font-size:1.6rem}
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">
        <!-- Mismo logo del ejemplo -->
        <img src="{{ asset('img/Atlantia.png') }}" alt="Supermercado Atlantia">
        <button class="menu-toggle" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>
    </div>

    <nav id="mainNav">
        <a href="{{ route('inicio') }}">Inicio</a>
        <a href="{{ route('cliente.productos') }}" class="activo">Catálogo</a>
        <a href="#contacto">Contacto</a>
        <a href="{{ route('carrito.ver') }}" class="carrito" title="Ver carrito">
            <i class="fa fa-shopping-cart"></i>
            <span style="font-weight:bold;">
                {{ is_countable(session('carrito')) ? count(session('carrito')) : 0 }}
            </span>
        </a>
        <a href="{{ route('login') }}" class="login-btn">Iniciar sesión</a>
    </nav>
</div>

<!-- Botón Regresar -->
<a href="{{ route('cliente.productos') }}" class="btn-regresar">
    <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
</a>

<!-- CONTENIDO -->
<div class="carrito-container">
    <h1 class="carrito-title">🛒 Tu carrito de compras</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if (count($carrito ?? []) > 0)
        <table class="carrito-table">
            <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
            @php $total = 0; @endphp
            @foreach (($carrito ?? []) as $id => $item)
                @php
                    $subtotal = ($item['precio'] ?? 0) * ($item['cantidad'] ?? 0);
                    $total += $subtotal;
                @endphp
                <tr>
                    <td>{{ $item['nombre'] ?? '—' }}</td>
                    <td>{{ $item['cantidad'] ?? 0 }}</td>
                    <td>Q{{ number_format($item['precio'] ?? 0, 2) }}</td>
                    <td class="text-vino">Q{{ number_format($subtotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="carrito-total">Total: Q{{ number_format($total, 2) }}</div>

        <div class="botonera">
            <a href="{{ route('cliente.productos') }}" class="btn">🛍️ Seguir comprando</a>

            @auth
                <a href="{{ route('checkout') }}" class="btn">✅ Realizar pedido</a>
            @else
                <p style="margin-top:1rem;color:#444;">
                    Para confirmar el pedido necesitas
                    <a href="{{ route('login') }}" style="color:var(--vino);font-weight:bold;">iniciar sesión</a>.
                </p>
            @endauth
        </div>
    @else
        <p class="carrito-empty">Tu carrito está vacío.</p>
        <div class="botonera">
            <a href="{{ route('cliente.productos') }}" class="btn">🛍️ Ir al catálogo</a>
        </div>
    @endif
</div>

<footer>
    <b>Supermercado Atlantia</b> &copy; 2025. Todos los derechos reservados.
</footer>

<!-- Toggle del menú hamburguesa -->
<script>
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.getElementById('mainNav');
    if (toggle && nav) {
        toggle.addEventListener('click', () => nav.classList.toggle('active'));
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('active');
            }
        });
    }
</script>

</body>
</html>
