<?php


namespace App\Http\Controllers\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MenuComidaModel;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
class InventarioController extends Controller
{

    public function index()
    {
        $productos = MenuComidaModel::where('user_id', Auth::id())->get();

        $bajo_stock = MenuComidaModel::where('user_id', Auth::id())
            ->where('stock','<=',5)
            ->get();

        return view('dashboard.vendedor.productos.inventario', compact('productos','bajo_stock'));
    }

    public function actualizar(Request $request)
    {
        DB::statement(
            'CALL sp_entrada_inventario(?,?,?)',
            [
                $request->id,          // producto_id
                $request->cantidad,    // cantidad que entra
                'Ajuste de inventario' // descripción
            ]
        );

        return back()->with('success','Entrada de inventario registrada correctamente');
    }
    public function historial($id)
    {
        $producto = DB::table('menu_comidas')
            ->where('id',$id)
            ->first();

        $historial = DB::table('vw_historial_inventario')
            ->where('producto_id',$id)
            ->orderBy('created_at','desc')
            ->get();

        return view('dashboard.vendedor.productos.historial',compact('producto','historial'));
    }
}