@extends('dashboard.welcome')

@section('contenido')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-4">
    <h2 class="mb-3">🛒 Tus Carritos</h2>

    <div id="contenedorCarrito" class="table-responsive">
        <p class="text-center text-muted">Cargando carrito...</p>
    </div>

    <div class="mt-3 text-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalPago">
            Proceder al pedido
        </button>
    </div>
</div>

{{-- ================= MODAL DE PAGO ================= --}}
<div class="modal fade" id="modalPago" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Confirmar pedido</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
{{-- ================= DIRECCIÓN SELECT ================= --}}
<div class="mb-3">
    <label class="form-label">Selecciona una dirección</label>
    <select id="direccionSelect" class="form-select">
        <option value="">Cargando direcciones...</option>
    </select>
</div>

<!-- Contenedor para mostrar detalle de la dirección -->
<div id="detalleDireccion" class="mb-3">
    <p class="text-muted">Selecciona una dirección para ver el detalle</p>
</div>

<div class="alert alert-info">
<strong>Pago contra entrega</strong><br>
• El pago se realiza al recibir el pedido.<br>
• Ten el monto exacto disponible.<br>
• Tiempo estimado: 30–60 minutos.
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

{{-- ================= INVENTARIO CANVAS ================= --}}
<div id="canvasInventario" style="
position: fixed;
top: 10%;
left: 50%;
transform: translateX(-50%);
width: 400px;
height: 500px;
background: #fff;
border: 2px solid #333;
border-radius: 10px;
box-shadow: 0 0 15px rgba(0,0,0,0.5);
padding: 15px;
display: none;
overflow-y: auto;
z-index: 1050;
">
<h5 class="mb-3">🎒 Inventario Personal</h5>
<ul id="listaInventario" class="list-group">
    <li class="list-group-item">Espada de madera</li>
    <li class="list-group-item">Poción de salud x2</li>
    <li class="list-group-item">Monedas de oro x50</li>
</ul>
<p class="text-end mt-3"><small>Presiona "I" para cerrar</small></p>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const contenedor = $('#contenedorCarrito');
const totalModal = $('#totalModal');

/* ====== RENDERIZAR CARRITOS ====== */
function renderizarCarritos(carritos) {
    if (!carritos.length) {
        contenedor.html('<p class="text-center text-muted">Tu carrito está vacío</p>');
        totalModal.text('0.00');
        return;
    }

    let html = '';
    carritos.forEach(carrito => {
        html += `
        <div class="card mb-3" data-id="${carrito.id}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <input type="radio" name="carritoSeleccionado" class="carritoCheck" data-id="${carrito.id}" checked>
                    <img src="/storage/${carrito.vendedor.icono}" width="32" height="32" class="rounded-circle" onerror="this.src='/img/default-user.png'">
                    <strong>${carrito.vendedor.nombre}</strong>
                </div>
                <div>Total: $${parseFloat(carrito.total).toFixed(2)}</div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>`;
        carrito.items.forEach(item => {
            html += `
            <tr data-id="${item.id}">
                <td>${item.producto.nombre}</td>
                <td>$${parseFloat(item.precio).toFixed(2)}</td>
                <td>
                    <div class="input-group input-group-sm justify-content-center" style="width:100px; margin:auto;">
                        <button class="btn btn-outline-secondary btn-sm restarCantidad" data-id="${item.id}">-</button>
                        <input type="text" class="form-control text-center cantidadItem" value="${item.cantidad}" readonly style="width:40px;">
                        <button class="btn btn-outline-secondary btn-sm sumarCantidad" data-id="${item.id}">+</button>
                    </div>
                </td>
                <td class="subtotalItem">$${parseFloat(item.subtotal).toFixed(2)}</td>
            </tr>`;
        });
        html += `</tbody></table>
            </div>
        </div>`;
    });

    contenedor.html(html);

    // Calcular total general
    let totalGeneral = carritos.reduce((acc, c) => acc + parseFloat(c.total), 0);
    totalModal.text(totalGeneral.toFixed(2));
}

