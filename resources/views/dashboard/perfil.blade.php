@extends('dashboard.welcome')

@section('contenido')
<div class="container mt-4">

    <h4 class="mb-4">Mi Perfil</h4>

    <div class="card shadow-sm">
        <div class="row g-0">

            <!-- PANEL IZQUIERDO (FOTO) -->
            <div class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center p-4">

                <img src="{{ Auth::user()->imagen
                    ? asset('storage/' . Auth::user()->imagen)
                    : asset('img/default-user.png') }}"
                     class="rounded-circle mb-3"
                     width="140" height="140"
                     style="object-fit:cover">

                <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                <small class="text-muted">{{ Auth::user()->email }}</small>

            </div>

            <!-- PANEL DERECHO (FORMULARIO) -->
            <div class="col-md-8">
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('perfil.update') }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input class="form-control"
                                   name="name"
                                   value="{{ Auth::user()->name }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo</label>
                            <input class="form-control"
                                   type="email"
                                   name="email"
                                   value="{{ Auth::user()->email }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input class="form-control"
                                   type="password"
                                   name="password"
                                   placeholder="Opcional">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input class="form-control"
                                   type="password"
                                   name="password_confirmation">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto de perfil</label>
                            <input class="form-control"
                                   type="file"
                                   name="imagen">
                        </div>

                        <button class="btn btn-primary">
                            Guardar cambios
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
