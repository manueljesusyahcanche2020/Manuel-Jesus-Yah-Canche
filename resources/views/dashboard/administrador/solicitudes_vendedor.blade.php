@extends('dashboard.welcome')

@section('contenido')

<div class="container py-4">

    <h3 class="mb-4">📋 Solicitudes para ser vendedor</h3>

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

    <div class="card shadow">

        <div class="card-body">

            <table class="table table-hover">

                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr>
                            <td>{{ $solicitud->id_solicitud }}</td>
                            <td>{{ $solicitud->nombre_del_solicitante }}</td>
                            <td>{{ $solicitud->motivo }}</td>
                            <td>
                                @if($solicitud->estado == 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($solicitud->estado == 'aprobado')
                                    <span class="badge bg-success">Aprobado</span>
                                @else
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </td>
                            <td>{{ $solicitud->created_at }}</td>
                            <td>
                                @if($solicitud->estado == 'pendiente')
                                    <form action="{{ route('admin.solicitud.aprobar', $solicitud->id_solicitud) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm me-1">✔ Aprobar</button>
                                    </form>

                                    <form action="{{ route('admin.solicitud.rechazar', $solicitud->id_solicitud) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">✖ Rechazar</button>
                                    </form>
                                @else
                                    <span class="text-muted">No disponible</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay solicitudes pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection