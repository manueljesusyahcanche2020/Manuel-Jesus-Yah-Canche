@extends('dashboard.welcome')

@section('contenido')

<div class="container py-4">

<h3 class="mb-4">📦 Inventario</h3>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
{{ session('error') }}
</div>
@endif


@if($bajo_stock->count() > 0)

<div class="alert alert-warning">

<strong>⚠ Productos con stock bajo</strong>

<ul class="mb-0 mt-2">

@foreach($bajo_stock as $item)

<li>
{{ $item->nombre }} (Stock: {{ $item->stock }})
</li>

@endforeach

</ul>

</div>

@endif


<div class="card shadow">

<div class="card-body">

<table class="table table-hover">

<thead class="table-dark">

<tr>
<th>Imagen</th>
<th>Producto</th>
<th>Precio</th>
<th>Stock actual</th>
<th>Agregar</th>
<th>Estado</th>
<th>Guardar</th>
</tr>

</thead>

<tbody>

@foreach($productos as $producto)

<tr>

<td>
<img
src="{{ asset('storage/'.$producto->imagen) }}"
width="60"
class="rounded"
>
</td>

<td>{{ $producto->nombre }}</td>

<td>${{ $producto->precio }}</td>

<td>
<strong>{{ $producto->stock }}</strong>
</td>

<td>

<form method="POST" action="{{ route('inventario.actualizar') }}">
@csrf

<input type="hidden" name="id" value="{{ $producto->id }}">

<input
type="number"
name="cantidad"
class="form-control"
style="width:90px"
placeholder="+ cantidad"
min="1"
@if($producto->sin_stock == 1) disabled @endif
required
>

</td>

<td>

@if($producto->sin_stock == 1)

<span class="badge bg-danger">
Agotado
</span>

@elseif($producto->stock <= 5)

<span class="badge bg-warning text-dark">
Stock bajo
</span>

@else

<span class="badge bg-success">
Disponible
</span>

@endif

</td>

<td>

<button 
class="btn btn-primary btn-sm"
@if($producto->sin_stock == 1) disabled @endif
>
Guardar
</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

@endsection