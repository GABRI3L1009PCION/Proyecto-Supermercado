<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supermercado Atlantia - Tu compra fácil y rápida</title>
    <link rel="icon" href="{{ asset('storage/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .sticky-nav {
            position: sticky;
            top: 0;
            z-index: 50;
        }
    </style>
</head>
<body class="bg-green-50 min-h-screen flex flex-col font-sans">

<!-- Header con navegación sticky -->
<header class="sticky-nav bg-green-700 shadow-lg">
    <!-- Barra superior con información importante -->
    <div class="bg-green-800 text-white text-sm py-1 px-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex space-x-4">
                <span><i class="fas fa-phone-alt mr-1"></i> Llámanos: 1234-5678</span>
                <span><i class="fas fa-map-marker-alt mr-1"></i> Envíos a todo el país</span>
            </div>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-green-200"><i class="fas fa-question-circle mr-1"></i> Ayuda</a>
                <a href="#" class="hover:text-green-200"><i class="fas fa-store mr-1"></i> Nuestras tiendas</a>
            </div>
        </div>
    </div>

    <!-- Barra principal de navegación -->
    <div class="container mx-auto flex flex-wrap justify-between items-center py-3 px-4 md:px-8">
        <!-- Logo y nombre -->
        <div class="flex items-center gap-3">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo Supermercado Atlantia" class="h-12 w-12 rounded-full shadow-lg bg-white ring-2 ring-green-300">
            <div>
                <span class="text-2xl font-extrabold text-white block">Supermercado Atlantia</span>
                <span class="text-green-100 text-sm">Compra fácil, vive mejor</span>
            </div>
        </div>

        <!-- Barra de búsqueda -->
        <div class="w-full md:w-auto mt-4 md:mt-0 md:flex-1 md:px-8">
            <form method="GET" action="{{ route('cliente.productos') }}" class="relative">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar productos..."
                       class="w-full rounded-full border-0 py-3 px-5 pr-12 shadow-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                <button type="submit" class="absolute right-3 top-3 text-green-600 hover:text-green-800">
                    <i class="fas fa-search fa-lg"></i>
                </button>
            </form>
        </div>

        <!-- Acciones de usuario -->
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <div class="relative">
                <a href="{{ route('login') }}" class="text-white hover:text-green-200 flex flex-col items-center">
                    <i class="fas fa-user-circle text-2xl"></i>
                    <span class="text-xs mt-1">Mi cuenta</span>
                </a>
            </div>
            <div class="relative">
                <a href="#" class="text-white hover:text-green-200 flex flex-col items-center" id="cart-icon">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                    <span class="text-xs mt-1">Carrito</span>
                    <span class="cart-badge">0</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Menú de categorías -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <ul class="flex flex-wrap justify-center md:justify-start space-x-1 md:space-x-6 py-2">
                <li class="dropdown relative">
                    <button class="flex items-center px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">
                        <i class="fas fa-bars mr-2"></i> Todas las categorías
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                        @foreach($categorias as $cat)
                            <a href="{{ route('cliente.productos', ['categoria' => $cat->id]) }}" class="block px-4 py-2 text-gray-800 hover:bg-green-50">{{ $cat->nombre }}</a>
                        @endforeach
                    </div>
                </li>
                <li><a href="#" class="px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">Ofertas</a></li>
                <li><a href="#" class="px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">Frescos</a></li>
                <li><a href="#" class="px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">Despensa</a></li>
                <li><a href="#" class="px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">Lácteos</a></li>
                <li><a href="#" class="px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">Bebidas</a></li>
                <li><a href="#" class="px-3 py-2 text-green-700 hover:bg-green-50 rounded-md font-medium">Limpieza</a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Banner promocional -->
<div class="bg-green-600 text-white">
    <div class="container mx-auto px-4 py-2 text-center">
        <p class="text-sm md:text-base">¡Envío gratis en compras mayores a Q200! <a href="#" class="font-bold underline ml-2">Ver más</a></p>
    </div>
</div>

