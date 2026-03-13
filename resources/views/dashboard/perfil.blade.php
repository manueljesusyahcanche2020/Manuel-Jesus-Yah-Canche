@extends('dashboard.welcome')

@section('contenido')

<style>

/* ANIMACIÓN GENERAL */
.anim-seccion{
opacity:0;
transform:translateY(20px);
animation:aparecer .6s ease forwards;
}

.anim-delay-1{ animation-delay:.2s;}
.anim-delay-2{ animation-delay:.4s;}
.anim-delay-3{ animation-delay:.6s;}
.anim-delay-4{ animation-delay:.8s;}

@keyframes aparecer{
to{
opacity:1;
transform:translateY(0);
}
}

/* DIRECCIONES */
.direccion-card{
animation:fadeCard .4s ease;
}

@keyframes fadeCard{
from{
opacity:0;
transform:scale(.95);
}
to{
opacity:1;
transform:scale(1);
}
}

/* skeleton loader */

.skeleton{
background:linear-gradient(90deg,#eee,#f5f5f5,#eee);
background-size:200% 100%;
animation:skeleton 1.2s infinite;
height:60px;
border-radius:8px;
}

@keyframes skeleton{
0%{background-position:200% 0}
100%{background-position:-200% 0}
}

</style>

<div class="container mt-4">

<h4 class="mb-4 fw-bold anim-seccion">Mi Perfil</h4>

<div class="card shadow-sm anim-seccion anim-delay-1">
<div class="row g-0">

<!-- FOTO PERFIL -->

<div class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center p-4 border-end anim-seccion anim-delay-2">

<form method="POST" action="{{ route('perfil.foto') }}" enctype="multipart/form-data">
@csrf

<label for="imagenInput">

<img src="{{ Auth::user()->imagen ? asset('storage/'.Auth::user()->imagen) : asset('img/default-user.png') }}"
class="rounded-circle shadow-sm"
width="130"
height="130"
style="object-fit:cover;cursor:pointer;transition:.3s">

</label>

<input type="file" name="imagen" id="imagenInput" class="d-none" onchange="this.form.submit()">

</form>

<h5 class="mb-0 text-center mt-3">{{ Auth::user()->name }}</h5>

<small class="text-muted">{{ Auth::user()->email }}</small>

@if(Auth::user()->telefono)
<small class="text-muted mt-2">📞 {{ Auth::user()->telefono }}</small>
@endif

</div>

<!-- DATOS PERFIL -->

<div class="col-md-8">
<div class="card-body p-4 anim-seccion anim-delay-3">

@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">
{{ session('success') }}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

@endif

<form method="POST" action="{{ route('perfil.update') }}">
@csrf
@method('PUT')

<h5 class="mb-3 text-primary">Datos personales</h5>

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label small fw-bold">Nombre</label>
<input class="form-control" name="name" value="{{ Auth::user()->name }}" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label small fw-bold">Correo</label>
<input class="form-control" type="email" name="email" value="{{ Auth::user()->email }}" required>
</div>

</div>

<div class="mb-3">
<label class="form-label small fw-bold">Número de Teléfono</label>
<input class="form-control" name="telefono" value="{{ Auth::user()->telefono }}">
</div>

<hr>

<h5 class="mb-3 text-primary">Seguridad</h5>

<div class="row">

<div class="col-md-6 mb-3">
<label class="form-label small fw-bold">Nueva contraseña</label>
<input class="form-control" type="password" name="password" placeholder="Dejar en blanco para no cambiar">
</div>

<div class="col-md-6 mb-3">
<label class="form-label small fw-bold">Confirmar contraseña</label>
<input class="form-control" type="password" name="password_confirmation">
</div>

</div>

<button class="btn btn-primary px-4">Actualizar Perfil</button>

</form>

<hr class="my-5">

<!-- DIRECCIONES -->

<h5 class="mb-3">📍 Mis direcciones</h5>

<div id="listaDirecciones" class="mb-4"></div>

<div class="card bg-light border-0 anim-seccion anim-delay-4">

<div class="card-body">

<h6 class="mb-3">Agregar / Editar dirección</h6>

<form id="formDireccion">

@csrf

<div class="mb-3">
<input class="form-control form-control-sm" name="calle" placeholder="Calle y número" required>
</div>

<div class="mb-3">
<input class="form-control form-control-sm" name="colonia" placeholder="Colonia" required>
</div>

<div class="mb-3">
<textarea class="form-control form-control-sm" name="referencia" rows="2" placeholder="Referencias adicionales"></textarea>
</div>

<div class="mb-3">
<input class="form-control form-control-sm" name="nombre_direccion" placeholder="Nombre de la dirección">
</div>

<div class="row g-2 mb-3">

<div class="col-6">
<input class="form-control form-control-sm" name="ciudad" value="Peto" readonly>
</div>

<div class="col-6">
<input class="form-control form-control-sm" name="estado" value="Yucatán" readonly>
</div>

</div>

<button type="submit" class="btn btn-success btn-sm w-100">

<span id="btnText">➕ Guardar dirección</span>
<span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>

</button>

</form>

</div>
</div>

</div>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

let direccionEditando=null;

$(document).ready(function(){

cargarDirecciones();

});

function cargarDirecciones(){

$('#listaDirecciones').html(`
<div class="skeleton mb-2"></div>
<div class="skeleton mb-2"></div>
<div class="skeleton"></div>
`);

$.get("{{ route('direccion.index') }}",function(data){

let html='';

if(data.length===0){

html='<div class="alert alert-light text-center border">Aún no has registrado direcciones.</div>';

}else{

data.forEach(function(d){

html+=`

<div class="card mb-2 border-start border-primary border-4 shadow-sm direccion-card">

<div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">

<div>

<strong>${d.calle}</strong>

<small class="d-block text-muted">${d.colonia}, ${d.ciudad}</small>

</div>

<button class="btn btn-sm btn-warning"
onclick="editarDireccion(${d.id},'${d.calle}','${d.colonia}','${d.referencia ?? ''}','${d.nombre_direccion ?? ''}')">

✏️

</button>

</div>

</div>

`;

});

}

$('#listaDirecciones').html(html);

});

}

function editarDireccion(id,calle,colonia,referencia,nombre){

direccionEditando=id;

$('input[name="calle"]').val(calle);
$('input[name="colonia"]').val(colonia);
$('textarea[name="referencia"]').val(referencia);
$('input[name="nombre_direccion"]').val(nombre);

$('#btnText').text('Actualizar dirección');

}

$('#formDireccion').submit(function(e){

e.preventDefault();

let url=direccionEditando
? "{{ url('/direcciones') }}/"+direccionEditando
: "{{ route('direccion.store') }}";

let datos=$(this).serialize();

if(direccionEditando){
datos+='&_method=PUT';
}

$('#btnSpinner').removeClass('d-none');

$.ajax({

url:url,
method:"POST",
data:datos,

success:function(){

$('#formDireccion')[0].reset();

direccionEditando=null;

$('#btnText').text('➕ Guardar dirección');

$('#btnSpinner').addClass('d-none');

cargarDirecciones();

}

});

});

</script>

@endsection