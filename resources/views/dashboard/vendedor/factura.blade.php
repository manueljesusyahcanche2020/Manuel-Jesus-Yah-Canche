@extends('dashboard.welcome')

@section('contenido')
<div class="container mt-5 mb-5">
    <div class="card shadow border-0 rounded-4 p-5" id="factura">

        {{-- ================= ENCABEZADO ================= --}}
        <div class="row mb-4">
            <div class="col-6">
                <h2 class="fw-bold" style="color:#ff9900;">
                    <i class="fa-solid fa-file-invoice"></i> Factura
                </h2>
                <p><strong>Pedido #:</strong> {{ $pedido->id }}</p>
                <p><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y') }}</p>
            </div>

            <div class="col-6 text-end">
                <h6 class="fw-bold">Vendedor</h6>
                <p>{{ Auth::user()->name }}</p>

                <h6 class="fw-bold mt-3">Cliente</h6>
                <p><strong>Nombre:</strong> {{ $pedido->user->name ?? 'Usuario eliminado' }}</p>
                <p><strong>Email:</strong> {{ $pedido->user->email ?? '-' }}</p>
                <p><strong>Teléfono:</strong> {{ $pedido->user->telefono ?? '-' }}</p>
            </div>
        </div>

        <hr>

        {{-- ================= INFORMACIÓN DE PEDIDO ================= --}}
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="fw-bold mb-3" style="color:#ff9900;">
                    <i class="fa-solid fa-truck"></i> Información del Pedido
                </h5>

                <div class="row">
                    {{-- Dirección --}}
                    <div class="col-md-6">
                        <p><strong>Dirección:</strong></p>
                        @if($pedido->direccion)
                            <p>
                                {{ $pedido->direccion->nombre_direccion ?? 'Dirección' }}<br>
                                {{ $pedido->direccion->calle ?? '-' }}<br>
                                {{ $pedido->direccion->colonia ?? '-' }}, {{ $pedido->direccion->ciudad ?? '-' }}<br>
                                @if($pedido->direccion->referencia)
                                    Ref: {{ $pedido->direccion->referencia }}
                                @endif
                            </p>
                        @else
                            <p>-</p>
                        @endif
                    </div>

                    {{-- Tipo de Pago y Estados --}}
                    <div class="col-md-6">
                        <p><strong>Tipo de Pago:</strong> {{ $pedido->tipo_pago->nombre ?? '-' }}</p>
                        <p><strong>Estado de Pago:</strong> {{ ucfirst($pedido->estado_pago ?? 'pendiente') }}</p>
                        <p>
                            <strong>Estado del Pedido:</strong>
                            <span class="badge 
                                @if($pedido->estado == 'entregado') bg-success
                                @elseif($pedido->estado == 'pendiente') bg-warning
                                @elseif($pedido->estado == 'cancelado') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($pedido->estado ?? 'pendiente') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= TABLA DE PRODUCTOS ================= --}}
        <div class="table-responsive mb-4">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio Unitario</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($pedido->pedidoItems as $item)
                        @php
                            $producto = $item->menu;
                            $categoria = $producto->categoria ?? null;
                            $subtotal = $item->cantidad * $item->precio;
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $producto->nombre ?? 'Producto eliminado' }}</td>
                            <td>{{ $categoria->nombre ?? '-' }}</td>
                            <td class="text-center">{{ $item->cantidad }}</td>
                            <td class="text-end">${{ number_format($item->precio, 2) }}</td>
                            <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th class="text-end text-success fs-5">${{ number_format($total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- ================= BOTÓN IMPRIMIR / PDF ================= --}}
        <div class="text-end">
            <button class="btn btn-success px-4" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
            <a href="{{ route('vendedor.factura.pdf', $pedido->id) }}" 
               class="btn btn-outline-secondary btn-sm rounded-pill px-3" 
               target="_blank" 
               title="Descargar / Abrir PDF">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF
            </a>
        </div>

    </div>
</div>

{{-- ================= ESTILOS DE IMPRESIÓN ================= --}}
<style>
@media print {
    body * { visibility: hidden; }
    #factura, #factura * { visibility: visible; }
    #factura { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
@endsection