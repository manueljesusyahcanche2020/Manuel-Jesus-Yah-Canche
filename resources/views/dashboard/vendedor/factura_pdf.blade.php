<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Factura #{{ $pedido->id }}</title>
<style>
    body { 
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
        background: #f0f2f5; 
        margin: 0; 
        padding: 20px; 
        color: #333; 
    }

    .invoice-card {
        max-width: 900px;
        margin: auto;
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    /* Encabezado */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #ff9900;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .invoice-title {
        font-size: 36px;
        color: #ff9900;
        font-weight: bold;
    }

    .invoice-info p {
        margin: 3px 0;
        font-size: 14px;
    }

    /* Secciones Cliente / Pago */
    .info-section {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .info-box {
        flex: 1;
        min-width: 280px;
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: #ff9900;
        margin-bottom: 10px;
        border-bottom: 2px solid #ff9900;
        padding-bottom: 5px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
    }

    /* Tabla de productos */
    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .products-table th, .products-table td {
        padding: 12px 15px;
        text-align: center;
    }

    .products-table th {
        background: #343a40;
        color: #fff;
        font-weight: bold;
    }

    .products-table td {
        background: #fff;
    }

    .products-table td.text-end {
        text-align: right;
    }

    /* Total */
    .totals {
        width: 40%;
        margin-left: auto;
        margin-bottom: 25px;
    }

    .totals td {
        padding: 10px;
        font-size: 16px;
    }

    .totals .grand-total {
        font-size: 20px;
        font-weight: bold;
        color: #198754;
    }

    /* Mensaje de agradecimiento */
    .thank-you {
        background: linear-gradient(90deg, #fff7e6, #fff3cd);
        border: 1px solid #ffeeba;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 30px;
        box-shadow: inset 0 2px 6px rgba(0,0,0,0.05);
    }

    .thank-you h2 {
        color: #856404;
        margin-bottom: 10px;
    }

    .thank-you p {
        color: #856404;
        font-size: 15px;
        margin: 5px 0;
    }

    .thank-you a {
        color: #856404;
        font-weight: bold;
        text-decoration: underline;
    }

    /* Footer */
    .footer {
        text-align: center;
        font-size: 12px;
        color: #777;
        margin-top: 30px;
    }

    /* Imprimir */
    @media print {
        body * { visibility: hidden; }
        .invoice-card, .invoice-card * { visibility: visible; }
        .invoice-card { position: absolute; top: 0; left: 0; width: 100%; }
    }
</style>
</head>
<body>

<div class="invoice-card">

    <!-- Encabezado -->
    <div class="header">
        <div class="invoice-title">FACTURA</div>
        <div class="invoice-info">
            <p><strong>Folio:</strong> #000{{ $pedido->id }}</p>
            <p><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Vendedor:</strong> {{ Auth::user()->name ?? '-' }}</p>
        </div>
    </div>

    <!-- Cliente y Pago -->
    <div class="info-section">
        <div class="info-box">
            <div class="section-title">CLIENTE</div>
            <p><strong>Nombre:</strong> {{ $pedido->user->name ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $pedido->user->email ?? '-' }}</p>
            <p><strong>Teléfono:</strong> {{ $pedido->user->telefono ?? '-' }}</p>
            <p><strong>Dirección:</strong> {{ $pedido->direccion->nombre_direccion ?? '-' }}, {{ $pedido->direccion->calle ?? '-' }}, {{ $pedido->direccion->colonia ?? '-' }}, {{ $pedido->direccion->ciudad ?? '-' }}, {{ $pedido->direccion->estado ?? '-' }} @if(!empty($pedido->direccion->referencia))(Ref: {{ $pedido->direccion->referencia }})@endif</p>
        </div>

        <div class="info-box">
            <div class="section-title">DETALLES DEL PAGO</div>
            <p><strong>Método:</strong> {{ $pedido->tipoPago->nombre ?? 'Efectivo' }}</p>
        </div>
    </div>

    <!-- Productos -->
    <div class="section-title">PRODUCTOS</div>
    <table class="products-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Cant.</th>
                <th>Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->pedidoItems as $item)
            <tr>
                <td>{{ $item->menu->nombre ?? 'Producto' }}</td>
                <td>{{ $item->menu->categoria->nombre ?? 'General' }}</td>
                <td>{{ $item->cantidad }}</td>
                <td class="text-end">${{ number_format($item->precio, 2) }}</td>
                <td class="text-end">${{ number_format($item->cantidad * $item->precio, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <table class="totals">
        <tr>
            <td class="grand-total">TOTAL:</td>
            <td class="text-end grand-total">${{ number_format($pedido->total, 2) }}</td>
        </tr>
    </table>

    <!-- Agradecimiento -->
    <div class="thank-you">
        <h2>¡Gracias por tu compra! 🎉</h2>
        <p>Nos alegra que hayas confiado en nosotros.</p>
        <p>Visita nuestro catálogo y descubre más productos increíbles.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Esta factura es solo una confirmación de tu pedido. Generado por PetoStory System.
    </div>

</div>

</body>
</html>