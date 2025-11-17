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
use App\Http\Controllers\Cliente\ReseñaController as ClienteReseñaController;

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
use App\Http\Controllers\Admin\AdminCategoriaController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\RepartidorAdminController;
use App\Http\Controllers\Admin\VendedorAdminController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\ReseñaController as AdminReseñaController;

// Vendedor
use App\Http\Controllers\Vendedor\PanelVendedorController;
use App\Http\Controllers\Vendedor\ProductoController as VendedorProductoController;
use App\Http\Controllers\Vendedor\PedidoController as VendedorPedidoController;
use App\Http\Controllers\Vendedor\PedidoItemController;
use App\Http\Controllers\Vendedor\ItemController;
use App\Http\Controllers\Vendedor\VendorZoneController;
use App\Http\Controllers\Vendedor\MarketCourierStatusController;
use App\Http\Controllers\Vendedor\ReseñaController;
use App\Http\Controllers\Vendedor\VendedorPerfilController;

// Cliente: tracking de pedido
use App\Http\Controllers\PedidoController;

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
    Route::get('/cliente', fn () => redirect()->route('cliente.productos'))->name('cliente.panel');

    // Carrito
    Route::get('/carrito', [CarritoController::class, 'ver'])->name('carrito.ver');
    Route::post('/carrito/aumentar/{id}', [CarritoController::class, 'aumentar'])->whereNumber('id')->name('carrito.aumentar');
    Route::post('/carrito/reducir/{id}', [CarritoController::class, 'reducir'])->whereNumber('id')->name('carrito.reducir');
    Route::post('/carrito/eliminar/{id}', [CarritoController::class, 'eliminar'])->whereNumber('id')->name('carrito.eliminar');

    // Checkout
    Route::get('/checkout', [CarritoController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/confirmar', [CarritoController::class, 'confirmarCheckout'])->name('checkout.confirmar');

    // Tracking pedido
    Route::get('/cliente/pedido/confirmado', [ClienteController::class, 'pedidoConfirmado'])->name('cliente.pedido.confirmado');
    Route::get('/cliente/pedido/{pedido}', [PedidoController::class, 'showCliente'])->whereNumber('pedido')->name('cliente.estado.pedido');
    Route::get('/cliente/pedido/{pedido}/json', [PedidoController::class, 'estadoJson'])->whereNumber('pedido')->name('cliente.estado.json');

    // Reseñas de productos
    Route::get('/cliente/reseñas', [ClienteReseñaController::class, 'index'])->name('cliente.reseñas.index');
    Route::post('/cliente/reseñas/{pedidoItem}', [ClienteReseñaController::class, 'store'])
        ->whereNumber('pedidoItem')
        ->name('cliente.reseñas.store');
});

