@extends('dashboard.welcome')

@section('title', 'Respaldo de Base de Datos')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card shadow border-0 mb-4">
            <div class="card-header fw-bold"
                 style="background-color:#facc15; color:#1f2937;">
                <i class="fa fa-database me-2"></i>
                Respaldo de la Base de Datos
            </div>

            <div class="card-body text-center">

                {{-- CREAR RESPALDO --}}
                <form action="{{ route('backup.create') }}" method="POST" class="mb-4">
                    @csrf
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fa fa-download me-2"></i>
                        Crear respaldo
                    </button>
                </form>

                <hr>

                {{-- SUBIR RESPALDO --}}
                <form action="{{ route('backup.restore') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      onsubmit="return confirm('⚠️ Esto reemplazará la base de datos actual. ¿Continuar?')">
                    @csrf

                    <div class="mb-3">
                        <input type="file"
                               name="sql_file"
                               class="form-control"
                               accept=".sql"
                               required>
                    </div>

                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fa fa-upload me-2"></i>
                        Subir y restaurar respaldo
                    </button>
                </form>

                {{-- MENSAJES --}}
                @if(session('success'))
                    <div class="alert alert-success mt-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mt-4">
                        {{ session('error') }}
                    </div>
                @endif

            </div>
        </div>

    </div>
    
</div>

@endsection
