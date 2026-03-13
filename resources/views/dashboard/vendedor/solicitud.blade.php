@extends('dashboard.welcome')
@section('contenido')

<div class="container py-4">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow-lg border-0">

<div class="card-header bg-warning text-dark">
<h4 class="mb-0">🛍️ Convertirse en Vendedor</h4>
</div>

<div class="card-body">

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

@if($solicitudPendiente)

<div class="alert alert-info">

<h5>📋 Solicitud enviada</h5>

Tu solicitud para convertirte en vendedor ya está en revisión.
Cuando el administrador la apruebe podrás acceder al panel de vendedor.
<br>
Estado:
<span class="badge bg-warning">{{ $solicitudPendiente->estado }}</span>
</div>

@else

<p class="text-muted">
Al convertirte en vendedor podrás acceder a herramientas para gestionar tu negocio dentro de la plataforma.
</p>

<div class="mb-3">

<ul class="list-group list-group-flush">

<li class="list-group-item">
📦 Publicar y administrar tus productos
</li>

<li class="list-group-item">
📋 Gestionar pedidos de tus clientes
</li>

</ul>

</div>

<hr>

<form method="POST" action="{{ route('vendedor.cambiar') }}">
@csrf

<div class="mb-3">

<label class="form-label fw-bold">
¿Que planeas vender?
</label>

<textarea
name="motivo"
class="form-control"
rows="4"
placeholder="Describe brevemente los productos o servicios que deseas vender..."
required
></textarea>

</div>

<div class="d-grid">

<button class="btn btn-warning btn-lg">
🚀 Enviar solicitud para ser vendedor
</button>

</div>

</form>

@endif

</div>

</div>

</div>

</div>

</div>

@endsection
