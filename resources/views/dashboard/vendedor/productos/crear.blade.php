@extends('dashboard.welcome')

@section('contenido')

<div class="container mt-4">
    <div class="row">

        <!-- PERFIL + PRODUCTOS -->
        <div class="col-md-6 order-md-2">

            <!-- PERFIL -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <img
                        src="{{ Auth::user()->imagen ? asset('storage/' . Auth::user()->imagen) : asset('img/user-default.png') }}"
                        class="rounded-circle"
                        width="70"
                        height="70"
                        style="object-fit:cover"
                    >
                    <div>
                        <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                        <small class="text-muted">{{ Auth::user()->role->nombre ?? 'Sin rol' }}</small>
                    </div>
                </div>
            </div>

            <!-- PRODUCTOS -->
            <div class="card shadow-sm">
                <div class="card-body" id="productos-list">
                    <small>Cargando productos...</small>
                </div>
            </div>

        </div>

        <!-- BOTÓN AGREGAR -->
        <div class="col-md-6 order-md-1">
            @if(Auth::user()->role->nombre === 'Vendedor')
            <div class="d-flex justify-content-center mb-3">
                <button class="btn btn-warning w-75" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">
                    Agregar Producto
                </button>
            </div>
            @endif
        </div>

    </div>
</div>

<!-- ================= MODAL AGREGAR ================= -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-agregar-producto" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input class="form-control mb-2" name="nombre" placeholder="Nombre" required>
                    <textarea class="form-control mb-2" name="descripcion" placeholder="Descripción"></textarea>

                    <select class="form-control mb-2" name="categoria_id" id="categoria_id" required>
                        <option value="">Cargando categorías...</option>
                    </select>

                    <input class="form-control mb-2" type="number" step="0.01" name="precio" placeholder="Precio" required>
                    <input class="form-control mb-2" type="file" name="imagen" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-warning">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDITAR ================= -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-editar-producto" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit-id">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input class="form-control mb-2" id="edit-nombre" name="nombre" required>
                    <textarea class="form-control mb-2" id="edit-descripcion" name="descripcion"></textarea>

                    <select class="form-control mb-2" id="edit-categoria_id" name="categoria_id" required>
                        <option value="">Cargando categorías...</option>
                    </select>

                    <input class="form-control mb-2" type="number" step="0.01" id="edit-precio" name="precio" required>
                    <input class="form-control mb-2" type="file" name="imagen">
                    <small class="text-muted">Opcional</small>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= JS ================= -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {

    /* ================= CATEGORÍAS ================= */
    function cargarCategorias(selectId, selected = null) {
        $.get("{{ route('ajax.categorias') }}", function (categorias) {
            let options = '<option value="">Seleccione una categoría</option>';

            categorias.forEach(cat => {
                options += `
                    <option value="${cat.id}" ${selected == cat.id ? 'selected' : ''}>
                        ${cat.nombre}
                    </option>
                `;
            });

            $(selectId).html(options);
        });
    }

    $('#modalAgregarProducto').on('shown.bs.modal', function () {
        cargarCategorias('#categoria_id');
    });

    /* ================= MENÚS ================= */
    function cargarMenus() {
        $.get("{{ route('vendedor.menu.ajax') }}", function (data) {
            let html = '<ul class="list-group">';

            if (!data.length) {
                html += '<li class="list-group-item text-muted">No hay productos</li>';
            } else {
                data.forEach(menu => {
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                ${menu.imagen ? `<img src="/storage/${menu.imagen}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">` : ''}
                                <span>${menu.nombre} - $${menu.precio}</span>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary btn-editar"
                                    data-id="${menu.id}"
                                    data-nombre="${menu.nombre}"
                                    data-descripcion="${menu.descripcion ?? ''}"
                                    data-categoria_id="${menu.categoria_id}"
                                    data-precio="${menu.precio}">✏️</button>
                                <button class="btn btn-sm btn-danger btn-eliminar"
                                    data-id="${menu.id}">🗑️</button>
                            </div>
                        </li>`;
                });
            }

            html += '</ul>';
            $('#productos-list').html(html);
        });
    }

    cargarMenus();

    /* ================= AGREGAR ================= */
    $('#form-agregar-producto').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('vendedor.productos.store') }}",
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success() {
                $('#modalAgregarProducto').modal('hide');
                $('#form-agregar-producto')[0].reset();
                cargarMenus();
            }
        });
    });

    /* ================= EDITAR ================= */
    $(document).on('click','.btn-editar',function(){
        let categoriaId = $(this).data('categoria_id');

        $('#edit-id').val($(this).data('id'));
        $('#edit-nombre').val($(this).data('nombre'));
        $('#edit-descripcion').val($(this).data('descripcion'));
        $('#edit-precio').val($(this).data('precio'));

        cargarCategorias('#edit-categoria_id', categoriaId);

        $('#modalEditarProducto').modal('show');
    });

    $('#form-editar-producto').submit(function(e){
        e.preventDefault();
        let id = $('#edit-id').val();

        $.ajax({
            url: `/vendedor/productos/${id}`,
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success() {
                $('#modalEditarProducto').modal('hide');
                cargarMenus();
            }
        });
    });

    /* ================= ELIMINAR ================= */
    $(document).on('click','.btn-eliminar',function(){
        if(!confirm('¿Eliminar producto?')) return;

        $.ajax({
            url: `/vendedor/productos/${$(this).data('id')}`,
            method: 'DELETE',
            data: {_token:'{{ csrf_token() }}'},
            success(){ cargarMenus(); }
        });
    });

});
</script>

@endsection
