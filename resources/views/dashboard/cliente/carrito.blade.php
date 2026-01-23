@extends('dashboard.welcome')

@section('contenido')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-4">
    <h2 class="mb-3">🛒 Tu Carrito</h2>

    <div id="contenedorCarrito" class="table-responsive">
        <p class="text-center text-muted">Cargando carrito...</p>
    </div>

    <div class="mt-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPago">
            Proceder al pago
        </button>
    </div>
</div>

{{-- ================= MODAL DE PAGO ================= --}}
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Opciones de Pago</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- BOTONES DE PAGO --}}
                <div class="d-grid gap-2 mb-3">
                    <button class="btn btn-primary" id="entregaDomicilio">
                        🚚 Entrega a domicilio (Pago al recibir)
                    </button>
                    <button class="btn btn-warning" id="pagoPrepagado">
                        💳 Pago prepagado
                    </button>
                </div>

                {{-- DIRECCIÓN --}}
                <div class="mb-3">
                    <label class="form-label">Dirección de entrega</label>
                    <input type="text" id="direccion" class="form-control">
                </div>

                {{-- AVISO ENTREGA --}}
                <div id="avisoEntrega" class="alert alert-info d-none">
                    <strong>Pago contra entrega</strong><br>
                    • El pago se realiza al recibir el pedido.<br>
                    • Ten el monto exacto disponible.<br>
                    • Tiempo estimado: 30–60 minutos.
                </div>

                {{-- FORMULARIO PREPAGO --}}
                <div id="formPrepago" class="border rounded p-3 d-none">
                    <h6 class="mb-3">Datos de pago</h6>

                    <div class="mb-2">
                        <label class="form-label">Método</label>
                        <select class="form-select" id="metodoPrepago">
                            <option value="">Selecciona</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Referencia / últimos 4 dígitos</label>
                        <input type="text" class="form-control" id="referenciaPago">
                    </div>

                    <div class="alert alert-success mt-2">
                        El pago será validado antes de preparar el pedido.
                    </div>
                </div>

                <hr>
                <p class="fs-5">
                    <strong>Total:</strong> $<span id="totalModal">0.00</span>
                </p>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" id="confirmarPago">Confirmar pedido</button>
            </div>

        </div>
    </div>
</div>

{{-- ================= JS ================= --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {

    const contenedor = $('#contenedorCarrito');
    const totalModal = $('#totalModal');
    let tipoPago = '';

    /* ========= RENDER ========= */
    function renderizarCarrito(items, total) {

        if (!items.length) {
            contenedor.html('<p class="text-center text-muted">Tu carrito está vacío</p>');
            totalModal.text('0.00');
            return;
        }

        let html = `
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Vendedor</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>`;

        items.forEach(item => {
            html += `
            <tr data-id="${item.id}">
                <td><strong>${item.producto.nombre}</strong></td>

                <td class="text-start">
                    <div class="d-flex align-items-center gap-2">
                        <img src="/storage/${item.vendedor.icono}"
                             width="32" height="32"
                             class="rounded-circle"
                             onerror="this.src='/img/default-user.png'">
                        ${item.vendedor.nombre}
                    </div>
                </td>

                <td>$${parseFloat(item.precio).toFixed(2)}</td>

                <td>
                    <div class="input-group input-group-sm justify-content-center">
                        <button class="btn btn-outline-secondary menos">−</button>
                        <input type="number" class="form-control cantidad text-center"
                               value="${item.cantidad}" min="1" style="max-width:60px">
                        <button class="btn btn-outline-secondary mas">+</button>
                    </div>
                </td>

                <td>$${parseFloat(item.subtotal).toFixed(2)}</td>

                <td>
                    <button class="btn btn-danger btn-sm eliminar">Eliminar</button>
                </td>
            </tr>`;
        });

        html += `</tbody></table>`;

        contenedor.html(html);
        totalModal.text(parseFloat(total).toFixed(2));
    }

    /* ========= AJAX ========= */
    function cargarCarrito() {
        $.get('/carrito/listar', res => {
            renderizarCarrito(res.items, res.total);
        });
    }

    function actualizarCantidad(id, cantidad) {
        if (cantidad < 1) return;

        $.post('/carrito/actualizar', {
            _token: '{{ csrf_token() }}',
            id,
            cantidad
        }, res => {
            renderizarCarrito(res.items, res.total);
        });
    }

    /* ========= EVENTOS ========= */
    contenedor.on('click', '.mas', function () {
        const fila = $(this).closest('tr');
        const input = fila.find('.cantidad');
        actualizarCantidad(fila.data('id'), parseInt(input.val()) + 1);
    });

    contenedor.on('click', '.menos', function () {
        const fila = $(this).closest('tr');
        const input = fila.find('.cantidad');
        const nueva = parseInt(input.val()) - 1;
        if (nueva >= 1) actualizarCantidad(fila.data('id'), nueva);
    });

    contenedor.on('click', '.eliminar', function () {
        const fila = $(this).closest('tr');
        const id = fila.data('id');

        $.ajax({
            url: `/carrito/item/${id}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: res => renderizarCarrito(res.items, res.total),
            error: () => alert('Error al eliminar el producto')
        });
    });

    $('#entregaDomicilio').click(() => {
        tipoPago = 'entrega';
        $('#avisoEntrega').removeClass('d-none');
        $('#formPrepago').addClass('d-none');
    });

    $('#pagoPrepagado').click(() => {
        tipoPago = 'prepago';
        $('#formPrepago').removeClass('d-none');
        $('#avisoEntrega').addClass('d-none');
    });

    $('#confirmarPago').click(() => {

        if (!tipoPago) {
            alert('Selecciona un método de pago');
            return;
        }

        if (!$('#direccion').val().trim()) {
            alert('Ingresa la dirección');
            return;
        }

        if (tipoPago === 'prepago') {
            if (!$('#metodoPrepago').val() || !$('#referenciaPago').val().trim()) {
                alert('Completa los datos de pago');
                return;
            }
        }

        alert('Pedido confirmado correctamente');
    });

    cargarCarrito();
});
</script>
@endsection
