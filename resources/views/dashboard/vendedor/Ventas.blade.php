@extends('dashboard.welcome')

@section('contenido')
<div class="container-fluid">

    <h4 class="mb-4 d-flex align-items-center gap-2" style="color:#ff9900;">
        <i class="fa-solid fa-chart-column"></i> Mis Ventas Completadas
    </h4>

    <!-- ====== GRÁFICAS ====== -->
    <div class="row mb-4">

        <!-- Ventas por mes -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="fw-bold mb-3">Ventas por Mes</h6>
                <canvas id="ventasMes" height="120"></canvas>
            </div>
        </div>

        <!-- Productos más vendidos -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="fw-bold mb-3">Productos más vendidos</h6>
                <canvas id="productosTop" height="120"></canvas>
            </div>
        </div>

    </div>

    <!-- ====== GRÁFICAS ADICIONALES ====== -->
    <div class="row mb-4">

        <!-- Ventas por Categoría -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="fw-bold mb-3">Ventas por Categoría</h6>
                <canvas id="ventasCategoria" height="120"></canvas>
            </div>
        </div>

        <!-- Clientes Top -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <h6 class="fw-bold mb-3">Clientes Top</h6>
                <canvas id="clientesTop" height="120"></canvas>
            </div>
        </div>

    </div>

    <!-- ====== TABLA DETALLE DE VENTAS ====== -->
    <div class="card shadow-sm border-0 rounded-4 p-3">
        <h6 class="fw-bold mb-3">Detalle de Ventas Completadas</h6>

        <div class="table-responsive">
            <table id="tablaVentas" class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Cliente</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $item)
                    <tr>
                        <td>{{ $item->pedido->user->name ?? 'Usuario eliminado' }}</td>
                        <td>{{ $item->menu->nombre ?? 'Producto eliminado' }}</td>
                        <td>{{ $item->menu->categoria->nombre ?? '-' }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>${{ number_format($item->subtotal,2) }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ====== LIBRERÍAS ====== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
const colores = {
    amarillo: '#ffcc00',
    naranja: '#ff9900',
    grisTexto: '#333',
    grisLinea: '#ddd'
};

/* ====== CHART 1: Ventas por Mes ====== */
new Chart(document.getElementById('ventasMes'), {
    type: 'line',
    data: {
        labels: @json(array_keys($ventasPorMes->toArray())),
        datasets: [{
            label: 'Ventas',
            data: @json(array_values($ventasPorMes->toArray())),
            borderColor: colores.naranja,
            backgroundColor: 'rgba(255,204,0,0.25)',
            pointBackgroundColor: colores.amarillo,
            pointBorderColor: colores.naranja,
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        plugins: { legend: { labels: { color: colores.grisTexto } } },
        scales: {
            x: { ticks: { color: colores.grisTexto }, grid: { display: false } },
            y: { ticks: { color: colores.grisTexto }, grid: { color: colores.grisLinea } }
        }
    }
});

/* ====== CHART 2: Productos Top ====== */
new Chart(document.getElementById('productosTop'), {
    type: 'bar',
    data: {
        labels: @json(array_keys($productosTop->toArray())),
        datasets: [{
            label: 'Cantidad vendida',
            data: @json(array_values($productosTop->toArray())),
            backgroundColor: [colores.amarillo, colores.naranja, '#ffe680', '#ffd633', '#ffbb00'],
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { labels: { color: colores.grisTexto } } },
        scales: {
            x: { ticks: { color: colores.grisTexto }, grid: { display: false } },
            y: { ticks: { color: colores.grisTexto }, grid: { color: colores.grisLinea } }
        }
    }
});

/* ====== CHART 3: Ventas por Categoría ====== */
new Chart(document.getElementById('ventasCategoria'), {
    type: 'doughnut',
    data: {
        labels: @json(array_keys($ventasPorCategoria->toArray())),
        datasets: [{
            data: @json(array_values($ventasPorCategoria->toArray())),
            backgroundColor: ['#ffcc00','#ff9900','#ffe680','#ffd633','#ffbb00','#ffdd99'],
            borderWidth: 1
        }]
    },
    options: {
        plugins: { legend: { labels: { color: colores.grisTexto } } }
    }
});

/* ====== CHART 4: Clientes Top ====== */
new Chart(document.getElementById('clientesTop'), {
    type: 'bar',
    data: {
        labels: @json(array_keys($clientesTop->toArray())),
        datasets: [{
            label: 'Total Comprado',
            data: @json(array_values($clientesTop->toArray())),
            backgroundColor: [colores.amarillo, colores.naranja, '#ffe680', '#ffd633', '#ffbb00'],
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { labels: { color: colores.grisTexto } } },
        scales: {
            x: { ticks: { color: colores.grisTexto }, grid: { display: false } },
            y: { ticks: { color: colores.grisTexto }, grid: { color: colores.grisLinea } }
        }
    }
});

/* ====== DATATABLE ====== */
$(document).ready(function () {
    $('#tablaVentas').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10
    });
});
</script>
@endsection