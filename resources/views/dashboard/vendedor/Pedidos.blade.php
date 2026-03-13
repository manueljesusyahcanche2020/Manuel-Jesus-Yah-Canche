@extends('dashboard.welcome')

@section('contenido')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="container-fluid py-4">

<h4 class="mb-4 fw-bold d-flex align-items-center gap-2" style="color:#ff9900;">
<i class="fa-solid fa-clipboard-list"></i> Pedidos Recibidos
</h4>

<div class="card border-0 shadow-lg rounded-4 p-4">

<h6 class="fw-bold mb-3 border-bottom pb-2">
Listado de Pedidos
</h6>

<div class="table-responsive">

<table id="tablaPedidos" class="table table-hover align-middle">

<thead class="table-dark">
<tr>
<th>#</th>
<th>Cliente</th>
<th>Producto</th>
<th>Categoría</th>
<th>Cant.</th>
<th>Subtotal</th>
<th>Estado</th>
<th>Fecha</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>

@foreach($pedidos as $pedidoId => $itemsPedido)

@php
$cliente = $itemsPedido->first()->cliente ?? 'Usuario eliminado';
$estadoPedido = $itemsPedido->first()->estado ?? 'pendiente';

$estadoColor = [
'pendiente' => 'secondary',
'enviado' => 'info',
'entregado' => 'success',
'cancelado' => 'danger'
];
@endphp

@foreach($itemsPedido as $item)

<tr>

<td class="fw-semibold">
#{{ $pedidoId }}
</td>

<td>
{{ $cliente }}
</td>

<td>
{{ $item->producto }}
</td>

<td>
{{ $item->categoria ?? '-' }}
</td>

<td>
{{ $item->cantidad }}
</td>

<td class="fw-semibold">
${{ number_format($item->subtotal,2) }}
</td>

<td>
<span class="badge bg-{{ $estadoColor[$estadoPedido] ?? 'secondary' }}">
{{ ucfirst($estadoPedido) }}
</span>
</td>

<td>
{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
</td>

<td>

<div class="d-flex gap-2">

<a href="{{ route('vendedor.factura',$pedidoId) }}"
class="btn btn-sm btn-primary rounded-pill">
<i class="fa-solid fa-file-invoice"></i>
</a>

<button class="btn btn-sm btn-warning rounded-pill"
data-bs-toggle="modal"
data-bs-target="#modalEstado{{ $pedidoId }}">
<i class="fa-solid fa-truck-fast"></i>
</button>

</div>

</td>

</tr>

@endforeach

{{-- MODAL POR PEDIDO --}}

<div class="modal fade" id="modalEstado{{ $pedidoId }}" tabindex="-1">

<div class="modal-dialog modal-dialog-centered">

<div class="modal-content rounded-4 shadow">

<form method="POST" action="{{ route('vendedor.cambiarEstado') }}">

@csrf

<input type="hidden" name="pedido_id" value="{{ $pedidoId }}">

<div class="modal-header">

<h5 class="modal-title fw-bold">
<i class="fa-solid fa-truck-fast text-warning"></i>
Cambiar estado del pedido
</h5>

<button class="btn-close" data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<label class="form-label fw-semibold">
Seleccionar estado
</label>

<select name="estado" class="form-select rounded-3" required>

<option value="pendiente">🟡 Pendiente</option>
<option value="enviado">🚚 Enviado</option>
<option value="entregado">✅ Entregado</option>
<option value="cancelado">❌ Cancelado</option>

</select>

</div>

<div class="modal-footer">

<button class="btn btn-secondary rounded-pill"
data-bs-dismiss="modal">
Cancelar
</button>

<button type="submit"
class="btn btn-warning rounded-pill">
Guardar cambios
</button>

</div>

</form>

</div>
</div>
</div>

@endforeach

</tbody>

</table>

</div>
</div>

</div>


{{-- DATATABLE --}}

<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>

$(document).ready(function(){

$('#tablaPedidos').DataTable({

language:{
url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
},

pageLength:10

});

});

</script>

@endsection