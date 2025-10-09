<?php

use Illuminate\Support\Facades\Route;

/**
 * ============================
 *  Imports de Controladores
 * ============================
 */
// Públicos / cliente
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ClienteController;

// Auth / perfil
use App\Http\Controllers\ProfileController;

// Empleado
use App\Http\Controllers\Empleado\PedidoEmpleadoController;

// Repartidor
use App\Http\Controllers\Repartidor\RepartidorController;
use App\Http\Controllers\Repartidor\PedidoRepartidorController;

// Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\AdminCategoriaController; // <- nombre real del controlador
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RepartidorAdminController;
use App\Http\Controllers\Admin\VendedorAdminController;
use App\Http\Controllers\Admin\DeliveryZoneController;

// Vendedor
use App\Http\Controllers\Vendedor\PanelVendedorController;
use App\Http\Controllers\Vendedor\ProductoController as VendedorProductoController;
use App\Http\Controllers\Vendedor\PedidoController as VendedorPedidoController;
use App\Http\Controllers\Vendedor\PedidoItemController;
use App\Http\Controllers\Vendedor\ItemController;

// Cliente: tracking de pedido
use App\Http\Controllers\PedidoController; // controlador público/cliente para show/json

/*
|--------------------------------------------------------------------------
| Rutas públicas (catálogo)
|--------------------------------------------------------------------------
*/
Route::get('/', [CarritoController::class, 'catalogo'])->name('inicio');
Route::get('/productos', [CarritoController::class, 'catalogo'])->name('cliente.productos');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::get('/categorias', [ClienteController::class, 'categorias'])->name('cliente.categorias');

