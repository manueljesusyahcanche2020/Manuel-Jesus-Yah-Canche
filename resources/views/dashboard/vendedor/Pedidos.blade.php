@extends('dashboard.welcome')

@section('contenido')
<div class="container-fluid">

    <h4 class="mb-4 d-flex align-items-center gap-2" style="color:#ff9900;">
        <i class="fa-solid fa-clipboard-list"></i> Pedidos
    </h4>

    <!-- ====== TABLA DE PEDIDOS ====== -->
    <div class="card shadow-sm border-0 rounded-4 p-3">

        <h6 class="fw-bold mb-3">Listado de Pedidos</h6>

        <div class="table-responsive">
            <table id="tablaPedidos" class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th># Pedido</th>
                        <th>Comprador</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- PEDIDO 1 -->
                    <tr>
                        <td>101</td>
                        <td>
                            <strong>Juan Pérez</strong><br>
                            <small class="text-muted">juan@gmail.com</small>
                        </td>
                        <td>
                            <ul class="mb-0 ps-3">
                                <li>2 × Taco al Pastor</li>
                                <li>1 × Hamburguesa</li>
                            </ul>
                        </td>
                        <td>$320.00</td>
                        <td>
                            <span class="badge bg-secondary">Pendiente</span>
                        </td>
                        <td>18/12/2025</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-estado"
                                    data-id="101"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEstado">
                                <i class="fa fa-pen"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- PEDIDO 2 -->
                    <tr>
                        <td>102</td>
                        <td>
                            <strong>Ana López</strong><br>
                            <small class="text-muted">ana@gmail.com</small>
                        </td>
                        <td>
                            <ul class="mb-0 ps-3">
                                <li>3 × Papas a la francesa</li>
                            </ul>
                        </td>
                        <td>$99.00</td>
                        <td>
                            <span class="badge bg-warning text-dark">En preparación</span>
                        </td>
                        <td>17/12/2025</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-estado"
                                    data-id="102"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEstado">
                                <i class="fa fa-pen"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== MODAL ESTADO ===== -->
<div class="modal fade" id="modalEstado" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-truck-fast text-warning"></i>
                    Cambiar estado del pedido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="pedidoId">

                <div class="mb-3">
                    <label class="form-label fw-bold">Estado</label>
                    <select id="nuevoEstado" class="form-select">
                        <option value="preparacion">🟡 En preparación</option>
                        <option value="reparto">🚚 En reparto</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-warning" id="guardarEstado">
                    Guardar estado
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ====== DATATABLE ====== --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
let filaActual = null;

$(document).ready(function () {
    $('#tablaPedidos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        pageLength: 10
    });
});

$(document).on('click', '.btn-estado', function () {
    filaActual = $(this).closest('tr');
    $('#pedidoId').val($(this).data('id'));
});

$('#guardarEstado').on('click', function () {
    const estado = $('#nuevoEstado').val();
    let badge = '';

    if (estado === 'preparacion') {
        badge = '<span class="badge bg-warning text-dark">En preparación</span>';
    } else {
        badge = '<span class="badge bg-info text-dark">En reparto</span>';
    }

    filaActual.find('td:eq(4)').html(badge);
    $('#modalEstado').modal('hide');
});
</script>
@endsection
