@extends('dashboard.welcome')

@section('contenido')

<div class="container mt-4">

<h3>Configurar PayPal</h3>

<form method="POST" action="{{ route('vendedor.pagos.guardar') }}">

@csrf

<div class="mb-3">
<label>Correo PayPal</label>
<input type="email"
       name="paypal_email"
       class="form-control"
       value="{{ $pago->paypal_email ?? '' }}">
</div>

<div class="mb-3">
<label>Client ID</label>
<input type="text"
       name="paypal_client_id"
       class="form-control"
       value="{{ $pago->paypal_client_id ?? '' }}">
</div>

<div class="mb-3">
<label>Secret</label>
<input type="text"
       name="paypal_secret"
       class="form-control"
       value="{{ $pago->paypal_secret ?? '' }}">
</div>

<button class="btn btn-primary">
Guardar configuración
</button>

</form>

</div>

@endsection