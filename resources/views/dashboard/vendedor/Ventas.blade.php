@extends('dashboard.welcome')

@section('contenido')
<div class="container-fluid">

    <h4 class="mb-4 d-flex align-items-center gap-2" style="color:#ff9900;">
        <i class="fa-solid fa-chart-column"></i> Ventas
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

    <!-- ====== TABLA ====== -->
    <div class="card shadow-sm border-0 rounded-4 p-3">
        <h6 class="fw-bold mb-3">Detalle de Ventas</h6>

        <div class="table-responsive">
            <table id="tablaVentas" class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Ejemplo estático -->
                    <tr>
                        <td>1</td>
                        <td>Taco al Pastor</td>
                        <td>Comida</td>
                        <td>5</td>
                        <td>$250.00</td>
                        <td>15/12/2025</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Hamburguesa</td>
                        <td>Comida</td>
                        <td>3</td>
                        <td>$180.00</td>
                        <td>16/12/2025</td>
                    </tr>
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
/* ====== PALETA OFICIAL ====== */
const colores = {
    amarillo: '#ffcc00',
    amarilloOscuro: '#ff9900',
    grisTexto: '#333',
    grisLinea: '#ddd'
};

/* ====== CHART 1: Ventas por mes ====== */
new Chart(document.getElementById('ventasMes'), {
    type: 'line',
    data: {
        labels: ['Ene','Feb','Mar','Abr','May'],
        datasets: [{
            label: 'Ventas',
            data: [1200,1500,900,1800,2000],
            borderColor: colores.amarilloOscuro,
            backgroundColor: 'rgba(255,204,0,0.25)',
            pointBackgroundColor: colores.amarillo,
            pointBorderColor: colores.amarilloOscuro,
            borderWidth: 3,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: { color: colores.grisTexto }
            }
        },
        scales: {
            x: {
                ticks: { color: colores.grisTexto },
                grid: { display: false }
            },
            y: {
                ticks: { color: colores.grisTexto },
                grid: { color: colores.grisLinea }
            }
        }
    }
});

/* ====== CHART 2: Productos más vendidos ====== */
new Chart(document.getElementById('productosTop'), {
    type: 'bar',
    data: {
        labels: ['Tacos','Hamburguesa','Pizza'],
        datasets: [{
            label: 'Cantidad vendida',
            data: [40,25,15],
            backgroundColor: [
                colores.amarillo,
                colores.amarilloOscuro,
                '#ffe680'
            ],
            borderRadius: 6
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: { color: colores.grisTexto }
            }
        },
        scales: {
            x: {
                ticks: { color: colores.grisTexto },
                grid: { display: false }
            },
            y: {
                ticks: { color: colores.grisTexto },
                grid: { color: colores.grisLinea }
            }
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