<!-- Contenido principal -->
<main class="flex-1 container mx-auto px-4 py-6">
    <!-- Carrusel de ofertas -->
    <div class="mb-10 rounded-xl overflow-hidden shadow-lg">
        <div class="bg-gradient-to-r from-green-500 to-green-700 h-64 md:h-80 flex items-center justify-center text-white text-2xl font-bold">
            Carrusel de Ofertas y Promociones
        </div>
    </div>

    <!-- Sección de categorías destacadas -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-green-800 mb-6">Explora nuestras categorías</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($categorias->take(6) as $cat)
                <a href="{{ route('cliente.productos', ['categoria' => $cat->id]) }}" class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg transition">
                    <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-{{ $cat->icono ?? 'shopping-basket' }} text-green-600 text-xl"></i>
                    </div>
                    <span class="text-green-800 font-medium">{{ $cat->nombre }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Productos destacados -->
    <section class="mb-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-green-800">Productos destacados</h2>
            <a href="#" class="text-green-600 hover:text-green-800 font-medium">Ver todos</a>
        </div>

        <div class="grid gap-6 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            @foreach($productos->take(5) as $producto)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition border border-green-100 flex flex-col">
                    @if($producto->descuento)
                        <div class="absolute bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-br-lg">
                            -{{ $producto->descuento }}%
                        </div>
                    @endif
                    <div class="bg-green-50 flex items-center justify-center h-40 p-4 relative">
                        <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('storage/logo.png') }}"
                             alt="Imagen de {{ $producto->nombre }}"
                             class="h-32 object-contain">
                        <button class="absolute right-2 bottom-2 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-md hover:bg-green-100">
                            <i class="far fa-heart text-green-600"></i>
                        </button>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-green-800 mb-1">{{ $producto->nombre }}</h3>
                        <span class="text-xs text-green-500 mb-2">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>

                        @if($producto->descuento)
                            <div class="flex items-center mb-2">
                                <span class="text-xl font-extrabold text-green-700">Q{{ number_format($producto->precio * (1 - $producto->descuento/100), 2) }}</span>
                                <span class="text-xs text-gray-500 line-through ml-2">Q{{ number_format($producto->precio, 2) }}</span>
                            </div>
                        @else
                            <span class="text-xl font-extrabold text-green-700 mb-2">Q{{ number_format($producto->precio, 2) }}</span>
                        @endif

                        <div class="flex items-center justify-between mt-auto">
                            <div class="flex items-center text-yellow-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <i class="far fa-star"></i>
                                <span class="text-gray-500 ml-1">(24)</span>
                            </div>
                            <button class="bg-green-600 text-white p-2 rounded-full shadow hover:bg-green-700 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Banner intermedio -->
    <div class="bg-gradient-to-r from-green-500 to-green-700 rounded-xl p-6 md:p-10 text-white mb-10">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-4 md:mb-0">
                <h3 class="text-2xl md:text-3xl font-bold mb-2">¡Compra ahora y paga después!</h3>
                <p class="mb-4">Disfruta de nuestros productos ahora y paga en cómodas cuotas.</p>
                <button class="bg-white text-green-700 font-bold py-2 px-6 rounded-full shadow hover:bg-green-100 transition">
                    Conoce más
                </button>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <img src="https://via.placeholder.com/300x200" alt="Pago a plazos" class="rounded-lg shadow-lg">
            </div>
        </div>
    </div>

    <!-- Ofertas especiales -->
    <section class="mb-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-green-800">Ofertas especiales</h2>
            <a href="#" class="text-green-600 hover:text-green-800 font-medium">Ver todas</a>
        </div>

        <div class="grid gap-6 grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            @foreach($productos->slice(5, 5) as $producto)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition border border-green-100 flex flex-col">
                    <div class="bg-green-50 flex items-center justify-center h-40 p-4 relative">
                        <img src="{{ $producto->imagen ? asset('storage/productos/' . $producto->imagen) : asset('storage/logo.png') }}"
                             alt="Imagen de {{ $producto->nombre }}"
                             class="h-32 object-contain">
                        <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            OFERTA
                        </div>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-green-800 mb-1">{{ $producto->nombre }}</h3>
                        <span class="text-xs text-green-500 mb-2">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>

                        <div class="flex items-center mb-2">
                            <span class="text-xl font-extrabold text-green-700">Q{{ number_format($producto->precio * 0.9, 2) }}</span>
                            <span class="text-xs text-gray-500 line-through ml-2">Q{{ number_format($producto->precio, 2) }}</span>
                        </div>

                        <div class="bg-green-100 rounded-full h-2 mb-3">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mb-3">Quedan 12 unidades</div>

                        <button class="mt-auto bg-green-600 text-white py-2 rounded-lg shadow hover:bg-green-700 transition w-full font-medium">
                            Añadir al carrito
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Marcas destacadas -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold text-green-800 mb-6">Nuestras marcas</h2>
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="grid grid-cols-3 md:grid-cols-6 gap-6">
                @foreach(range(1,6) as $i)
                    <div class="flex items-center justify-center p-3 border border-gray-200 rounded-lg hover:shadow-md transition">
                        <img src="https://via.placeholder.com/100x50?text=Marca{{ $i }}" alt="Marca {{ $i }}" class="h-8 object-contain">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</main>

