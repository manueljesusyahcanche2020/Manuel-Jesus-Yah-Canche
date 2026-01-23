@extends('dashboard.welcome')

@section('contenido')
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

{{-- jQuery (asegúrate de tenerlo cargado antes del script) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {

    const contenedor = $('#contenedorComidas');

    // Función para renderizar las comidas
    function renderizarComidas(comidas) {
        contenedor.empty();

        if (comidas.length === 0) {
            contenedor.html('<p class="text-center text-muted">No se encontraron resultados.</p>');
            return;
        }

        $.each(comidas, function(index, c) {
            const card = `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="/storage/${c.imagen}" class="card-img-top" alt="${c.nombre}">
                        <div class="card-body">
                            <h5 class="card-title">${c.nombre}</h5>
                            <p class="card-text text-muted">${c.descripcion}</p>
                            <h6 class="text-success">$${parseFloat(c.precio).toFixed(2)}</h6>
                            <button class="btn btn-warning w-100 mt-2 agregar-carrito" data-id="${c.id}">Agregar al pedido</button>
                        </div>
                    </div>
                </div>
            `;
            contenedor.append(card);
        });
    }

    // Cargar todas las comidas (AJAX GET)
    function cargarComidas() {
        $.ajax({
            url: '/cliente/menu/listar',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                renderizarComidas(data);
            },
            error: function() {
                contenedor.html('<p class="text-center text-danger">Error al cargar el menú.</p>');
            }
        });
    }

    // Buscar comida en tiempo real
    $('#buscarComida').on('keyup', function() {
        const texto = $(this).val().trim();
        const url = texto.length > 0
            ? `/cliente/menu/buscar?buscar=${encodeURIComponent(texto)}`
            : `/cliente/menu/listar`;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                renderizarComidas(data);
            }
        });
    });

    // Función para agregar al carrito
    contenedor.on('click', '.agregar-carrito', function() {
        const menuId = $(this).data('id');

        $.ajax({
            url: '/carrito/agregar',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // CSRF de Laravel
                menu_id: menuId,
                cantidad: 1
            },
            success: function(res) {
                alert('Producto agregado al carrito');
                // Aquí puedes actualizar contador o modal si quieres
            },
            error: function(err) {
                alert('Error al agregar al carrito');
                console.log(err);
            }
        });
    });

    // Inicializar
    cargarComidas();
});
</script>
@endsection
