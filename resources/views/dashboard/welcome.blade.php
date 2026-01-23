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
<title>Dashboard - </title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
html, body {
    height: 100%;
    margin: 0;
    overflow: hidden; /* 🚫 No se puede mover ni hacer scroll */
    font-family: 'Segoe UI', Arial, sans-serif;
    background-color: #f5f5f5;
    color: #333;
}

/* Header */
.header {
    height: 60px;
    background-color: #ffcc00; /* amarillo Mercado Libre */
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 25px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 10;
}
.header .logo {
    font-size: 24px;
}
.header .auth {
    display: flex;
    gap: 15px;
    align-items: center;
}
.header .auth button {
    background-color: #fff;
    color: #ffcc00;
    border: none;
    border-radius: 25px;
    padding: 6px 18px;
    font-weight: bold;
    transition: all 0.3s ease;
}
.header .auth button:hover {
    background-color: #ffe680;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background-color: #fff;
    border-right: 1px solid #ddd;
    height: calc(100vh - 60px);
    position: fixed;
    top: 60px;
    left: 0;
    padding-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow-y: auto; /* Permite scroll solo dentro del menú si crece demasiado */
}
.sidebar a {
    display: flex;
    align-items: center;
    color: #555;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.2s ease;
}
.sidebar a i {
    margin-right: 12px;
    font-size: 16px;
}
.sidebar a:hover {
    background-color: #f0f0f0;
    color: #ff9900;
}

/* Contenido principal */
.main-content {
    margin-left: 220px;
    margin-top: 60px;
    padding: 30px;
    height: calc(100vh - 60px);
    overflow: hidden; /* 🚫 Contenido fijo sin scroll */
}
.main-content h2 {
    color: #ff9900;
    margin-bottom: 20px;
}

/* Cards */
.card-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}
.card {
    background-color: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
.card h5 {
    color: #333;
    margin-bottom: 10px;
}
.card p {
    color: #777;
    font-size: 14px;
}

/* Tabla de productos */
.table-wrapper {
    margin-top: 30px;
    background-color: #fff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.table-wrapper table {
    margin: 0;
}
</style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo"></div>

    <div class="auth" style="display: flex; align-items: center; gap: 10px;">
        @if (Auth::user()->role_id == 3)
            <a href="{{ route('carrito.ver') }}"
            class="btn btn-light position-relative"
            style="border-radius: 50%; width: 42px; height: 42px;
                    display: flex; align-items: center; justify-content: center;">
                <i class="fa fa-shopping-cart"></i>

                {{-- contador opcional --}}
                @if(session('carrito_cantidad', 0) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ session('carrito_cantidad') }}
                    </span>
                @endif
            </a>
        @endif


        <a href="{{ route('perfil') }}"
           style="display:flex; align-items:center; gap:8px; text-decoration:none; color:inherit;">

            @if(Auth::user()->imagen)
                <img src="{{ Auth::user()->imagen
                    ? asset('storage/' . Auth::user()->imagen)
                    : asset('img/default-user.png') }}"
                     alt="Foto de {{ Auth::user()->name }}"
                     style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
            @else
                <img src="{{ asset('img/default-user.png') }}"
                     alt="Usuario"
                     style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
            @endif

            <span>{{ Auth::user()->name }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>
</div>


<!-- Sidebar -->
<div class="sidebar">
    @foreach($menus as $item)
        @if(Route::has($item->ruta))
            <a href="{{ route($item->ruta) }}" class="menu-item">
                <i class="{{ $item->icono }}"></i>
                <span>{{ $item->nombre }}</span>
            </a>
        @endif
    @endforeach
</div>

<!-- Contenido principal -->
<div class="main-content">
    @yield('contenido') {{-- Aquí se inyectará el contenido de cada vista --}}
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@stack('scripts')


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
