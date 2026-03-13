@extends('dashboard.welcome')

@section('contenido')

<div class="container mt-4">

<h3 class="mb-4">Vendedores disponibles</h3>

<div class="row">

@forelse($vendedores as $v)

<div class="col-md-4 mb-4">
    <div class="card shadow-sm h-100">

        <div class="text-center pt-3">
            <img src="{{ $v->imagen ? asset('storage/'.$v->imagen) : asset('img/default.png') }}"
                 width="90"
                 height="90"
                 class="rounded-circle border">
        </div>

        <div class="card-body text-center">

            <h5 class="card-title">{{ $v->name }}</h5>

            <p class="text-danger">
                Pedido mínimo: ${{ number_format($v->minimo_pedido,2) }}
            </p>

            <a href="{{ route('cliente.catalogo.vendedor',$v->id) }}"
               class="btn btn-warning w-100">
                Ver catálogo
            </a>

        </div>

    </div>
</div>

@empty
<p class="text-muted text-center">No hay vendedores disponibles.</p>
@endforelse

</div>

</div>

@endsection