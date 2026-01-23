<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
//use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuComidasController;
use App\Http\Controllers\cliente\CarritoController;
use App\Http\Controllers\UserMenuController;
use App\models\User;

/*
|--------------------------------------------------------------------------
| Ruta principal
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Auth::check()
        ? redirect('/dashboard')
        : view('welcome');
});
Route::middleware('auth')->group(function () {

    Route::put('/perfil', [AuthController::class, 'update'])
        ->name('perfil.update');

});
Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard.welcome');
})->name('dashboard');

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

/*
|--------------------------------------------------------------------------
| Rutas CLIENTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Cliente'])->group(function () {

    Route::get('/cliente/menu', [MenuComidasController::class, 'index'])
        ->name('cliente.menu');

    Route::get('/cliente/menu/listar', [MenuComidasController::class, 'listar']);
    Route::get('/cliente/menu/buscar', [MenuComidasController::class, 'buscar']);

    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.ver');
    Route::get('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::delete('/carrito/item/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
    Route::post('/carrito/actualizar', [CarritoController::class, 'actualizar']);
    Route::get('/carrito/listar', [CarritoController::class, 'listar']);
    Route::get('/carrito/pago', [CarritoController::class, 'pago']);

});

//perfil logeado
Route::middleware('auth')->get('/perfil', function () {
    return view('dashboard.perfil');
})->name('perfil');


/*
|--------------------------------------------------------------------------
| Rutas VENDEDOR
|--------------------------------------------------------------------------
*/
Route::get('/ajax/categorias', function () {
    return \App\Models\Categoria::select('id', 'nombre')->get();
})->name('ajax.categorias');
use App\Http\Controllers\administrador\VentasController;
Route::middleware(['auth', 'role:Vendedor'])->group(function () {

    Route::get('/vendedor/productos/crear', function () {
        return view('dashboard.vendedor.productos.crear');
    })->name('vendedor.productos.crear');

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

    Route::get('/vendedor/ventas', [VentasController::class, 'ventas'])
        ->name('vendedor.ventas');

    Route::get('/vendedor/pedidos', [VentasController::class, 'pedidos'])
        ->name('vendedor.pedidos');

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


});
