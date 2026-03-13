@extends('dashboard.welcome')

@section('contenido')

<div class="container mt-4">

@php
$primero = $menu[0] ?? null;
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex align-items-center p-3">
        <div class="rounded-circle border d-flex align-items-center justify-content-center bg-white" style="width: 60px; height: 60px; overflow: hidden; flex-shrink: 0;">
            <img src="{{ $vendedor->imagen ? asset('storage/'.$vendedor->imagen) : asset('default-logo.png') }}" 
                alt="Logo" style="object-fit: contain; width: 70%;">
        </div>
        
        <div class="ms-3">
            <div class="d-flex align-items-center mb-0">
                <span class="text-primary me-1" style="font-size: 0.9rem;"><i class="bi bi-patch-check-fill"></i></span>
                <small class="text-muted fw-bold" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Tienda oficial</small>
            </div>
            <h3 class="mb-0 fw-bold" style="color: #333;">{{ $primero->vendedor ?? 'Vendedor' }}</h3>
        </div>
    </div>
</div>

@if($primero)
<div class="alert alert-warning mb-4 border-0 shadow-sm">
    Compra mínima: <strong>${{ number_format($primero->minimo_pedido,2) }}</strong>
</div>
@endif

<h4 class="mb-4 fw-normal text-secondary">Catálogo de productos</h4>

<div class="row">

@forelse($menu as $c)

<div class="col-md-4 mb-4">

    <div class="card h-100 shadow-sm">

        <img src="{{ asset('storage/'.$c->imagen) }}"
             class="card-img-top"
             style="height:200px;object-fit:contain;background:#f8f9fa;padding:10px;">

        <div class="card-body">

            <h5>{{ $c->nombre }}</h5>

            <p class="text-muted">
                {{ $c->descripcion }}
            </p>

            <h6 class="text-success">
                ${{ number_format($c->precio,2) }}
            </h6>

            <a href="/carrito/agregar/{{ $c->id }}"
               class="btn btn-warning w-100 fw-bold">
                Agregar al pedido
            </a>

        </div>

    </div>

</div>

@empty
<div class="col-12">
    <p class="text-center text-muted">Este vendedor aún no tiene productos.</p>
</div>
@endforelse

</div>

</div>

@endsection