/* ====== CARGAR CARRITOS ====== */
function cargarCarrito() {
    $.get('/carrito/listar', res => {
        renderizarCarritos(res.carritos);
    });
}

/* ====== ACTUALIZAR CANTIDAD ====== */
function actualizarCantidad(item_id, cantidad) {
    $.post('/carrito/actualizar', {
        _token: '{{ csrf_token() }}',
        id: item_id,
        cantidad: cantidad
    }, function(res) {
        renderizarCarritos(res.carritos);
    });
}

/* ====== BOTONES + Y - ====== */
$(document).on('click', '.sumarCantidad', function() {
    const row = $(this).closest('tr');
    let cantidad = parseInt(row.find('.cantidadItem').val());
    cantidad++;
    const item_id = $(this).data('id');
    actualizarCantidad(item_id, cantidad);
});

$(document).on('click', '.restarCantidad', function() {
    const row = $(this).closest('tr');
    let cantidad = parseInt(row.find('.cantidadItem').val());
    if (cantidad > 1) cantidad--;
    const item_id = $(this).data('id');
    actualizarCantidad(item_id, cantidad);
});

/* ====== CARGAR DIRECCIONES ====== */
function cargarDirecciones() {
    const select = $('#direccionSelect');
    const detalleDiv = $('#detalleDireccion');

    select.html('<option value="">Cargando direcciones...</option>');
    detalleDiv.html('<p class="text-muted">Selecciona una dirección para ver el detalle</p>');

    $.get("{{ route('direccion.index') }}", function(data) {
        if(data.length === 0){
            select.html('<option value="">No tienes direcciones registradas</option>');
            return;
        }
        let options = '<option value="">Selecciona una dirección</option>';
        data.forEach(d => {
            options += `<option value="${d.id}">${d.nombre_direccion || 'Dirección'}</option>`;
        });
        select.html(options);

        select.off('change').on('change', function() {
            const selectedId = $(this).val();
            if(!selectedId) {
                detalleDiv.html('<p class="text-muted">Selecciona una dirección para ver el detalle</p>');
                return;
            }
            const direccion = data.find(d => d.id == selectedId);
            if(direccion){
                detalleDiv.html(`
                    <div class="border p-2 rounded bg-light">
                        <strong>${direccion.nombre_direccion || 'Dirección'}</strong><br>
                        ${direccion.calle}<br>
                        ${direccion.colonia}, ${direccion.ciudad}<br>
                        ${direccion.referencia ? `Ref: ${direccion.referencia}` : ''}
                    </div>
                `);
            }
        });
    });
}

/* ====== CONFIRMAR PEDIDO ====== */
$('#confirmarPago').click(() => {
    const direccion_id = $('#direccionSelect').val();
    if (!direccion_id) {
        alert('Por favor, selecciona una dirección');
        return;
    }

    const carrito_id = $('.carritoCheck:checked').data('id');
    if (!carrito_id) {
        alert('Selecciona un carrito para comprar');
        return;
    }

    $.post('{{ route("confirmar.compra") }}', {
        _token: '{{ csrf_token() }}',
        direccion_id: direccion_id,
        carrito_id: carrito_id
    })
    .done(res => {
        alert(res.message || '¡Pedido confirmado!');
        $('#modalPago').modal('hide');
        cargarCarrito();
    })
    .fail(err => {
        const errorMsg = err.responseJSON ? err.responseJSON.message : 'Error de conexión';
        alert('❌ ' + errorMsg);
    });
});

/* ====== INVENTARIO CANVAS ====== */
const canvas = $('#canvasInventario');
$(document).on('keydown', function(e){
    if(e.key.toLowerCase() === 'i'){
        canvas.toggle();
    }
});

/* ====== INICIO ====== */
cargarCarrito();
cargarDirecciones();
</script>
@endsection