/*
|--------------------------------------------------------------------------
| Dashboard genérico
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Perfil usuario autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel Cliente (auth + role:cliente)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:cliente'])->group(function () {
    Route::get('/cliente', fn () => view('panel.cliente'))->name('cliente.panel');

    // Carrito
    Route::get('/carrito', [CarritoController::class, 'ver'])->name('carrito.ver');
    Route::post('/carrito/aumentar/{id}', [CarritoController::class, 'aumentar'])->whereNumber('id')->name('carrito.aumentar');
    Route::post('/carrito/reducir/{id}',   [CarritoController::class, 'reducir'])->whereNumber('id')->name('carrito.reducir');
    Route::post('/carrito/eliminar/{id}',  [CarritoController::class, 'eliminar'])->whereNumber('id')->name('carrito.eliminar');

    // Checkout
    Route::get('/checkout', [CarritoController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/confirmar', [CarritoController::class, 'confirmarCheckout'])->name('checkout.confirmar');

    // Tracking pedido
    Route::get('/cliente/pedido/confirmado', [ClienteController::class, 'pedidoConfirmado'])->name('cliente.pedido.confirmado');
    Route::get('/cliente/pedido/{pedido}', [PedidoController::class, 'showCliente'])->whereNumber('pedido')->name('cliente.estado.pedido');
    Route::get('/cliente/pedido/{pedido}/json', [PedidoController::class, 'estadoJson'])->whereNumber('pedido')->name('cliente.estado.json');
});

/*
|--------------------------------------------------------------------------
| Panel Empleado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:empleado'])->group(function () {
    Route::get('/empleado', [PedidoEmpleadoController::class, 'index'])->name('empleado.panel');
    Route::post('/empleado/items/{item}/preparar', [PedidoEmpleadoController::class, 'preparar'])->whereNumber('item')->name('empleado.items.preparar');
    Route::post('/empleado/items/{item}/listo',     [PedidoEmpleadoController::class, 'listo'])->whereNumber('item')->name('empleado.items.listo');
});

/*
|--------------------------------------------------------------------------
| Panel Repartidor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:repartidor'])
    ->prefix('repartidor')->as('repartidor.')
    ->group(function () {
        Route::get('/panel', [PedidoRepartidorController::class, 'index'])->name('panel');

        Route::post('/items/{item}/entregar', [PedidoRepartidorController::class, 'entregar'])
            ->whereNumber('item')->name('items.entregar');

        Route::get('/perfil', [RepartidorController::class, 'perfil'])->name('perfil');
        Route::get('/soporte', [RepartidorController::class, 'soporte'])->name('soporte');
        Route::get('/pedidos/asignados',  [RepartidorController::class, 'pedidosAsignados'])->name('pedidos.asignados');
        Route::get('/pedidos/entregados', [RepartidorController::class, 'pedidosEntregados'])->name('pedidos.entregados');
    });

/*
|--------------------------------------------------------------------------
| Panel Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'panel'])->name('panel');

        // Usuarios
        Route::get('/usuarios',            [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear',      [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios',           [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{id}/edit',  [UsuarioController::class, 'edit'])->whereNumber('id')->name('usuarios.edit');
        Route::put('/usuarios/{id}',       [UsuarioController::class, 'update'])->whereNumber('id')->name('usuarios.update');
        Route::delete('/usuarios/{id}',    [UsuarioController::class, 'destroy'])->whereNumber('id')->name('usuarios.destroy');

        // Productos y categorías
        Route::resource('/productos',  ProductoController::class)->names('productos');
        Route::resource('/categorias', AdminCategoriaController::class)->names('categorias');

        // Pedidos
        Route::get('/pedidos',                [AdminPedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{pedido}',       [AdminPedidoController::class, 'show'])->whereNumber('pedido')->name('pedidos.show');
        Route::match(['post','put'], '/pedidos/{pedido}/asignar-repartidor', [AdminPedidoController::class, 'asignarRepartidor'])
            ->whereNumber('pedido')->name('pedidos.asignar-repartidor');
        Route::match(['post','put'], '/pedidos/{pedido}/actualizar-estado',  [AdminPedidoController::class, 'actualizarEstado'])
            ->whereNumber('pedido')->name('pedidos.actualizar-estado');

        // Reportes y gestión
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::resource('/repartidores', RepartidorAdminController::class)->names('repartidores');
        Route::resource('/zonas-entrega', DeliveryZoneController::class)->names('delivery-zones');

        Route::get('/vendedores',         [VendedorAdminController::class, 'index'])->name('vendedores.index');
        Route::get('/vendedores/crear',   [VendedorAdminController::class, 'create'])->name('vendedores.create');
        Route::post('/vendedores',        [VendedorAdminController::class, 'store'])->name('vendedores.store');
        Route::patch('/vendedores/{vendor}/status', [VendedorAdminController::class, 'toggleStatus'])
            ->whereNumber('vendor')->name('vendedores.toggle');

        // Extras (opcional)
        Route::view('/clientes',     'admin.clientes')->name('clientes');
        Route::view('/facturacion',  'admin.facturacion')->name('facturacion');
        Route::view('/carritos',     'admin.carritos')->name('carritos');
        Route::view('/cupones',      'admin.cupones')->name('cupones');
    });

/*
|--------------------------------------------------------------------------
| Panel Vendedor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:vendedor', 'vendor.active'])
    ->prefix('vendedor')->as('vendedor.')
    ->group(function () {
        Route::redirect('/', '/vendedor/dashboard');
        Route::get('/dashboard', [PanelVendedorController::class, 'index'])->name('dashboard');

        // Productos del vendedor
        Route::get('/productos',              [VendedorProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/crear',        [VendedorProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos',             [VendedorProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [VendedorProductoController::class, 'edit'])->whereNumber('producto')->name('productos.edit');
        Route::put('/productos/{producto}',   [VendedorProductoController::class, 'update'])->whereNumber('producto')->name('productos.update');
        Route::delete('/productos/{producto}',[VendedorProductoController::class, 'destroy'])->whereNumber('producto')->name('productos.destroy');

        // Pedidos del vendedor
        Route::get('/pedidos',            [VendedorPedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{pedido}',   [VendedorPedidoController::class, 'show'])->whereNumber('pedido')->name('pedidos.show');

        // PDF: factura SAT (simulada) y comprobante de compra con marca
        Route::get('/pedidos/{pedido}/factura.pdf',     [VendedorPedidoController::class, 'facturaPdf'])
            ->whereNumber('pedido')->name('pedidos.factura.pdf');
        Route::get('/pedidos/{pedido}/comprobante.pdf', [VendedorPedidoController::class, 'comprobantePdf'])
            ->whereNumber('pedido')->name('pedidos.comprobante.pdf');

        // PedidoItem (acciones)
        Route::post('/pedidoitems/{pedidoItem}/estado', [PedidoItemController::class, 'updateStatus'])
            ->whereNumber('pedidoItem')->name('pedidoitems.estado');
        Route::post('/pedidoitems/{pedido}/actualizar-todos', [PedidoItemController::class, 'updateAllStatus'])
            ->whereNumber('pedido')->name('pedidoitems.actualizar.todos');

        Route::post('/pedidos/{pedido}/logistica', [PedidoItemController::class, 'assignDelivery'])
            ->whereNumber('pedido')->name('pedidos.logistica');

        // Compat (si aún hay vistas antiguas que apuntan a estos endpoints)
        Route::post('/items/{item}/aceptar',  [VendedorPedidoController::class, 'aceptarItem'])->whereNumber('item')->name('items.aceptar');
        Route::post('/items/{item}/rechazar', [VendedorPedidoController::class, 'rechazarItem'])->whereNumber('item')->name('items.rechazar');
        Route::post('/items/{item}/estado',   [VendedorPedidoController::class, 'actualizarEstado'])->whereNumber('item')->name('items.estado');

        // Vista estado ítem (opcional)
        Route::get('/items/{item}/estado', [ItemController::class, 'estado'])->whereNumber('item')->name('items.estado.view');
    });

/*
|--------------------------------------------------------------------------
| Contacto
|--------------------------------------------------------------------------
*/
Route::get('/contacto', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contacto', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(fn () => redirect()->route('cliente.productos'));

/*
|--------------------------------------------------------------------------
| Auth scaffolding
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
