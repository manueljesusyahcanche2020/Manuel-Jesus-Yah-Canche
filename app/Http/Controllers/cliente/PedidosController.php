<?php
namespace App\Http\Controllers\cliente;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoItem;

class PedidosController extends Controller
{
    public function verPedidos()
    {
        $pedidos = Pedido::with([
                'pedidoItems.menu',
                'user',
                'historial' // 👈 agregamos historial
            ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.cliente.pedidos', compact('pedidos'));
    }
    public function cancelar(Request $request)
    {
        // Llamamos al procedimiento que creamos
        $resultado = DB::select('CALL sp_cancelar_pedido(?, ?)', [
            $request->id,
            auth()->id()
        ]);

        $respuesta = $resultado[0];

        // Si el procedimiento devolvió 'SUCCESS', mandamos sesión 'success'
        if ($respuesta->status == 'SUCCESS') {
            return back()->with('success', $respuesta->mensaje);
        } 
        
        // De lo contrario, mandamos sesión 'error'
        return back()->with('error', $respuesta->mensaje);
    }
}
