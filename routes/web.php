<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
//use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuComidasController;
use App\Http\Controllers\cliente\CarritoController;
use App\Http\Controllers\UserMenuController;
use App\Http\Controllers\vendedor\VendedorController;
use App\Http\Controllers\vendedor\VentasVendedorController;
use App\models\User;
use App\Http\Controllers\cliente\PedidosController;
use App\Http\Controllers\Vendedor\VendedorPagoController;
use App\Http\Controllers\Vendedor\InventarioController;
use App\Http\Controllers\DireccionesController;
/*
|--------------------------------------------------------------------------
| Ruta principal
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect('/dashboard')
        : view('welcome');
})->name('dashboard');
Route::middleware('auth')->group(function () {

    Route::put('/perfil', [AuthController::class, 'update'])
        ->name('perfil.update');

});
Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard.welcome');
})->name('dashboard');
Route::post('/perfil/foto', [AuthController::class,'updateFoto'])->name('perfil.foto');
/*
|--------------------------------------------------------------------------
| Login / Registro (solo invitados)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Mostrar formulario login / registro
    Route::get('/login', function () {
        return view('welcome');
    })->name('login');

    Route::get('/register', function () {
        return view('welcome');
    })->name('register');

    // Procesar formularios
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.welcome');
    });


    Route::post('/logout',
        [AuthController::class, 'logout']
    )->name('logout');

});
// Ruta para actualizar la dirección
Route::put('/direcciones/{id}', [DireccionesController::class, 'update'])->name('direccion.update');
Route::post('/usuarios/{id}/rol', [AuthController::class,'cambiarRol'])
->name('usuarios.cambiarRol')
->middleware('auth');
/*
|--------------------------------------------------------------------------
| Rutas CLIENTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Cliente'])->group(function () {
    // Confirmar compra
    Route::post('/confirmar-compra', [CarritoController::class, 'confirmarCompra'])
        ->name('confirmar.compra');
    Route::get('/convertirse-vendedor', [VendedorController::class, 'indexcambiar'])->name('vendedor.solicitud');
    Route::post('/convertirse-vendedor', [VendedorController::class, 'cambiar'])->name('vendedor.cambiar');

    Route::get('/cliente/menu', [MenuComidasController::class, 'index'])
        ->name('cliente.menu');
    Route::get('/cliente/pedidos', [PedidosController::class, 'verPedidos'])->name('cliente.pedidos');

    Route::get('/cliente/menu/listar', [MenuComidasController::class, 'listar']);
    Route::get('/cliente/menu/buscar', [MenuComidasController::class, 'buscar']);

    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.ver');
    Route::get('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::delete('/carrito/item/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/actualizar', [CarritoController::class, 'actualizar']);
    Route::get('/carrito/listar', [CarritoController::class, 'listar']);
    Route::get('/carrito/pago', [CarritoController::class, 'pago']);
    Route::get('/cliente/vendedores', [VendedorController::class, 'vendedores'])
        ->name('cliente.vendedores');

    Route::get('/cliente/vendedor/{id}', [VendedorController::class, 'catalogoVendedor'])
        ->name('cliente.catalogo.vendedor');
    Route::post('/pedido/cancelar', [PedidosController::class, 'cancelar'])->name('pedido.cancelar');

});

//perfil logeado
Route::middleware('auth')->get('/perfil', function () {
    return view('dashboard.perfil');
})->name('perfil');

Route::post('/direccion', [DireccionesController::class,'store'])
->name('direccion.store')
->middleware('auth');
Route::get('/direccion/user', [DireccionesController::class,'index'])
->name('direccion.index')
->middleware('auth');
/*
|--------------------------------------------------------------------------
| Rutas VENDEDOR
|--------------------------------------------------------------------------
*/
    Route::get('/inventario', [InventarioController::class,'index'])->name('inventario.index');
    Route::post('/inventario/actualizar', [InventarioController::class,'actualizar'])->name('inventario.actualizar');
    Route::get('/inventario/historial/{id}', [InventarioController::class,'historial'])
    ->name('inventario.historial');
    
Route::get('/ajax/categorias', function () {
    return \App\Models\Categoria::select('id', 'nombre')->get();
})->name('ajax.categorias');
use App\Http\Controllers\Administrador\VentasController;
Route::middleware(['auth', 'role:Vendedor'])->group(function () {
    Route::get('/vendedor/factura-pdf/{pedido}', [VentasVendedorController::class, 'generarFacturaPDF'])
    ->name('vendedor.factura.pdf');

    Route::get('/vendedor/productos/crear',[VendedorController::class,'index']
    )->name('vendedor.productos.crear');
    Route::get('/vendedor/factura/{pedido}', [VentasVendedorController::class, 'generarFactura'])->name('vendedor.factura');
    Route::post('/vendedor/productos', [UserMenuController::class, 'store'])
        ->name('vendedor.productos.store');

    Route::get('/vendedor/productos', [MenuComidasController::class, 'misProductos'])
        ->name('vendedor.productos');

    Route::get('/vendedor/menu/ajax', [UserMenuController::class, 'ajaxMenuUsuario'])
        ->name('vendedor.menu.ajax');

    Route::put('/vendedor/productos/{id}', [UserMenuController::class, 'update'])
        ->name('vendedor.productos.update');

    Route::delete('/vendedor/productos/{id}', [UserMenuController::class, 'destroy'])
        ->name('vendedor.productos.destroy');

    Route::get('/vendedor/ventas', [VentasVendedorController::class, 'VerVentas'])
        ->name('vendedor.ventas');

    Route::get('/vendedor/pedidos', [VentasVendedorController::class, 'pedidos'])
        ->name('vendedor.pedidos');
    Route::post('/vendedor/cambiar-estado', 
    [VentasVendedorController::class, 'cambiarEstado'])->name('vendedor.cambiarEstado');
//pagos 


        Route::get('/vendedor/pagos', [VendedorPagoController::class, 'index'])
            ->name('vendedor.pagos');

        Route::post('/vendedor/pagos', [VendedorPagoController::class, 'store'])
            ->name('vendedor.pagos.guardar');

});


/*
|--------------------------------------------------------------------------
| RUTAS ADMIN (ejemplo)
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth','admin'])->group(function () {
//     Route::get('/admin/dashboard', [AdminController::class, 'index']);
// });
use App\Http\Controllers\Administrador\AdminUserController;
use App\Http\Controllers\Administrador\BackupController;

Route::middleware(['auth', 'role:Admin'])->group(function () {

    Route::get('/admin/usuarios', [AdminUserController::class, 'index'])
        ->name('administrador.usuarios');

    Route::get('/backup', [BackupController::class, 'index'])
        ->name('backup.index');

    Route::post('/backup', [BackupController::class, 'create'])
        ->name('backup.create');

    Route::post('/backup/restore', [BackupController::class, 'restore'])
        ->name('backup.restore');
    Route::post('/backup/config', [BackupController::class, 'config'])
        ->name('backup.config');
    Route::get('/admin/solicitudes-vendedor',[AdminUserController::class,'solicitudesVendedor'])
    ->name('admin.solicitudes.vendedor');

    Route::post('/admin/solicitudes/aprobar/{id}',[AdminUserController::class,'aprobarSolicitud'])
    ->name('admin.solicitud.aprobar');

    Route::post('/admin/solicitudes/rechazar/{id}',[AdminUserController::class,'rechazarSolicitud'])
    ->name('admin.solicitud.rechazar');

});
