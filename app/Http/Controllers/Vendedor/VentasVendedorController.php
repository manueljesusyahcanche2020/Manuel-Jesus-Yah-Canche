<?php
namespace App\Http\Controllers\vendedor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PedidoItem;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Models\MenuComidaModel;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class VentasVendedorController extends Controller
{
    public function VerVentas()
    {
        // Solo ventas completadas del vendedor
        $ventas = PedidoItem::with(['pedido.user', 'menu.categoria'])
            ->whereHas('pedido', function ($query) {
                $query->where('estado_pago', 'pagado')
                    ->where('estado', 'entregado');
            })
            ->where('vendor_id', Auth::id()) // solo los items de este vendedor
            ->orderBy('created_at', 'desc')
            ->get();

        // Ventas por Mes
        $ventasPorMes = $ventas->groupBy(fn($item) => $item->created_at->format('m/Y'))
                            ->map(fn($items) => $items->sum('subtotal'));

        // Productos más vendidos (top 5)
        $productosTop = $ventas->groupBy(fn($item) => $item->menu->nombre ?? 'Producto eliminado')
                            ->map(fn($items) => $items->sum('cantidad'))
                            ->sortDesc()
                            ->take(5);

        // Ventas por categoría
        $ventasPorCategoria = $ventas->groupBy(fn($item) => $item->menu->categoria->nombre ?? 'Sin categoría')
                                    ->map(fn($items) => $items->sum('subtotal'));

        // Clientes top
        $clientesTop = $ventas->groupBy(fn($item) => $item->pedido->user->name ?? 'Usuario eliminado')
                            ->map(fn($items) => $items->sum('subtotal'))
                            ->sortDesc()
                            ->take(5);

        return view('dashboard.vendedor.ventas', compact(
            'ventas',
            'ventasPorMes',
            'productosTop',
            'ventasPorCategoria',
            'clientesTop'
        ));
    }
    public function pedidos()
    {
        $items = DB::table('vista_pedidos_vendedor')
            ->where('vendedor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Agrupar por pedido
        $pedidos = $items->groupBy('pedido_id');

        return view('dashboard.vendedor.pedidos', compact('pedidos'));
    }
    public function generarFactura($pedidoId)
    {
        $pedido = Pedido::with([
            'user',
            'direccion',                // <--- agregamos la dirección aquí
            'pedidoItems.menu.categoria',
            'historial'
        ])->findOrFail($pedidoId);

    return view('dashboard.vendedor.factura', compact('pedido'));}

    public function generarFacturaPDF($pedidoId)
    {
        $pedido = Pedido::with(['user', 'pedidoItems.menu.categoria'])->findOrFail($pedidoId);
        
        // Apunta a la nueva vista simplificada
        $pdf = Pdf::loadView('dashboard.vendedor.factura_pdf', compact('pedido'));
        
        return $pdf->stream("factura_{$pedido->id}.pdf");
    }

    public function cambiarEstado(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'estado' => 'required|in:pendiente,enviado,entregado,cancelado'
        ]);

        $pedidoId = $request->pedido_id;
        $nuevoEstado = $request->estado;

        // Llamar a la función SQL
        $resultado = \DB::select("SELECT sp_cambiar_estado(?, ?) AS resultado", [$pedidoId, $nuevoEstado]);

        $mensaje = $resultado[0]->resultado ?? 'Error desconocido';

        if ($mensaje !== 'OK') {
            // Si la función devuelve mensaje distinto a OK, mostrar error
            return back()->with('error', $mensaje);
        }

        return back()->with('success', 'Estado actualizado correctamente');
    }
}

