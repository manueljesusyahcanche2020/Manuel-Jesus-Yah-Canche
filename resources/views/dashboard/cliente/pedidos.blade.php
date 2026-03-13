@extends('dashboard.welcome')

@section('contenido')
<div class="container mt-3">
    {{-- Mensaje de Éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Mensaje de Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
<div class="container py-4">

    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
        <h4 class="fw-bold mb-0" style="color: #333;">
            <i class="fa-solid fa-box-open text-warning me-2"></i> Mis Compras
        </h4>
        <span class="badge bg-light text-dark border">{{ $pedidos->count() }} pedidos realizados</span>
    </div>

    @php
        $estadoColor = [
            'pendiente' => 'secondary',
            'enviado' => 'info',
            'entregado' => 'success',
            'cancelado' => 'danger',
        ];
    @endphp

    @forelse($pedidos as $pedido)
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        
        <!-- Header -->
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <span class="text-muted small text-uppercase fw-bold d-block">Pedido</span>
                    <span class="fw-bold text-dark">#{{ $pedido->id }}</span>
                </div>
                <div class="col-md-4 text-md-center">
                    <span class="text-muted small text-uppercase fw-bold d-block">Fecha</span>
                    <span class="text-dark">{{ $pedido->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="text-muted small text-uppercase fw-bold d-block">Total</span>
                    <span class="fs-5 fw-bold text-success">${{ number_format($pedido->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-4">
            <div class="row">
                <!-- Productos -->
                <div class="col-lg-7">
                    <h6 class="fw-bold text-uppercase small text-muted mb-3">Resumen de Productos</h6>
                    @foreach($pedido->pedidoItems as $item)
                    <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded-3 border-bottom-dashed">
                        <div class="flex-shrink-0">
                            <img src="{{ $item->menu?->imagen ? asset('storage/' . $item->menu->imagen) : asset('images/default-product.png') }}" 
                                 alt="Producto"
                                 class="img-thumbnail"
                                 style="width:70px; height:70px; object-fit:contain; background:#f9f9f9;">
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark mb-0">
                                {{ $item->menu?->nombre ?? 'Producto no disponible' }}
                            </div>
                            <div class="text-muted small">
                                Cantidad: <span class="fw-bold text-dark">{{ $item->cantidad }}</span> | 
                                Vendedor: <span class="text-primary">{{ $item->vendor?->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Botón cancelar pedido -->
                    @if(!in_array($pedido->estado, ['enviado','entregado','cancelado']))
                    <div class="mt-4">
                        <form action="{{ route('pedido.cancelar') }}" method="post" onsubmit="return confirm('¿Estás seguro de que deseas cancelar este pedido?');">
                            @csrf
                            <input type="hidden" name="id" value="{{ $pedido->id }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                                <i class="fa-solid fa-xmark me-1"></i> Cancelar Pedido
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <!-- Timeline / Seguimiento -->
                <div class="col-lg-5 border-start ps-lg-4">
                    <h6 class="fw-bold text-uppercase small text-muted mb-3">Estado del Seguimiento</h6>
                    @if($pedido->historial && $pedido->historial->count())
                    <div class="timeline mt-2">
                        @foreach($pedido->historial as $h)
                        <div class="timeline-item pb-3 position-relative">
                            <div class="timeline-dot bg-{{ $estadoColor[$h->estado] ?? 'secondary' }}"></div>
                            <div class="ps-3">
                                <span class="fw-bold d-block" style="font-size: 0.9rem;">{{ ucfirst($h->estado) }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                                @if($h->comentario)
                                <p class="mb-0 text-muted small mt-1 bg-light p-2 rounded fst-italic">"{{ $h->comentario }}"</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fa-solid fa-clock-rotate-left text-light mb-2 d-block" style="font-size: 2rem;"></i>
                        <p class="text-muted small">Pendiente de procesamiento</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    @empty
    <div class="text-center py-5">
        <i class="fa-solid fa-bag-shopping text-light mb-3" style="font-size: 4rem;"></i>
        <h5 class="text-muted">Aún no has realizado ninguna compra</h5>
        <a href="/" class="btn btn-warning mt-3 px-4 fw-bold">Ir a la tienda</a>
    </div>
    @endforelse

</div>

<style>
    .timeline {
        border-left: 2px solid #e9ecef;
        margin-left: 10px;
    }
    .timeline-item {
        position: relative;
        padding-left: 10px;
    }
    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        position: absolute;
        left: -7px;
        top: 4px;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    .border-bottom-dashed {
        border-bottom: 1px dashed #dee2e6;
    }
    .border-bottom-dashed:last-child {
        border-bottom: none;
    }
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endsection