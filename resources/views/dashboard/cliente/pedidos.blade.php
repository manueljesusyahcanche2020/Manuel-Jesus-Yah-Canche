@extends('dashboard.welcome')

@section('contenido')
<div class="container mt-4">
    <h2 class="mb-4 text-primary">Pedidos Realizados</h2>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>03/10/2025</td>
                    <td>$120.00</td>
                    <td><span class="badge bg-success">Entregado</span></td>
                    <td>
                        <button class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>02/10/2025</td>
                    <td>$90.00</td>
                    <td><span class="badge bg-warning text-dark">En camino</span></td>
                    <td>
                        <button class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Opcional: destacar la sección con una tarjeta -->
    <div class="mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Resumen de pedidos</h5>
                <p class="card-text">Aquí puedes revisar todos los pedidos realizados, su estado y detalles.</p>
            </div>
        </div>
    </div>
</div>

<style>
    table.table-hover tbody tr:hover {
        background-color: #f1f9ff;
        transition: 0.2s;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }
    .card-title {
        color: #0d6efd;
        font-weight: bold;
    }
</style>
@endsection
