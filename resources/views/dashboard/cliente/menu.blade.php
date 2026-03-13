@extends('dashboard.welcome')

@section('contenido')
<style>
.img-producto {
    height: 200px;          /* altura uniforme */
    object-fit: contain;    /* no deforma */
    background: #f8f9fa;    /* fondo limpio */
    padding: 10px;
}

.card {
    border-radius: 12px;
}

.card-img-top {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}
</style>

<div class="container mt-4">

    <!-- Buscador -->
    <div class="mb-3">
        <input type="text" id="buscarComida" class="form-control" placeholder="Buscar comida...">
    </div>

    <!-- Contenedor de comidas -->
    <div class="row" id="contenedorComidas">
        <p class="text-center text-muted">Cargando menú...</p>
    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {

    const contenedor = $('#contenedorComidas');

    // Renderizar comidas
    function renderizarComidas(comidas) {
        contenedor.empty();

        if (!comidas.length) {
            contenedor.html('<p class="text-center text-muted">No se encontraron resultados.</p>');
            return;
        }

        $.each(comidas, function (index, c) {

            const imagenVendedor = c.vendedor?.imagen
                ? `/storage/${c.vendedor.imagen}`
                : `/img/default.png`;

            // Aviso si es categoría "Comida"
            let avisoComida = '';
            if (c.categoria && c.categoria.nombre === 'Comida') {
                avisoComida = `<p style="color:red; font-size:0.9rem;">
                    ⚠️ Este producto de tipo comida no se puede devolver una vez enviado.
                </p>`;
            }

            const card = `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">

                        <img src="/storage/${c.imagen}"
                             class="card-img-top img-producto"
                             alt="${c.nombre}">

                        <div class="card-body">

                            <h5 class="card-title">${c.nombre}</h5>
                            <p class="card-text text-muted">${c.descripcion ?? ''}</p>

                            <!-- Aviso Comida -->
                            ${avisoComida}

                            <!-- Vendedor -->
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="${imagenVendedor}"
                                     width="32"
                                     height="32"
                                     class="rounded-circle border">

                                <div>
                                    <small class="text-muted">
                                        Vendedor: ${c.vendedor?.name ?? 'Desconocido'}
                                    </small><br>

                                    <small class="text-danger">
                                        Pedido mínimo: $${parseFloat(c.vendedor?.minimo_pedido ?? 0).toFixed(2)}
                                    </small>
                                </div>
                            </div>

                            <h6 class="text-success">
                                $${parseFloat(c.precio).toFixed(2)}
                            </h6>

                            <button class="btn btn-warning w-100 mt-2 agregar-carrito"
                                    data-id="${c.id}">
                                Agregar al pedido
                            </button>

                        </div>
                    </div>
                </div>
            `;

            contenedor.append(card);
        });
    }

    // Cargar menú
    function cargarComidas() {
        $.ajax({
            url: '/cliente/menu/listar',
            type: 'GET',
            dataType: 'json',
            success: renderizarComidas,
            error: () => {
                contenedor.html('<p class="text-center text-danger">Error al cargar el menú.</p>');
            }
        });
    }

    // Buscar en tiempo real
    $('#buscarComida').on('keyup', function () {
        const texto = $(this).val().trim();

        const url = texto.length
            ? `/cliente/menu/buscar?buscar=${encodeURIComponent(texto)}`
            : `/cliente/menu/listar`;

        $.getJSON(url, renderizarComidas);
    });

    // Agregar al carrito
    contenedor.on('click', '.agregar-carrito', function () {
        const id = $(this).data('id');

        $.get(`/carrito/agregar/${id}`)
            .done(() => {
                alert('Producto agregado al carrito');
            })
            .fail(err => {
                alert('Error al agregar al carrito');
                console.error(err);
            });
    });

    // Inicializar
    cargarComidas();
});
</script>
@endsection