/*
|--------------------------------------------------------------------------
| Panel Empleado
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:empleado'])->group(function () {
    Route::get('/empleado', [PedidoEmpleadoController::class, 'index'])->name('empleado.panel');
    Route::post('/empleado/items/{item}/preparar', [PedidoEmpleadoController::class, 'preparar'])->whereNumber('item')->name('empleado.items.preparar');
    Route::post('/empleado/items/{item}/listo', [PedidoEmpleadoController::class, 'listo'])->whereNumber('item')->name('empleado.items.listo');
});

/*
|--------------------------------------------------------------------------
| Panel Repartidor
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:repartidor'])
    ->prefix('repartidor')->as('repartidor.')
    ->group(function () {
        Route::get('/panel', [RepartidorController::class, 'index'])->name('panel');

        Route::get('/pedidos/asignados', [RepartidorController::class, 'pedidosAsignados'])->name('pedidos.asignados');
        Route::get('/pedidos/entregados', [RepartidorController::class, 'pedidosEntregados'])->name('pedidos.entregados');
        Route::get('/pedidos/{pedido}/rastreo', [RepartidorController::class, 'rastreo'])->whereNumber('pedido')->name('pedidos.rastreo');

        Route::post('/pedidos/{pedido}/aceptar', [RepartidorController::class, 'aceptar'])->whereNumber('pedido')->name('pedidos.aceptar');
        Route::post('/pedidos/{pedido}/iniciar-ruta', [RepartidorController::class, 'iniciarRuta'])->whereNumber('pedido')->name('pedidos.iniciar');
        Route::post('/pedidos/{pedido}/entregado', [RepartidorController::class, 'confirmarEntrega'])->whereNumber('pedido')->name('pedidos.entregado');
        Route::post('/pedidos/{pedido}/incidencia', [RepartidorController::class, 'marcarProblema'])->whereNumber('pedido')->name('pedidos.incidencia');

        Route::post('/items/{item}/entregar', [PedidoRepartidorController::class, 'entregar'])->whereNumber('item')->name('items.entregar');
        Route::get('/perfil', [RepartidorController::class, 'perfil'])->name('perfil');
        Route::get('/soporte', [RepartidorController::class, 'soporte'])->name('soporte');
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
        Route::resource('/usuarios', UsuarioController::class)->names('usuarios');

        // Productos y categorías
        Route::resource('/productos', ProductoController::class)->names('productos');
        Route::resource('/categorias', AdminCategoriaController::class)->names('categorias');

        // Pedidos
        Route::get('/pedidos', [AdminPedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{pedido}', [AdminPedidoController::class, 'show'])->whereNumber('pedido')->name('pedidos.show');

        // Reportes y gestión
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::resource('/repartidores', RepartidorAdminController::class)->names('repartidores');
        Route::resource('/zonas-entrega', DeliveryZoneController::class)
            ->parameters(['zonas-entrega' => 'deliveryZone'])
            ->names('delivery-zones');

        // Vendedores
        Route::resource('/vendedores', VendedorAdminController::class)->names('vendedores');

        // Reseñas de productos
        Route::get('/reseñas', [AdminReseñaController::class, 'index'])->name('reseñas.index');
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

        // Productos
        Route::resource('/productos', VendedorProductoController::class)->names('productos');

        // Pedidos
        Route::get('/pedidos', [VendedorPedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{pedido}', [VendedorPedidoController::class, 'show'])->whereNumber('pedido')->name('pedidos.show');
        Route::get('/pedidos/{pedido}/factura', [VendedorPedidoController::class, 'facturaPdf'])
            ->whereNumber('pedido')
            ->name('pedidos.factura.pdf');
        Route::get('/pedidos/{pedido}/comprobante', [VendedorPedidoController::class, 'comprobantePdf'])
            ->whereNumber('pedido')
            ->name('pedidos.comprobante.pdf');

        // Items de pedido
        Route::post('/pedidoitems/{pedidoItem}/estado', [PedidoItemController::class, 'updateStatus'])
            ->whereNumber('pedidoItem')->name('pedidoitems.estado');
        Route::post('/pedidos/{pedido}/estado', [PedidoItemController::class, 'updateAllStatus'])
            ->whereNumber('pedido')->name('pedidos.estado');
        Route::post('/pedidos/{pedido}/logistica', [PedidoItemController::class, 'assignDelivery'])
            ->whereNumber('pedido')->name('pedidos.logistica');

        // Zonas de reparto personalizadas
        Route::get('/zonas-reparto', [VendorZoneController::class, 'index'])->name('zonas.index');
        Route::get('/zonas-reparto/crear', [VendorZoneController::class, 'create'])->name('zonas.create');
        Route::post('/zonas-reparto', [VendorZoneController::class, 'store'])->name('zonas.store');
        Route::get('/zonas-reparto/{zona}/editar', [VendorZoneController::class, 'edit'])
            ->whereNumber('zona')->name('zonas.edit');
        Route::put('/zonas-reparto/{zona}', [VendorZoneController::class, 'update'])
            ->whereNumber('zona')->name('zonas.update');
        Route::delete('/zonas-reparto/{zona}', [VendorZoneController::class, 'destroy'])
            ->whereNumber('zona')->name('zonas.destroy');

        // Estado del repartidor del supermercado
        Route::get('/repartidor-supermercado/estado', MarketCourierStatusController::class)
            ->name('repartidor.estado');

        // Perfil del vendedor
        Route::get('/perfil', [VendedorPerfilController::class, 'index'])->name('perfil');

        // ✅ Reseñas de productos
        Route::get('/reseñas', [ReseñaController::class, 'index'])->name('reseñas.index');
        Route::post('/reseñas/{reseña}/responder', [ReseñaController::class, 'responder'])->name('reseñas.responder');
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