<!-- Footer completo -->
<footer class="bg-green-800 text-white pt-10">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Información de contacto -->
            <div>
                <h3 class="text-lg font-bold mb-4">Contacto</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-green-300"></i>
                        <span>Av. Principal 123, Ciudad</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone-alt mt-1 mr-2 text-green-300"></i>
                        <span>1234-5678</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-2 text-green-300"></i>
                        <span>info@atlantia.com</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mt-1 mr-2 text-green-300"></i>
                        <span>Lun-Vie: 8:00 - 18:00<br>Sab: 8:00 - 13:00</span>
                    </li>
                </ul>
            </div>

            <!-- Enlaces rápidos -->
            <div>
                <h3 class="text-lg font-bold mb-4">Enlaces rápidos</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-green-300">Inicio</a></li>
                    <li><a href="#" class="hover:text-green-300">Productos</a></li>
                    <li><a href="#" class="hover:text-green-300">Ofertas</a></li>
                    <li><a href="#" class="hover:text-green-300">Sobre nosotros</a></li>
                    <li><a href="#" class="hover:text-green-300">Contacto</a></li>
                </ul>
            </div>

            <!-- Mi cuenta -->
            <div>
                <h3 class="text-lg font-bold mb-4">Mi cuenta</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-green-300">Iniciar sesión</a></li>
                    <li><a href="#" class="hover:text-green-300">Registrarse</a></li>
                    <li><a href="#" class="hover:text-green-300">Carrito</a></li>
                    <li><a href="#" class="hover:text-green-300">Lista de deseos</a></li>
                    <li><a href="#" class="hover:text-green-300">Seguimiento de pedido</a></li>
                </ul>
            </div>

            <!-- Boletín informativo -->
            <div>
                <h3 class="text-lg font-bold mb-4">Boletín informativo</h3>
                <p class="mb-4 text-green-200">Suscríbete para recibir ofertas exclusivas y novedades.</p>
                <form class="flex">
                    <input type="email" placeholder="Tu correo" class="px-4 py-2 rounded-l-lg focus:outline-none text-gray-800 w-full">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-r-lg">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-2xl hover:text-green-300"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-2xl hover:text-green-300"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-2xl hover:text-green-300"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-2xl hover:text-green-300"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <!-- Métodos de pago -->
        <div class="border-t border-green-700 pt-6 pb-4">
            <h4 class="text-center font-bold mb-4">Métodos de pago</h4>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach(['visa', 'mastercard', 'paypal', 'american-express', 'bank-transfer'] as $method)
                    <div class="bg-white rounded p-2 shadow">
                        <i class="fab fa-cc-{{ $method }} text-3xl text-gray-700"></i>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Derechos de autor -->
        <div class="border-t border-green-700 py-4 text-center text-sm text-green-300">
            <p>© {{ date('Y') }} Supermercado Atlantia. Todos los derechos reservados.</p>
            <div class="mt-2 space-x-4">
                <a href="#" class="hover:text-white">Términos y condiciones</a>
                <a href="#" class="hover:text-white">Política de privacidad</a>
                <a href="#" class="hover:text-white">Aviso legal</a>
            </div>
        </div>
    </div>
</footer>

<!-- Carrito flotante (oculto por defecto) -->
<div id="cart-sidebar" class="fixed top-0 right-0 h-full w-full md:w-96 bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 overflow-y-auto">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-green-800">Tu carrito (3)</h3>
            <button id="close-cart" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div class="space-y-4 mb-6">
            <!-- Producto en carrito -->
            <div class="flex border-b pb-4">
                <div class="w-20 h-20 bg-green-50 rounded-lg flex items-center justify-center mr-4">
                    <img src="https://via.placeholder.com/50" alt="Producto" class="h-16 object-contain">
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-green-800">Nombre del producto</h4>
                    <p class="text-sm text-gray-600">Q25.00 x 2</p>
                    <div class="flex items-center mt-2">
                        <button class="text-gray-500 hover:text-green-600">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="mx-2">2</span>
                        <button class="text-gray-500 hover:text-green-600">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="ml-auto text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Más productos... -->
        </div>

        <div class="bg-green-50 rounded-lg p-4 mb-6">
            <div class="flex justify-between mb-2">
                <span>Subtotal:</span>
                <span class="font-medium">Q75.00</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>Envío:</span>
                <span class="font-medium">Q15.00</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-green-700">
                <span>Total:</span>
                <span>Q90.00</span>
            </div>
        </div>

        <button class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition mb-4">
            Proceder al pago
        </button>

        <button class="w-full border border-green-600 text-green-600 py-3 rounded-lg font-bold hover:bg-green-50 transition">
            Seguir comprando
        </button>
    </div>
</div>

<!-- Scripts -->
<script>
    // Mostrar/ocultar carrito
    document.getElementById('cart-icon').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('cart-sidebar').classList.remove('translate-x-full');
    });

    document.getElementById('close-cart').addEventListener('click', function() {
        document.getElementById('cart-sidebar').classList.add('translate-x-full');
    });

    // Menú desplegable para móviles
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
</body>
</html>
