<?php 
$menus = Auth::user()
    ->role
    ->menus()
    ->orderBy('orden')
    ->get();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
html, body {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background-color: #f5f5f5;
    overflow-x: hidden;
}

/* HEADER */
.header {
    height: 60px;
    background-color: #ffcc00;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    z-index: 1000;
}

.logo {
    font-weight: bold;
    font-size: 20px;
}

.header .auth {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header button {
    background: #fff;
    border: none;
    padding: 6px 15px;
    border-radius: 20px;
    font-weight: bold;
}

/* SIDEBAR */
.sidebar {
    width: 230px;
    background: #fff;
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    border-right: 1px solid #ddd;
    padding-top: 15px;
    overflow-y: auto;
    transition: transform 0.3s ease;
}

.sidebar a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    transition: 0.2s;
}

.sidebar a i {
    margin-right: 10px;
}

.sidebar a:hover {
    background: #f0f0f0;
    color: #ff9900;
}

/* CAMBIO DE ROL */
.role-box{
    padding:15px;
    border-top:1px solid #eee;
    margin-top:10px;
}

/* CONTENIDO */
.main-content {
    margin-top: 60px;
    margin-left: 230px;
    padding: 30px;
    min-height: calc(100vh - 60px);
}

@media (max-width: 992px) {

    .sidebar {
        transform: translateX(-100%);
        z-index: 1100;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    /* Clase para el menú activo */
    .sidebar a.active {
        background-color: #fef0b3; /* Un amarillo suave */
        color: #ff9900;            /* Color de texto énfasis */
        border-left: 4px solid #ff9900;
        font-weight: bold;
    }

}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">

<button class="btn btn-light d-lg-none" id="toggleSidebar">
<i class="fa fa-bars"></i>
</button>

<div class="logo">TU Negocio Online</div>

<div class="auth">

@if (Auth::user()->role_id == 3)
<a href="{{ route('carrito.ver') }}"
class="btn btn-light position-relative"
style="border-radius:50%; width:42px; height:42px;
display:flex; align-items:center; justify-content:center;">
<i class="fa fa-shopping-cart"></i>

@if(session('carrito_cantidad', 0) > 0)
<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
{{ session('carrito_cantidad') }}
</span>
@endif
</a>
@endif

<a href="{{ route('perfil') }}"
style="display:flex; align-items:center; gap:8px; text-decoration:none; color:inherit;">

<img src="{{ Auth::user()->imagen 
? asset('storage/' . Auth::user()->imagen) 
: asset('img/default-user.png') }}"
style="width:40px; height:40px; border-radius:50%; object-fit:cover;">

<span>{{ Auth::user()->name }}</span>
</a>

<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit">Cerrar sesión</button>
</form>

</div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    @foreach($menus as $item)
        @if(Route::has($item->ruta))
            <a href="{{ route($item->ruta) }}" 
               class="{{ Route::is($item->ruta) ? 'active' : '' }}">
                <i class="{{ $item->icono }}"></i>
                <span>{{ $item->nombre }}</span>
            </a>
        @endif
    @endforeach

</div>
<!-- CONTENIDO -->
<div class="main-content">
@section('contenido')

<div class="card shadow-sm border-0 rounded-4 mb-4">
<div class="card-body">
<h3 class="fw-bold">Bienvenido a Mi Plataforma</h3>
<p class="text-muted">
Administra pedidos, usuarios y productos desde este panel.
</p>
</div>
</div>

<div class="row">

<div class="col-md-4">
<div class="card border-0 shadow-sm rounded-4 p-3">
<h5 class="fw-bold">📦 Gestión de Pedidos</h5>
<p class="text-muted small">
Controla pedidos en tiempo real y actualiza su estado fácilmente.
</p>
</div>
</div>

<div class="col-md-4">
<div class="card border-0 shadow-sm rounded-4 p-3">
<h5 class="fw-bold">👥 Gestión de Usuarios</h5>
<p class="text-muted small">
Administra roles y accesos del sistema.
</p>
</div>
</div>

<div class="col-md-4">
<div class="card border-0 shadow-sm rounded-4 p-3">
<h5 class="fw-bold">📊 Reportes</h5>
<p class="text-muted small">
Visualiza estadísticas y rendimiento del negocio.
</p>
</div>
</div>

</div>

@show
</div>

<script>
document.getElementById("toggleSidebar").addEventListener("click", function() {
document.getElementById("sidebar").classList.toggle("active");
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>