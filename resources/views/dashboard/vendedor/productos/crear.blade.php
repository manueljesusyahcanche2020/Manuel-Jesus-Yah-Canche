@extends('dashboard.welcome')

@section('contenido')

{{-- ================= MENSAJES DEL SISTEMA ================= --}}

{{-- 🟢 Éxito --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <strong>✔ Éxito:</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- 🔴 Error del procedure --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong>✖ Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- 🟡 Errores de validación Laravel --}}
@if ($errors->any())
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <strong>⚠ Atención:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<style>
    /* Scroll solo en desktop */
@media (min-width: 992px) {
    .productos-scroll {
        height: calc(100vh - 140px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 12px;
    }
}

/* En móvil no usamos scroll interno */
@media (max-width: 991px) {
    .productos-scroll {
        height: auto;
        overflow: visible;
    }
}
</style>
<div class="container-fluid mt-4">
    <div class="row">

        <!-- PERFIL -->
        <div class="col-12 col-md-4 col-lg-3 mb-4">
            <div class="card shadow-sm h-100 position-lg-sticky" style="top:20px;">
                <div class="card-body text-center">

                    <img
                        src="{{ Auth::user()->imagen ? asset('storage/' . Auth::user()->imagen) : asset('img/user-default.png') }}"
                        class="rounded-circle mb-3 border border-3 border-warning"
                        width="110"
                        height="110"
                        style="object-fit:cover"
                    >

                    <h5 class="fw-bold mb-0">{{ Auth::user()->name }}</h5>
                    <small class="text-muted d-block mb-3">
                        {{ Auth::user()->role->nombre ?? 'Sin rol' }}
                    </small>

                    @if(Auth::user()->role->nombre === 'Vendedor')
                        <button class="btn btn-warning w-100 fw-bold"
                                data-bs-toggle="modal"
                                data-bs-target="#modalAgregarProducto">
                            ➕ Agregar Producto
                        </button>
                    @endif

                </div>
            </div>
        </div>

        <!-- PRODUCTOS -->
        <div class="col-12 col-md-8 col-lg-9">

            <!-- Scroll solo en pantallas grandes -->
            <div class="productos-scroll">

                <div class="row g-4">
                    @forelse($productos as $producto)

                        <div class="col-12 col-sm-6 col-xl-4">
                            <div class="card h-100 shadow-sm border-0">

                                @if($producto->imagen)
                                    <img src="{{ asset('storage/'.$producto->imagen) }}"
                                         class="card-img-top"
                                         style="height:200px;object-fit:cover;">
                                @endif

                                <div class="card-body d-flex flex-column">

                                    <h5 class="fw-bold">{{ $producto->nombre }}</h5>

                                    <p class="small text-muted">
                                        {{ $producto->descripcion }}
                                    </p>

                                    <p class="fw-bold text-warning fs-5">
                                        ${{ number_format($producto->precio,2) }}
                                    </p>

                                    <p class="small">
                                        @if($producto->sin_stock)
                                            <span class="badge bg-danger">
                                                Hasta agotar existencia
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Stock: {{ $producto->stock }}
                                            </span>
                                        @endif
                                    </p>

                                    <div class="mt-auto d-flex justify-content-between">

                                        <!-- EDITAR -->
                                        <button class="btn btn-sm btn-outline-primary btn-editar"
                                            data-id="{{ $producto->id }}"
                                            data-nombre="{{ $producto->nombre }}"
                                            data-descripcion="{{ $producto->descripcion }}"
                                            data-precio="{{ $producto->precio }}"
                                            data-categoria="{{ $producto->categoria_id }}"
                                            data-stock="{{ $producto->stock }}"
                                            data-sin_stock="{{ $producto->sin_stock }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditarProducto">
                                            ✏️ Editar
                                        </button>

                                        <!-- ELIMINAR -->
                                        <form method="POST"
                                            action="{{ route('vendedor.productos.destroy', $producto->id) }}"
                                            onsubmit="return confirm('¿Eliminar producto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                🗑️
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="col-12">
                            <div class="alert alert-secondary text-center">
                                No hay productos registrados.
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

    </div>
</div>

<!-- ================= MODAL AGREGAR ================= -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST"
                  action="{{ route('vendedor.productos.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Agregar Producto</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input class="form-control mb-2" name="nombre" placeholder="Nombre" required>

                    <textarea class="form-control mb-2" name="descripcion" placeholder="Descripción"></textarea>

                    <select class="form-control mb-2" name="categoria_id" required>
                        <option value="">Seleccione categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <input class="form-control mb-2" type="number" step="0.01" name="precio" placeholder="Precio" required>

                    <!-- STOCK -->
                    <input class="form-control mb-2" type="number" name="stock" placeholder="Cantidad en stock">

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="sin_stock" value="1" id="sinStockCheck">
                        <label class="form-check-label" for="sinStockCheck">
                            No hay stock (hasta agotar existencia)
                        </label>
                    </div>

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

            <form method="POST" id="formEditarProducto" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input class="form-control mb-2" id="edit-nombre" name="nombre" required>
                    <textarea class="form-control mb-2" id="edit-descripcion" name="descripcion"></textarea>

                    <select class="form-control mb-2" id="edit-categoria" name="categoria_id" required>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <input class="form-control mb-2" type="number" step="0.01" id="edit-precio" name="precio" required>

                    <!-- STOCK -->
                    <input class="form-control mb-2" type="number" id="edit-stock" name="stock">

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit-sin-stock" name="sin_stock" value="1">
                        <label class="form-check-label">
                            No hay stock (hasta agotar existencia)
                        </label>
                    </div>

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

<script>
document.addEventListener("DOMContentLoaded", function() {

    document.querySelectorAll('.btn-editar').forEach(button => {

        button.addEventListener('click', function() {

            let id = this.dataset.id;

            document.getElementById('edit-nombre').value = this.dataset.nombre;
            document.getElementById('edit-descripcion').value = this.dataset.descripcion;
            document.getElementById('edit-precio').value = this.dataset.precio;
            document.getElementById('edit-categoria').value = this.dataset.categoria;
            document.getElementById('edit-stock').value = this.dataset.stock;

            document.getElementById('edit-sin-stock').checked =
                this.dataset.sin_stock == 1 ? true : false;

            document.getElementById('formEditarProducto')
                .action = "/vendedor/productos/" + id;

        });

    });

});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    /* ================= AGREGAR ================= */
    const stockInput = document.querySelector('#modalAgregarProducto input[name="stock"]');
    const sinStockCheck = document.getElementById('sinStockCheck');

    if (stockInput && sinStockCheck) {

        stockInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                sinStockCheck.checked = false;
                sinStockCheck.setAttribute('disabled', true);
            } else {
                sinStockCheck.removeAttribute('disabled');
            }
        });

        sinStockCheck.addEventListener('change', function() {
            if (this.checked) {
                stockInput.value = '';
                stockInput.setAttribute('disabled', true);
            } else {
                stockInput.removeAttribute('disabled');
            }
        });
    }

    /* ================= EDITAR ================= */
    const editStockInput = document.getElementById('edit-stock');
    const editSinStockCheck = document.getElementById('edit-sin-stock');

    if (editStockInput && editSinStockCheck) {

        editStockInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                editSinStockCheck.checked = false;
                editSinStockCheck.setAttribute('disabled', true);
            } else {
                editSinStockCheck.removeAttribute('disabled');
            }
        });

        editSinStockCheck.addEventListener('change', function() {
            if (this.checked) {
                editStockInput.value = '';
                editStockInput.setAttribute('disabled', true);
            } else {
                editStockInput.removeAttribute('disabled');
            }
        });
    }

});
</script>

@endsection